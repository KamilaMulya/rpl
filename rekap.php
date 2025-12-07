<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Pengawas") {
    header('Location: index.php');
}
if (!isset($_POST['tahunajaran'])) {
  header('Location: rekapitulasi.php');
}
include_once("koneksi.php");

$data_jurnal = [];

if(isset($_POST['submit'])) {
    $tahun = $_POST['tahunajaran'];
    $querytahun = mysqli_query($koneksi, "SELECT tahun FROM tahun_ajaran WHERE id_tahun_ajaran = $tahun");
    $hasil = mysqli_fetch_assoc($querytahun);
    $query = "
        SELECT 
            u.nama_user,
            jg.tanggal,
            jp.jam_mulai,
            jp.jam_selesai,
            k.id_kelas,
            jg.id_jurnal_guru,
            k.nama_kelas,
            mp.nama_mata_pelajaran,
            jg.materi,
            jg.catatan,
            jg.dokumentasi_path,
            jg.tanggal_validasi,
            jg.catatan_validasi
        FROM jurnal_guru jg
        JOIN user u ON jg.id_user = u.id_user
        JOIN jadwal_mengajar jm ON jg.id_jadwal_mengajar = jm.id_jadwal_mengajar
        JOIN kelas k ON jm.id_kelas = k.id_kelas
        JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
        JOIN jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
        JOIN tahun_ajaran ta ON jm.id_tahun_ajaran = ta.id_tahun_ajaran
        WHERE jm.id_tahun_ajaran = $tahun AND jg.status_validasi = '2'
        ORDER BY jg.tanggal ASC
    ";

    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data_jurnal[] = $row;
        }
    }
}
?>
        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Rekapitulasi</h3>
              <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                  <a href="index.php">
                    <i class="icon-home"></i>
                  </a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="rekapitulasi.php">Rekapitulasi</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Rekap Jurnal</a>
                </li>
              </ul>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Rekapitulasi Jurnal Tahun Ajaran <?= $hasil['tahun'] ?></h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover"
                      >
                        <thead>
                          <tr>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Materi</th>
                            <th>Catatan Ajar</th>
                            <th>Total Siswa per Kelas</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Tanggal Validasi</th>
                            <th>Catatan Validasi</th>
                            <th>Dokumentasi</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Materi</th>
                            <th>Catatan Ajar</th>
                            <th>Total Siswa per Kelas</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Tanggal Validasi</th>
                            <th>Catatan Validasi</th>
                            <th>Dokumentasi</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php foreach($data_jurnal as $row): ?>
                          <?php
                            $id_kelas = $row['id_kelas'];
                            $sqlTotal = $koneksi->prepare("SELECT COUNT(*) AS total_siswa FROM siswa WHERE id_kelas = ?");
                            $sqlTotal->bind_param("i", $id_kelas);
                            $sqlTotal->execute();
                            $resultTotal = $sqlTotal->get_result()->fetch_assoc();
                            $totalSiswa = $resultTotal['total_siswa'];
                            $sqlTotal->close();
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
                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['jam_mulai']) ?> - <?= htmlspecialchars($row['jam_selesai']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                            <td><?= htmlspecialchars($row['nama_mata_pelajaran']) ?></td>
                            <td><?= htmlspecialchars($row['materi']) ?></td>
                            <?php if ($row['catatan'] == ''): ?>
                              <td>-</td>
                            <?php else: ?>
                              <td><?= htmlspecialchars($row['catatan']) ?></td>
                            <?php endif; ?>
                            <td><span class="badge badge-soft-success text-dark"><?= $totalSiswa; ?></span></td>
                            <td><span class="badge badge-soft-warning text-dark"><?= $rekap['sakit']; ?></span></td>
                            <td><span class="badge badge-soft-info text-dark"><?= $rekap['izin'] ?></span></td>
                            <td><span class="badge badge-soft-danger text-dark"><?= $rekap['alpa']; ?></span></td>
                            <td><?= htmlspecialchars($row['tanggal_validasi']) ?></td>
                            <?php if ($row['catatan_validasi'] == ''): ?>
                              <td>-</td>
                            <?php else: ?>
                              <td><?= htmlspecialchars($row['catatan_validasi']) ?></td>
                            <?php endif; ?>
                            <td><img src='uploads/dokumentasi/<?= $row['dokumentasi_path'] ?>' alt='<?= $row['dokumentasi_path'] ?>' height='100px'></td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

<?php
$content = ob_get_clean();
ob_start();
?>
    <script>
      $(document).ready(function () {
        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api()
              .columns()
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });
    });
    </script>
<?php
$script = ob_get_clean();
include 'layout.php';
renderLayout($content, $script);