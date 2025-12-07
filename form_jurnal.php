<?php
ob_start(); // Memulai output buffering
session_start(); // Memulai sesi PHP

include_once("koneksi.php");

// Verifikasi peran pengguna
if (strtolower($_SESSION['role']) != 'guru') {
    header("Location: login.php"); // Alihkan ke halaman login jika bukan guru
    exit;
}

$id_user = $_SESSION['user']['id_user']; // Ambil ID pengguna dari sesi

// --- Proses Form Jika Ada POST Request ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Ambil data dari form
        $id_jadwal_mengajar = $_POST['jadwal'];
        $tanggal = $_POST['tanggal'];
        $materi = $_POST['materi'];
        $catatan = $_POST['catatan'] ?? ''; // Catatan bersifat opsional
        $dokumentasi_path = null;

        // Proses upload dokumentasi jika ada
        if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf']; // Jenis file yang diizinkan
            if (in_array($ext, $allowed)) {
                $newName = 'dok_' . time() . '_' . rand(1000,9999) . '.' . $ext; // Nama unik untuk file
                $uploadPath = 'uploads/dokumentasi/' . $newName; // Path penyimpanan
                if (!is_dir('uploads/dokumentasi')) { // Buat direktori jika belum ada
                    mkdir('uploads/dokumentasi', 0777, true);
                }
                if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $uploadPath)) {
                    $dokumentasi_path = $newName; // Simpan nama file ke database
                }
            }
        }

        // Query untuk menyimpan data jurnal guru
        $query_jurnal = "INSERT INTO jurnal_guru (
            id_jadwal_mengajar,
            id_user,
            use_id_user,
            tanggal,
            materi,
            catatan,
            status_validasi,
            dokumentasi_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $status_validasi = '1'; // Atur status validasi default

        $stmt_jurnal = $conn->prepare($query_jurnal);
        $stmt_jurnal->bind_param("iiisssss",
            $id_jadwal_mengajar,
            $id_user,
            $id_user, // Diasumsikan use_id_user sama dengan id_user yang membuat jurnal
            $tanggal,
            $materi,
            $catatan,
            $status_validasi,
            $dokumentasi_path
        );

        // Eksekusi query jurnal
        if ($stmt_jurnal->execute()) {
            $id_jurnal = $conn->insert_id; // Dapatkan ID jurnal yang baru saja disimpan

            // Proses penyimpanan absensi siswa
            $absensi = isset($_POST['absensi']) ? $_POST['absensi'] : [];
            if (!empty($absensi)) {
                foreach ($absensi as $id_siswa => $status) {
                    // Hanya simpan absensi jika statusnya sakit, izin, atau alpa
                    if (in_array($status, ['sakit','izin','alpa'])) {
                        $stmt_absen = $conn->prepare("INSERT INTO absensi (id_siswa, id_jurnal, status_kehadiran) VALUES (?, ?, ?)");
                        $stmt_absen->bind_param("iis", $id_siswa, $id_jurnal, $status);
                        $stmt_absen->execute();
                        $stmt_absen->close(); // Tutup statement absensi setelah digunakan
                    }
                }
            }
            $_SESSION['sukses'] = "Jurnal dan absensi berhasil disimpan!";
        } else {
            throw new Exception("Gagal menyimpan jurnal: " . $stmt_jurnal->error);
        }
        $stmt_jurnal->close(); // Tutup statement jurnal

        header("Location: " . $_SERVER['PHP_SELF']); // Alihkan kembali ke halaman ini
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage(); // Tangani error
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// --- Ambil Data Jadwal Mengajar ---
$query_jadwal = "SELECT
    jm.id_jadwal_mengajar,
    mp.nama_mata_pelajaran,
    k.nama_kelas,
    k.id_kelas,
    jp.jam_mulai,
    jp.jam_selesai
FROM jadwal_mengajar jm
JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
JOIN kelas k ON jm.id_kelas = k.id_kelas
JOIN jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
WHERE jm.id_user = ?
ORDER BY mp.nama_mata_pelajaran, k.nama_kelas, jp.jam_mulai";
$stmt_jadwal = $conn->prepare($query_jadwal);
$stmt_jadwal->bind_param("i", $id_user);
$stmt_jadwal->execute();
$result_jadwal = $stmt_jadwal->get_result();
$stmt_jadwal->close(); // Tutup statement jadwal

// --- Ambil Nama User ---
$query_user = "SELECT nama_user FROM user WHERE id_user = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$nama_user = $user_data ? $user_data['nama_user'] : '';
$stmt->close(); // Tutup statement user

$conn->close(); // Tutup koneksi database

// --- HTML untuk Form ---
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-custom p-4 shadow">
                <h2 class="mb-4 fw-bold text-center" style="color:#26364d;">Agenda Dan Jurnal Guru</h2>
                <?php if(isset($_SESSION['sukses'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['sukses']; unset($_SESSION['sukses']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form id="mainForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Jadwal Mengajar</label>
                            <select class="form-select" name="jadwal" id="jadwalSelect" required>
                                <option value="" disabled selected>Pilih Jadwal Mengajar</option>
                                <?php if($result_jadwal->num_rows > 0): ?>
                                    <?php while($row = $result_jadwal->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($row['id_jadwal_mengajar']); ?>" data-id_kelas="<?php echo $row['id_kelas']; ?>">
                                            <?php
                                                echo htmlspecialchars($row['nama_mata_pelajaran']) . " - " .
                                                     htmlspecialchars($row['nama_kelas']) . " (" .
                                                     substr($row['jam_mulai'],0,5) . "-" . substr($row['jam_selesai'],0,5) . ")";
                                            ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada jadwal mengajar</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Materi</label>
                        <textarea class="form-control" name="materi" rows="2" placeholder="Tulis materi yang diajarkan..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan (opsional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dokumentasi (opsional, jpg/png/pdf)</label>
                        <input type="file" class="form-control" name="dokumentasi" accept=".jpg,.jpeg,.png,.pdf">
                    </div>

                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-success w-50" id="btnAbsensiKelas" data-bs-toggle="modal" data-bs-target="#modalAbsensiKelas">Absensi Kelas</button>
                        <input type="hidden" id="isAbsensiFilled" value="0"/> </div>

                    <div class="modal fade" id="modalAbsensiKelas" tabindex="-1" aria-labelledby="modalAbsensiKelasLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form id="formAbsensiSiswa">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalAbsensiKelasLabel">Absensi Siswa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="absensiSiswaList">
                                        <div class="text-center text-muted">Silakan pilih jadwal untuk menampilkan daftar siswa.</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="absensiInputs"></div>

                  
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean(); // Ambil semua output buffering dan simpan ke $content
ob_start(); // Mulai output buffering lagi untuk skrip JS
?>

<script>
// Dapatkan elemen-elemen yang diperlukan
const jadwalSelect = document.getElementById('jadwalSelect');
const btnAbsensiKelas = document.getElementById('btnAbsensiKelas');
const absensiSiswaList = document.getElementById('absensiSiswaList');
const formAbsensiSiswa = document.getElementById('formAbsensiSiswa');
const absensiInputs = document.getElementById('absensiInputs');
const isAbsensiFilled = document.getElementById('isAbsensiFilled');

// Event listener untuk tombol "Absensi Kelas"
btnAbsensiKelas.onclick = function() {
    var jadwal = jadwalSelect.value;
    if (!jadwal) {
        // Jika jadwal belum dipilih, tampilkan pesan error
        absensiSiswaList.innerHTML = '<div class="text-danger text-center">Silakan pilih jadwal terlebih dahulu</div>';
        return;
    }
    // Lakukan AJAX call untuk mengambil data siswa berdasarkan jadwal (id_kelas)
    fetch('get_siswa_by_jadwal.php?jadwal='+jadwal)
        .then(response => response.json()) // Parse respons sebagai JSON
        .then(data => {
            if (data.length === 0) {
                // Jika tidak ada siswa ditemukan
                absensiSiswaList.innerHTML = '<div class="text-danger text-center">Tidak ada siswa di kelas ini.</div>';
                return;
            }
            // Bangun tabel HTML untuk daftar siswa
            let html = '<table class="table table-bordered"><thead><tr><th>Nama Siswa</th><th>Jenis Kelamin</th><th>Kehadiran</th></tr></thead><tbody>';
            data.forEach(s => {
                html += `<tr>
                    <td>${s.nama_siswa}</td>
                    <td>${s.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'}</td> <td>
                        <select name="absensi[${s.id_siswa}]" class="form-select">
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            absensiSiswaList.innerHTML = html; // Masukkan HTML ke modal
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
            absensiSiswaList.innerHTML = '<div class="text-danger text-center">Gagal memuat data siswa.</div>';
        });
};

// Event listener saat form absensi siswa di dalam modal disubmit
formAbsensiSiswa.onsubmit = function(e) {
    e.preventDefault(); // Mencegah form modal melakukan submit normal
    // Salin input absensi siswa dari modal ke form utama agar ikut ter-submit
    absensiInputs.innerHTML = ''; // Kosongkan dulu
    const selects = formAbsensiSiswa.querySelectorAll('select[name^="absensi"]');
    selects.forEach(sel => {
        const nm = sel.name;
        const val = sel.value;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = nm;
        input.value = val;
        absensiInputs.appendChild(input); // Tambahkan hidden input ke form utama
    });
    isAbsensiFilled.value = "1"; // Set flag bahwa absensi sudah diisi
    // Tutup modal
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAbsensiKelas')).hide();
};

// Event listener untuk validasi form utama sebelum submit
document.getElementById('mainForm').onsubmit = function(e) {
    // Cek apakah absensi sudah diisi
    if (isAbsensiFilled.value !== "1") {
        alert("Silakan lakukan absensi kelas terlebih dahulu!");
        btnAbsensiKelas.focus(); // Fokuskan ke tombol absensi
        e.preventDefault(); // Batalkan submit form
        return false;
    }
};
</script>
<?php
$script = ob_get_clean(); // Ambil semua output buffering (skrip JS) dan simpan ke $script
include 'layout.php'; // Sertakan file layout
renderLayout($content, $script); // Panggil fungsi renderLayout dari layout.php
