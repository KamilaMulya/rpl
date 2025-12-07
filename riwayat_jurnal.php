<?php
ob_start(); // Memulai output buffering
session_start(); // Memulai sesi PHP

include 'koneksi.php'; // Sertakan file koneksi database

// --- AUTHENTIKASI DAN OTORISASI ---
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'guru') {
    header("Location: login.php"); // Alihkan ke halaman login jika bukan guru
    exit;
}
$id_user = $_SESSION['user']['id_user']; // Ambil ID pengguna dari sesi

// --- HANDLER REQUEST AJAX (GET, UPDATE, DELETE) ---
// Header Content-Type diatur untuk JSON hanya jika ini adalah permintaan AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    // Mengambil data jurnal untuk diedit (action: 'get')
    if ($_POST['action'] == 'get') {
        $id_jurnal_guru = intval($_POST['id']);
        $stmt = $koneksi->prepare("SELECT id_jurnal_guru, materi, tanggal, catatan FROM jurnal_guru WHERE id_jurnal_guru = ? AND id_user = ?");
        $stmt->bind_param("ii", $id_jurnal_guru, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['status' => 'success', 'data' => $row]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Jurnal tidak ditemukan atau Anda tidak memiliki akses.']);
        }
        $stmt->close();
        exit;
    }

    // Memperbarui data jurnal (action: 'update')
    if ($_POST['action'] == 'update') {
        $id_jurnal_guru = intval($_POST['id_jurnal_guru']);
        $materi = trim($_POST['materi'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $catatan = trim($_POST['catatan'] ?? ''); // Ambil catatan juga

        if ($id_jurnal_guru && $materi && $tanggal) {
            $stmt = $koneksi->prepare("UPDATE jurnal_guru SET materi = ?, tanggal = ?, catatan = ? WHERE id_jurnal_guru = ? AND id_user = ?");
            $stmt->bind_param("sssii", $materi, $tanggal, $catatan, $id_jurnal_guru, $id_user);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Jurnal berhasil diperbarui.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui jurnal: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data yang dikirim tidak lengkap.']);
        }
        exit;
    }

    // Menghapus data jurnal (action: 'delete')
    if ($_POST['action'] == 'delete') {
        $id_jurnal_guru = intval($_POST['id']);

        // Pastikan jurnal belum divalidasi sebelum dihapus
        $check_stmt = $koneksi->prepare("SELECT status_validasi FROM jurnal_guru WHERE id_jurnal_guru = ? AND id_user = ?");
        $check_stmt->bind_param("ii", $id_jurnal_guru, $id_user);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $jurnal_data = $check_result->fetch_assoc();
        $check_stmt->close();

        if ($jurnal_data && $jurnal_data['status_validasi'] == '1') { // Hanya izinkan hapus jika status_validasi == '1' (belum divalidasi)
            // Mulai transaksi untuk memastikan konsistensi data
            $koneksi->begin_transaction();
            try {
                // Hapus absensi siswa yang terkait dengan jurnal ini terlebih dahulu
                $stmt_absensi = $koneksi->prepare("DELETE FROM absensi WHERE id_jurnal = ?");
                $stmt_absensi->bind_param("i", $id_jurnal_guru);
                $stmt_absensi->execute();
                $stmt_absensi->close();

                // Dapatkan path dokumentasi untuk dihapus dari server
                $get_doc_path = $koneksi->prepare("SELECT dokumentasi_path FROM jurnal_guru WHERE id_jurnal_guru = ?");
                $get_doc_path->bind_param("i", $id_jurnal_guru);
                $get_doc_path->execute();
                $doc_result = $get_doc_path->get_result();
                $doc_row = $doc_result->fetch_assoc();
                $get_doc_path->close();

                // Hapus jurnal guru
                $stmt_jurnal = $koneksi->prepare("DELETE FROM jurnal_guru WHERE id_jurnal_guru = ? AND id_user = ?");
                $stmt_jurnal->bind_param("ii", $id_jurnal_guru, $id_user);
                $stmt_jurnal->execute();
                $stmt_jurnal->close();

                // Hapus file dokumentasi dari server jika ada
                if ($doc_row && !empty($doc_row['dokumentasi_path'])) {
                    $file_path = 'uploads/dokumentasi/' . $doc_row['dokumentasi_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path); // Hapus file
                    }
                }

                $koneksi->commit(); // Commit transaksi
                echo json_encode(['status' => 'success', 'message' => 'Jurnal dan absensi terkait berhasil dihapus.']);
            } catch (Exception $e) {
                $koneksi->rollback(); // Rollback jika ada kesalahan
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jurnal: ' . $e->getMessage()]);
            }
        } elseif (!$jurnal_data) {
            echo json_encode(['status' => 'error', 'message' => 'Jurnal tidak ditemukan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Jurnal tidak dapat dihapus karena sudah divalidasi.']);
        }
        exit;
    }

    // Mengambil data absensi untuk diedit (action: 'get_absensi')
    if ($_POST['action'] == 'get_absensi') {
        $id_jurnal = intval($_POST['id_jurnal']);
        $id_kelas = intval($_POST['id_kelas']);
        $data = [];
        // Ambil semua siswa di kelas
        $sqlSiswa = $koneksi->prepare("SELECT id_siswa, nama_siswa, jenis_kelamin FROM siswa WHERE id_kelas=? ORDER BY nama_siswa");
        $sqlSiswa->bind_param("i", $id_kelas);
        $sqlSiswa->execute();
        $resSiswa = $sqlSiswa->get_result();
        while($s = $resSiswa->fetch_assoc()) {
            $data[$s['id_siswa']] = [
                'id_siswa' => $s['id_siswa'],
                'nama' => $s['nama_siswa'],
                'jk' => $s['jenis_kelamin'],
                'status' => 'hadir'
            ];
        }
        $sqlSiswa->close();
        // Ambil absensi siswa
        $sqlAbs = $koneksi->prepare("SELECT id_siswa, status_kehadiran FROM absensi WHERE id_jurnal=?");
        $sqlAbs->bind_param("i", $id_jurnal);
        $sqlAbs->execute();
        $resAbs = $sqlAbs->get_result();
        while($abs = $resAbs->fetch_assoc()) {
            if (isset($data[$abs['id_siswa']])) {
                $data[$abs['id_siswa']]['status'] = $abs['status_kehadiran'];
            }
        }
        $sqlAbs->close();
        echo json_encode(['status'=>'success','data'=>array_values($data)]);
        exit;
    }

    // Memperbarui data absensi siswa (action: 'update_absensi')
    if ($_POST['action'] == 'update_absensi') {
        $id_jurnal = intval($_POST['id_jurnal_guru']);
        $absensi = $_POST['absensi'] ?? [];
        // Hapus absensi lama
        $del = $koneksi->prepare("DELETE FROM absensi WHERE id_jurnal=?");
        $del->bind_param("i", $id_jurnal);
        $del->execute();
        $del->close();
        // Insert absensi baru
        $stmt = $koneksi->prepare("INSERT INTO absensi (id_jurnal, id_siswa, status_kehadiran) VALUES (?, ?, ?)");
        foreach($absensi as $id_siswa=>$status){
            $id_siswa = intval($id_siswa);
            $status = trim($status);
            $stmt->bind_param("iis", $id_jurnal, $id_siswa, $status);
            $stmt->execute();
        }
        $stmt->close();
        echo json_encode(['status'=>'success']);
        exit;
    }
}

// --- AMBIL DATA JURNAL UNTUK TAMPILAN TABEL ---
$query_jurnal = "
    SELECT
        jg.*,
        jm.id_kelas,
        mp.nama_mata_pelajaran,
        k.nama_kelas,
        jp.jam_mulai,
        jp.jam_selesai
    FROM jurnal_guru jg
    INNER JOIN jadwal_mengajar jm ON jg.id_jadwal_mengajar = jm.id_jadwal_mengajar
    INNER JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
    INNER JOIN kelas k ON jm.id_kelas = k.id_kelas
    INNER JOIN jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
    WHERE jg.id_user = ?
    ORDER BY jg.tanggal DESC, jg.id_jurnal_guru DESC
";
$stmt_jurnal = $koneksi->prepare($query_jurnal);
$stmt_jurnal->bind_param("i", $id_user);
$stmt_jurnal->execute();
$result_jurnal = $stmt_jurnal->get_result();
$stmt_jurnal->close(); // Tutup statement setelah mengambil hasil

// --- HTML UNTUK HALAMAN RIWAYAT JURNAL ---
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-custom p-4 shadow">
                <h2 class="mb-4 fw-bold text-center" style="color:#26364d;">Riwayat Jurnal</h2>
                <div class="card-custom shadow-custom">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 jurnal-table">
                            <thead class="align-middle text-center sticky-top" style="background:#f8fafc; z-index:2;">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jadwal</th>
                                    <th>Materi</th>
                                    <th>Sakit</th>
                                    <th>Izin</th>
                                    <th>Alpa</th>
                                    <th>Catatan</th>
                                    <th>Dokumentasi</th>
                                    <th>Status</th>
                                    <th>Absensi Siswa</th>
                                    <th>Catatan Validator</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($result_jurnal->num_rows > 0): ?>
                                <?php while($row = $result_jurnal->fetch_assoc()): ?>
                                    <?php
                                    // --- GET ALL SISWA DI KELAS INI UNTUK MODAL DETAIL ABSENSI ---
                                    $listSiswa = [];
                                    $id_kelas = $row['id_kelas'];
                                    $sqlSiswa = $koneksi->prepare("SELECT id_siswa, nama_siswa, jenis_kelamin FROM siswa WHERE id_kelas=? ORDER BY nama_siswa");
                                    $sqlSiswa->bind_param("i", $id_kelas);
                                    $sqlSiswa->execute();
                                    $resSiswa = $sqlSiswa->get_result();
                                    while($s = $resSiswa->fetch_assoc()) {
                                        $listSiswa[$s['id_siswa']] = ['nama' => $s['nama_siswa'], 'jk' => $s['jenis_kelamin']];
                                    }
                                    $sqlSiswa->close();

                                    // --- GET ABSENSI SISWA (dari tabel absensi, hanya yang sakit/izin/alpa) ---
                                    $absensiMap = [];
                                    $rekap = ['sakit'=>0, 'izin'=>0, 'alpa'=>0];
                                    $sqlAbs = $koneksi->prepare("SELECT id_siswa, status_kehadiran FROM absensi WHERE id_jurnal=?");
                                    $sqlAbs->bind_param("i", $row['id_jurnal_guru']);
                                    $sqlAbs->execute();
                                    $resAbs = $sqlAbs->get_result();
                                    while($abs = $resAbs->fetch_assoc()) {
                                        $absensiMap[$abs['id_siswa']] = $abs['status_kehadiran'];
                                        if (isset($rekap[$abs['status_kehadiran']])) {
                                            $rekap[$abs['status_kehadiran']]++;
                                        }
                                    }
                                    $sqlAbs->close();
                                    ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d', strtotime($row['tanggal'])); ?></td>
                                        <td>
                                            <?php
                                                echo htmlspecialchars($row['nama_mata_pelajaran']) . " - " .
                                                     htmlspecialchars($row['nama_kelas']) . " (" .
                                                     substr($row['jam_mulai'],0,5) . "-" . substr($row['jam_selesai'],0,5) . ")";
                                            ?>
                                        </td>
                                        <td style="white-space:pre-line; text-align:left;"><?php echo htmlspecialchars($row['materi']); ?></td>
                                        <td><span class="badge badge-soft-warning text-dark"><?php echo $rekap['sakit']; ?></span></td>
                                        <td><span class="badge badge-soft-info text-dark"><?php echo $rekap['izin']; ?></span></td>
                                        <td><span class="badge badge-soft-danger"><?php echo $rekap['alpa']; ?></span></td>
                                        <td style="white-space:pre-line; text-align:left; max-width:220px;">
                                            <span class="single-catatan">
                                                <?php echo nl2br(htmlspecialchars($row['catatan'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['dokumentasi_path'])): ?>
                                                <?php
                                                $ext = strtolower(pathinfo($row['dokumentasi_path'], PATHINFO_EXTENSION));
                                                $url = 'uploads/dokumentasi/' . $row['dokumentasi_path'];
                                                if (in_array($ext, ['jpg','jpeg','png'])) {
                                                    echo "<a href='$url' target='_blank'><img src='$url' alt='Dokumentasi' style='max-width:60px;max-height:60px;border-radius:6px;'></a>";
                                                } else {
                                                    echo "<a href='$url' target='_blank'>Lihat File</a>";
                                                }
                                                ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                                if ($row['status_validasi'] == '1') {
                                                    echo "<span class='badge badge-soft-secondary'>Belum Divalidasi</span>";
                                                } elseif ($row['status_validasi'] == '2') {
                                                    echo "<span class='badge badge-soft-success'>Sudah Divalidasi</span>";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (count($listSiswa)): ?>
                                                <button class="btn btn-sm btn-info absensi-detail-btn" data-bs-toggle="modal" data-bs-target="#absensiDetailModal<?php echo $row['id_jurnal_guru']; ?>">Lihat</button>
                                                <button class="btn btn-sm btn-warning absensi-edit-btn" data-jurnal="<?php echo $row['id_jurnal_guru']; ?>" data-kelas="<?php echo $row['id_kelas']; ?>">Edit</button>

                                                <div class="modal fade" id="absensiDetailModal<?php echo $row['id_jurnal_guru']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detail Absensi Siswa</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered table-sm">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>No</th>
                                                                                <th>Nama Siswa</th>
                                                                                <th>Jenis Kelamin</th>
                                                                                <th>Status Kehadiran</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php $no=1; foreach($listSiswa as $id_siswa => $data_siswa): ?>
                                                                            <tr>
                                                                                <td><?php echo $no++; ?></td>
                                                                                <td><?php echo htmlspecialchars($data_siswa['nama']); ?></td>
                                                                                <td><?php echo ($data_siswa['jk'] == 'L' ? 'Laki-laki' : 'Perempuan'); ?></td>
                                                                                <td>
                                                                                    <?php
                                                                                    $status = isset($absensiMap[$id_siswa]) ? $absensiMap[$id_siswa] : 'hadir';
                                                                                    if ($status=='hadir') echo "<span class='badge badge-soft-success'>Hadir</span>";
                                                                                    elseif ($status=='sakit') echo "<span class='badge badge-soft-warning text-dark'>Sakit</span>";
                                                                                    elseif ($status=='izin') echo "<span class='badge badge-soft-info text-dark'>Izin</span>";
                                                                                    elseif ($status=='alpa') echo "<span class='badge badge-soft-danger'>Alpa</span>";
                                                                                    else echo htmlspecialchars($status); // Fallback jika status tidak dikenal
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['catatan_validasi'])): ?>
                                                <span class="badge badge-soft-danger"><?php echo htmlspecialchars($row['catatan_validasi']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status_validasi'] == '1') : // Hanya bisa diedit/hapus jika belum divalidasi ?>
                                                <button class="aksi-btn edit-btn" data-id="<?php echo $row['id_jurnal_guru']; ?>" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="aksi-btn delete-btn" data-id="<?php echo $row['id_jurnal_guru']; ?>" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php elseif ($row['status_validasi']=='2') : // Jika sudah divalidasi, tombol dinonaktifkan ?>
                                                <button class="aksi-btn" title="Sudah Divalidasi" disabled>
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="aksi-btn" title="Sudah Divalidasi" disabled>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center">Belum ada data jurnal.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Jurnal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id_jurnal_guru">
            <div class="mb-3">
                <label class="form-label">Materi</label>
                <textarea name="materi" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="editAbsensiModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="formEditAbsensi">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Absensi Siswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="editAbsensiBody">
          <div class="text-center text-muted">Memuat data siswa...</div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="id_jurnal_guru" id="editAbsensiJurnalId">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean(); // Ambil semua output buffering dan simpan ke $content
ob_start(); // Mulai output buffering lagi untuk skrip JS
?>
<style>
    /* CSS Styling (sesuai yang Anda berikan) */
    body { background: #f6fafd; }
    .fw-bold { font-weight: 700; }
    .card-custom {
        border-radius: 20px;
        box-shadow: 8px 8px 16px #e5e9f2, -8px -8px 16px #ffffff;
        padding: 2.5rem 2rem 2rem 2rem;
    }
    .jurnal-table {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        width: 100%;
        font-size: 1.02rem;
    }
    th, td {
        vertical-align: middle !important;
        text-align: center;
        border-color: #d6eaff !important;
    }
    th {
        background: #f8fafc;
        color: #26364d;
        font-weight: 600;
        border-bottom: 2px solid #d6eaff !important;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    tr {
        border-bottom: 1px solid #d6eaff !important;
    }
    .table>:not(caption)>*>* {
        border-bottom-width: 1px;
        border-top: none;
    }
    .aksi-btn {
        background: none;
        border: none;
        color: #26364d;
        font-size: 1.2rem;
        margin: 0 5px;
        cursor: pointer;
        transition: color 0.2s;
    }
    .aksi-btn:hover { color: #395886; }
    .shadow-custom { box-shadow: 8px 8px 16px #e5e9f2, -8px -8px 16px #ffffff; }
    .badge {
        font-size: 1rem;
        padding: 0.45em 0.9em;
        border-radius: 8px;
        font-weight: 500;
        letter-spacing: 0.02em;
    }
    .badge-soft-success { background: #e6f7e6; color: #218838; }
    .badge-soft-warning { background: #fffbe6; color: #856404; }
    .badge-soft-info { background: #e6f4fa; color: #117a8b; }
    .badge-soft-danger { background: #fdeaea; color: #c82333; }
    .badge-soft-secondary { background: #f1f3f5; color: #6c757d; }
    .single-catatan {
        display: block;
        padding: 4px 0;
        color: #444;
        font-size: 0.95rem;
        line-height: 1.4;
        white-space: pre-line;
        word-break: break-word;
    }
    td[style*="max-width:220px;"] {
        word-break: break-word;
        padding: 0.75rem 1rem !important;
        background: #fff;
        vertical-align: top !important;
    }
    @media (max-width: 1200px) {
        .jurnal-table { font-size: 0.95rem; }
        th, td { padding: 0.5rem 0.3rem; }
    }
    @media (max-width: 900px) {
        .jurnal-table { font-size: 0.9rem; }
        th, td { padding: 0.4rem 0.2rem; }
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
$(function(){
    // Tombol edit klik
    $('.jurnal-table').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.post('', { action: 'get', id: id }, function(response){
            if(response.status === 'success'){
                $('#editForm input[name="id_jurnal_guru"]').val(response.data.id_jurnal_guru);
                $('#editForm textarea[name="materi"]').val(response.data.materi); // Menggunakan textarea
                $('#editForm textarea[name="catatan"]').val(response.data.catatan); // Menggunakan textarea
                $('#editForm input[name="tanggal"]').val(response.data.tanggal);
                var modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            } else {
                alert(response.message || "Gagal mengambil data jurnal.");
            }
        }, 'json')
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error: " + textStatus + ", " + errorThrown);
            alert("Terjadi kesalahan saat mengambil data jurnal.");
        });
    });

    // Submit edit
    $('#editForm').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serializeArray();
        data.push({ name: 'action', value: 'update' });
        $.post('', $.param(data), function(response){
            if(response.status === 'success'){
                alert("Berhasil update jurnal!");
                location.reload(); // Reload halaman untuk melihat perubahan
            } else {
                alert(response.message || "Gagal update jurnal!");
            }
        }, 'json')
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error: " + textStatus + ", " + errorThrown);
            alert("Terjadi kesalahan saat update jurnal.");
        });
    });

    // Tombol hapus klik
    $('.jurnal-table').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if(confirm("Apakah Anda yakin ingin menghapus jurnal ini? Tindakan ini tidak dapat dibatalkan dan absensi terkait juga akan terhapus.")){
            $.post('', { action: 'delete', id: id }, function(response){
                if(response.status === 'success'){
                    alert("Jurnal berhasil dihapus!");
                    location.reload(); // Reload halaman untuk melihat perubahan
                } else {
                    alert(response.message || "Gagal menghapus jurnal!");
                }
            }, 'json')
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ", " + errorThrown);
                alert("Terjadi kesalahan saat menghapus jurnal.");
            });
        }
    });

    // Tombol Edit Absensi
    $('.jurnal-table').on('click', '.absensi-edit-btn', function() {
        var id_jurnal = $(this).data('jurnal');
        var id_kelas = $(this).data('kelas');
        $('#editAbsensiJurnalId').val(id_jurnal);
        $('#editAbsensiBody').html('<div class="text-center text-muted">Memuat data siswa...</div>');
        var modal = new bootstrap.Modal(document.getElementById('editAbsensiModal'));
        modal.show();

        // Ambil data siswa dan absensi via AJAX
        $.post('', { action: 'get_absensi', id_jurnal: id_jurnal, id_kelas: id_kelas }, function(response){
            if(response.status === 'success'){
                var html = '<div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr><th>No</th><th>Nama Siswa</th><th>Jenis Kelamin</th><th>Status Kehadiran</th></tr></thead><tbody>';
                $.each(response.data, function(i, siswa){
                    html += '<tr>';
                    html += '<td>'+(i+1)+'</td>';
                    html += '<td>'+siswa.nama+'</td>';
                    html += '<td>'+(siswa.jk == 'L' ? 'Laki-laki' : 'Perempuan')+'</td>';
                    html += '<td><select name="absensi['+siswa.id_siswa+']" class="form-select form-select-sm">';
                    var opts = ['hadir','sakit','izin','alpa'];
                    $.each(opts, function(_,opt){
                        html += '<option value="'+opt+'"'+(siswa.status==opt?' selected':'')+'>'+opt.charAt(0).toUpperCase()+opt.slice(1)+'</option>';
                    });
                    html += '</select></td>';
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
                $('#editAbsensiBody').html(html);
            } else {
                $('#editAbsensiBody').html('<div class="text-danger">'+(response.message||'Gagal memuat data absensi.')+'</div>');
            }
        },'json');
    });

    // Submit Edit Absensi
    $('#formEditAbsensi').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serializeArray();
        data.push({ name: 'action', value: 'update_absensi' });
        $.post('', $.param(data), function(response){
            if(response.status === 'success'){
                alert('Absensi berhasil diupdate!');
                location.reload();
            } else {
                alert(response.message || 'Gagal update absensi!');
            }
        },'json');
    });
});

// Fungsi untuk menghindari XSS pada output
function htmlspecialchars(text) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
</script>
<?php
$script = ob_get_clean(); // Ambil semua output buffering (skrip JS) dan simpan ke $script
include 'layout.php'; // Sertakan file layout
renderLayout($content, $script); // Panggil fungsi renderLayout dari layout.php

$koneksi->close(); // Tutup koneksi database setelah semua proses selesai
?>