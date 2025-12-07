<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
}

include "koneksi.php"; 

// Inisialisasi data
$barLabels = [];
$barData = [];

$pieLabels = ["Belum Tervalidasi", "Tervalidasi"];
$pieData = [0, 0];

$bulanIni = date("m");
$tahunIni = date("Y");

// Query untuk data bar chart: jumlah jurnal bulan ini per guru
$queryBar = "SELECT u.nama_user, COUNT(j.id_jurnal_guru) AS total_jurnal
             FROM jurnal_guru j
             JOIN user u ON j.id_user = u.id_user
             WHERE MONTH(j.tanggal) = '$bulanIni' AND YEAR(j.tanggal) = '$tahunIni' AND u.role = 1
             GROUP BY j.id_user";

$resultBar = mysqli_query($koneksi, $queryBar);
while ($row = mysqli_fetch_assoc($resultBar)) {
    $barLabels[] = $row['nama_user'];
    $barData[] = (int)$row['total_jurnal'];
}

// Query untuk data pie chart: jumlah tervalidasi dan belum tervalidasi
$queryPie = "SELECT status_validasi, COUNT(*) as jumlah
             FROM jurnal_guru
             WHERE MONTH(tanggal) = '$bulanIni' AND YEAR(tanggal) = '$tahunIni'
             GROUP BY status_validasi";

$resultPie = mysqli_query($koneksi, $queryPie);
while ($row = mysqli_fetch_assoc($resultPie)) {
    if ($row['status_validasi'] == 1) {
        $pieData[0] = (int)$row['jumlah']; // Belum tervalidasi
    } elseif ($row['status_validasi'] == 2) {
        $pieData[1] = (int)$row['jumlah']; // Tervalidasi
    }
}
?>
        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Statistik</h3>
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
                  <a href="#">Statistik</a>
                </li>
              </ul>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <div class="card-title">Jumlah Jurnal Bulan Ini</div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container">
                      <canvas id="barChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <div class="card-title">Klasifikasi Jurnal Bulan Ini</div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container">
                      <canvas
                        id="pieChart"
                        style="width: 50%; height: 50%"
                      ></canvas>
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
      const barLabels = <?php echo json_encode($barLabels); ?>;
  const barData = <?php echo json_encode($barData); ?>;

  const pieLabels = <?php echo json_encode($pieLabels); ?>;
  const pieData = <?php echo json_encode($pieData); ?>;

  new Chart(document.getElementById("barChart"), {
    type: "bar",
    data: {
      labels: barLabels,
      datasets: [{
        label: "Jumlah Jurnal",
        backgroundColor: "rgb(23, 125, 255)",
        borderColor: "rgb(23, 125, 255)",
        data: barData,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        yAxes: [{ ticks: { beginAtZero: true } }],
      },
    },
  });

  new Chart(document.getElementById("pieChart"), {
    type: "pie",
    data: {
      labels: pieLabels,
      datasets: [{
        data: pieData,
        backgroundColor: ["#f3545d", "#1d7af3"],
        borderWidth: 0,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: "bottom",
        labels: {
          fontColor: "rgb(154, 154, 154)",
          fontSize: 11,
          usePointStyle: true,
          padding: 20,
        },
      },
      tooltips: true,
    },
  });
    </script>
<?php
$script = ob_get_clean();
include 'layout.php';
renderLayout($content, $script);