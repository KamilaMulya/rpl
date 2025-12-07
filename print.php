<?php
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
    exit;
}
include_once "koneksi.php";

$data_jurnal = [];
$tahun = $_POST['tahun'];
$guru = $_POST['guru'];
$mapel = $_POST['mapel'];
$kls = $_POST['kelas'];
    // Ambil nama guru
$nama = "SELECT nama_user from user where id_user = '$guru'";
$nama_result = mysqli_query($koneksi, $nama);
$nama_row = mysqli_fetch_assoc($nama_result);
$nama = $nama_row['nama_user'] ?? '-';

// Ambil nama mata pelajaran
$matpel = "SELECT nama_mata_pelajaran from mata_pelajaran where id_mata_pelajaran = '$mapel'";
$mapel_result = mysqli_query($koneksi, $matpel);
$mapel_row = mysqli_fetch_assoc($mapel_result);
$matpel = $mapel_row['nama_mata_pelajaran'] ?? '-';

// Ambil nama kelas
$kelas = "SELECT nama_kelas from kelas where id_kelas = '$kls'";
$kelas_result = mysqli_query($koneksi, $kelas);
$kelas_row = mysqli_fetch_assoc($kelas_result);
$kelas = $kelas_row['nama_kelas'] ?? '-';

$querytahun = mysqli_query($koneksi, "SELECT tahun FROM tahun_ajaran WHERE id_tahun_ajaran = $tahun");
$hasil = mysqli_fetch_assoc($querytahun);
$query = "
SELECT 
    jg.tanggal,
    jg.id_jurnal_guru,
    jp.jam_mulai,
    jg.materi,
    jg.catatan,
    jg.tanggal_validasi,
    jg.catatan_validasi,
    uv.nama_user AS nama_validator,
    jg.dokumentasi_path
FROM jurnal_guru jg
JOIN jadwal_mengajar jm ON jg.id_jadwal_mengajar = jm.id_jadwal_mengajar
JOIN kelas k ON jm.id_kelas = k.id_kelas
JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
JOIN jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
JOIN user u ON jg.id_user = u.id_user
JOIN user uv ON jg.use_id_user = uv.id_user
JOIN tahun_ajaran ta ON jm.id_tahun_ajaran = ta.id_tahun_ajaran
WHERE ta.id_tahun_ajaran = $tahun
  AND jg.status_validasi = '2'
  AND jg.id_user = '$guru'
  AND mp.id_mata_pelajaran = '$mapel'
  AND k.id_kelas = '$kls'
ORDER BY jg.tanggal ASC;
";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_jurnal[] = $row;
    }
}

// Ini untuk chartnya
$querychart = mysqli_query($koneksi, "
  SELECT 
    MONTH(jg.tanggal) AS bulan,
    COUNT(*) AS total,
    SUM(CASE WHEN jg.status_validasi = '2' THEN 1 ELSE 0 END) AS tervalidasi,
    SUM(CASE WHEN jg.status_validasi != '2' OR jg.status_validasi IS NULL THEN 1 ELSE 0 END) AS belum_validasi
  FROM jurnal_guru jg
JOIN jadwal_mengajar jm ON jg.id_jadwal_mengajar = jm.id_jadwal_mengajar
JOIN kelas k ON jm.id_kelas = k.id_kelas
JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
JOIN user u ON jg.id_user = u.id_user
JOIN tahun_ajaran ta ON jm.id_tahun_ajaran = ta.id_tahun_ajaran
  WHERE jg.tanggal BETWEEN 
    STR_TO_DATE(CONCAT(LEFT(ta.tahun, 4), '-07-01'), '%Y-%m-%d') AND 
    STR_TO_DATE(CONCAT(RIGHT(ta.tahun, 4), '-06-30'), '%Y-%m-%d')
  AND jg.id_user = '$guru'
  AND mp.id_mata_pelajaran = '$mapel'
  AND k.id_kelas = '$kls'
  GROUP BY MONTH(tanggal)
  ORDER BY bulan
");

$labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$total = array_fill(0, 12, 0);
$tervalidasi = array_fill(0, 12, 0);
$belum_validasi = array_fill(0, 12, 0);

while ($rowss = mysqli_fetch_assoc($querychart)) {
    $index = (int)$rowss['bulan'] - 1;
    $total[$index] = (int)$rowss['total'];
    $tervalidasi[$index] = (int)$rowss['tervalidasi'];
    $belum_validasi[$index] = (int)$rowss['belum_validasi'];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>E-JurnalGuru</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  </head>
  <body>
    <div class="wrapper">
          <div class="page-inner">
            <div class="page-header">
                <div class="row">
                    <div class="col-2 d-flex align-items-center justify-content-center">  
                        <img src="assets/img/image-removebg-preview.png" alt="navbar brand" class="navbar-brand" height="40" />
                    </div>
                    <div class="col-8 text-center">
                        <h5 style="margin-bottom: 5px;">PEMERINTAHAN PROVINSI JAWA TIMUR</h5>
                        <h4 style="margin-bottom: 0px; margin-top: 0px;">DINAS PENDIDIKAN</h4>
                        <h3 style="margin-bottom: 0px; margin-top: 0px;">SMA NU SUMENEP</h3>
                        <span style="margin-bottom: 5px;">Jl. Intan No.7, Dalem Anyar, Bangselok, Kec. Kota Sumenep, Kabupaten Sumenep</span><br>
                        <span style="margin-bottom: 5px; font-style:italic;">Website: http://smanuoke.wordpress.com, Email: smanu.sumenep@gmail.com</span>
                    </div>
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <img src="assets/img/image-removebg-preview.png" alt="navbar brand" class="navbar-brand" height="40" style="align-content: center;" />
                    </div>
                </div>
                <hr style="border: 2px solid black; margin-top: 10px; margin-bottom: 20px;">
            </div>
            <div class="page-category">
                <div class="row">
                    <div class="col-12 text-center">
                        <h6 style="margin-bottom: 5px;">JURNAL MENGAJAR GURU</h6>
                        <h6 style="margin-bottom: 5px;">TAHUN AJARAN <?= !empty($_POST['tahun']) ? $hasil['tahun'] : "-" ?></h6>
                    </div>
                </div>
                <div class="row mb-3 mt-3">
                  <!-- Kiri: Nama Guru & Mata Pelajaran -->
                  <div class="col-8">
                    <div class="d-flex mb-1">
                      <div style="width: 35%;">Nama Guru</div>
                      <div style="width: 5%;">:</div>
                      <div style="flex: 1;"><?= isset($_POST['guru']) ? htmlspecialchars($nama) : '-' ?></div>
                    </div>
                    <div class="d-flex">
                      <div style="width: 35%;">Mata Pelajaran</div>
                      <div style="width: 5%;">:</div>
                      <div style="flex: 1;"><?= isset($_POST['mapel']) ? htmlspecialchars($matpel) : '-' ?></div>
                    </div>
                  </div>

                  <!-- Kanan: Kelas -->
                  <div class="col-4 text-right">
                    <div class="d-flex justify-content-end">
                      <div style="width: 35%; text-align: left;">Kelas</div>
                      <div style="width: 5%; text-align: left;">:</div>
                      <div style="flex: 1; text-align: left;"><?= isset($_POST['kelas']) ? htmlspecialchars($kelas) : '-' ?></div>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="chart-container">
                          <canvas id="multipleLineChart"></canvas>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th rowspan="2">Tanggal</th>
                      <th rowspan="2">Jam Mulai</th>
                      <th rowspan="2">Materi</th>
                      <th colspan="4">Jumlah Siswa</th>
                      <th rowspan="2">Catatan Jurnal</th>
                      <th rowspan="2">Dokumentasi</th>
                    </tr>
                    <tr>
                      <th>S</th>
                      <th>I</th>
                      <th>A</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(!empty($data_jurnal)): ?>
                    <?php foreach($data_jurnal as $row): ?>
                    <tr>
                      <?php 
                      $idjurnalguru = $row['id_jurnal_guru'];
                      $queryabsen = "
                        SELECT
                          SUM(CASE WHEN a.status_kehadiran = 'sakit' THEN 1 ELSE 0 END) AS sakit,
                          SUM(CASE WHEN a.status_kehadiran = 'izin' THEN 1 ELSE 0 END) AS izin,
                          SUM(CASE WHEN a.status_kehadiran = 'alpa' THEN 1 ELSE 0 END) AS alpa,
                          COUNT(s.id_siswa) AS total
                        FROM absensi a
                        JOIN siswa s ON a.id_siswa = s.id_siswa
                        WHERE a.id_jurnal = '$idjurnalguru' AND s.id_kelas = '$kls'
                      ";
                      $resultAbsen = mysqli_query($koneksi, $queryabsen);
                      $dataAbsen = mysqli_fetch_assoc($resultAbsen);

                      $queryTotal = "SELECT COUNT(*) as total FROM siswa WHERE id_kelas = '$kls'";
                      $resultTotal = mysqli_query($koneksi, $queryTotal);
                      $dataTotal = mysqli_fetch_assoc($resultTotal);
                      ?>
                      <td><?= htmlspecialchars($row['tanggal']) ?></td>
                      <td><?= htmlspecialchars($row['jam_mulai']) ?></td>
                      <td><?= htmlspecialchars($row['materi']) ?></td>
                      <?php if ($dataAbsen['sakit'] == '0' || $dataAbsen['sakit'] == ''): ?>
                        <td>-</td>
                      <?php else: ?>
                        <td><?= htmlspecialchars($dataAbsen['sakit']) ?></td>
                      <?php endif; ?>
                      <?php if ($dataAbsen['izin'] == '0' || $dataAbsen['izin'] == ''): ?>
                        <td>-</td>
                      <?php else: ?>
                        <td><?= htmlspecialchars($dataAbsen['izin']) ?></td>
                      <?php endif; ?>
                      <?php if ($dataAbsen['alpa'] == '0' || $dataAbsen['alpa'] == ''): ?>
                        <td>-</td>
                      <?php else: ?>
                        <td><?= htmlspecialchars($dataAbsen['alpa']) ?></td>
                      <?php endif; ?>
                      <?php if ($dataTotal['total'] == '0' || $dataTotal['total'] == ''): ?>
                        <td>-</td>
                      <?php else: ?>
                        <td><?= htmlspecialchars($dataTotal['total']) ?></td>
                      <?php endif; ?>
                      <?php if ($row['catatan'] == ''): ?>
                        <td>-</td>
                      <?php else: ?>
                        <td><?= htmlspecialchars($row['catatan']) ?></td>
                      <?php endif; ?>
                      <td><img src='uploads/dokumentasi/<?= $row['dokumentasi_path'] ?>' alt='<?= $row['dokumentasi_path'] ?>' height='100px'></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                      <td colspan="9" class="text-center">Tidak ada data.</td>
                    <?php endif; ?>
                  </tbody>
                </table>
                
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Divalidasi Oleh</th>
                      <th>Tanggal Validasi</th>
                      <th>Catatan Validasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(!empty($data_jurnal)): ?>
                    <?php foreach($data_jurnal as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['nama_validator']) ?></td>
                      <td><?= htmlspecialchars($row['tanggal_validasi']) ?></td>
                      <?php if ($row['catatan_validasi'] == ''): ?>
                        <td>-</td>
                      <?php else: ?>
                        <td><?= htmlspecialchars($row['catatan_validasi']) ?></td>
                      <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                      <td colspan="3" class="text-center">Tidak ada data.</td>
                    <?php endif; ?>
                  </tbody>
                </table>
            </div>
          </div>
    </div>

    <!--   Core JS Files   -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>
    <script>
  var multipleLineChart = document
    .getElementById("multipleLineChart")
    .getContext("2d");
  var myMultipleLineChart = new Chart(multipleLineChart, {
    type: "line",
    data: {
      labels: <?php echo json_encode($labels); ?>,
      datasets: [
        {
          label: "Total Jurnal",
          borderColor: "#1d7af3",
          pointBorderColor: "#FFF",
          pointBackgroundColor: "#1d7af3",
          backgroundColor: "transparent",
          fill: true,
          borderWidth: 2,
          data: <?php echo json_encode($total); ?>,
        },
        {
          label: "Jurnal Tervalidasi",
          borderColor: "#59d05d",
          pointBorderColor: "#FFF",
          pointBackgroundColor: "#59d05d",
          backgroundColor: "transparent",
          fill: true,
          borderWidth: 2,
          data: <?php echo json_encode($tervalidasi); ?>,
        },
        {
          label: "Jurnal Belum Validasi",
          borderColor: "#f3545d",
          pointBorderColor: "#FFF",
          pointBackgroundColor: "#f3545d",
          backgroundColor: "transparent",
          fill: true,
          borderWidth: 2,
          data: <?php echo json_encode($belum_validasi); ?>,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: "top",
      },
      tooltips: {
        mode: "nearest",
        intersect: false,
        position: "nearest",
      },
      layout: {
        padding: { left: 15, right: 15, top: 15, bottom: 15 },
      },
    },
  });

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
  </body>
</html>