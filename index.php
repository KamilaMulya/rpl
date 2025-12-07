<?php
ob_start();
session_start();

include_once("koneksi.php");


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$role = $_SESSION['role'];

date_default_timezone_set('Asia/Jakarta');
$server_time = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$today = $server_time->format('Y-m-d');
$current_date = $server_time->format('d');
$current_month = $server_time->format('m');
$current_year = $server_time->format('Y');

$month_name = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Inisialisasi array untuk semua role
$aktivitas_per_bulan = array_fill(1, 12, 0);

if ($role == "Guru") {
    // Cek apakah hari ini sudah mengisi jurnal
    $query_cek_jurnal = "SELECT * FROM jurnal_guru WHERE id_user = '$id_user' AND tanggal = '$today'";
    $result_jurnal = mysqli_query($koneksi, $query_cek_jurnal);
    $sudah_mengisi = mysqli_num_rows($result_jurnal) > 0;

    // Aktivitas per bulan untuk guru tertentu
    $query_aktivitas = "SELECT MONTH(tanggal) as bulan, COUNT(*) as jumlah_aktivitas 
                        FROM jurnal_guru 
                        WHERE id_user = '$id_user' AND YEAR(tanggal) = '$current_year'
                        GROUP BY MONTH(tanggal)";
    $result_aktivitas = mysqli_query($koneksi, $query_aktivitas);

    while ($row = mysqli_fetch_assoc($result_aktivitas)) {
        $aktivitas_per_bulan[$row['bulan']] = $row['jumlah_aktivitas'];
    }
    
    // Nama user
    $query_user = "SELECT nama_user FROM user WHERE id_user = '$id_user'";
    $result_user = mysqli_query($koneksi, $query_user);
    $user_data = mysqli_fetch_assoc($result_user);
    $nama_user = $user_data['nama_user'];
    
} elseif ($role == "Pengawas" || $role == "Admin") {
    // Aktivitas per bulan untuk semua guru (pengawas dan admin melihat semua data)
    $query_aktivitas = "SELECT MONTH(tanggal) as bulan, COUNT(*) as jumlah_aktivitas 
                        FROM jurnal_guru 
                        WHERE YEAR(tanggal) = '$current_year'
                        GROUP BY MONTH(tanggal)";
    $result_aktivitas = mysqli_query($koneksi, $query_aktivitas);

    while ($row = mysqli_fetch_assoc($result_aktivitas)) {
        $aktivitas_per_bulan[$row['bulan']] = $row['jumlah_aktivitas'];
    }
}
?>

<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Dashboard</h3>
      </div>
    </div>
    <?php if($_SESSION['role']=="Guru") : ?>
    <div class="row">
      <div class="col-4">
        <div class="card">
          <div class="card-body p-3 text-center mt-5">
            <div class="h1 m-0"><?php echo $current_date; ?></div>
            <div class="text-muted mb-5"><?php echo $month_name[$current_month]; ?></div>
          </div>
        </div>
        <div class="card card-stats card-round <?php echo $sudah_mengisi ? 'bg-success text-white' : 'card-warning'; ?>">
          <div class="card-body mt-5">
            <div class="row mb-5">
              <div class="col-5">
                <div class="icon-big text-center">
                  <?php if ($sudah_mengisi): ?>
                    <i class="fas fa-check-circle"></i>
                  <?php else: ?>
                    <i class="fas fa-exclamation-triangle"></i>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-7 col-stats">
                <div class="numbers">
                  <?php if ($sudah_mengisi): ?>
                    <p class="card-category fw-bold text-white mb-0">Hari ini sudah mengisi jurnal.</p>
                  <?php else: ?>
                    <p class="card-category fw-bold mb-0">Hari ini belum mengisi jurnal.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Aktivitas</div>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="barChart"></canvas>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if($_SESSION['role']=="Pengawas") : ?>
    <div class="row">
      <div class="col-md-6">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row card-tools-still-right">
              <div class="card-title">Jurnal Terbaru</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button
                    class="btn btn-icon btn-clean me-0"
                    type="button"
                    id="dropdownMenuButton"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                  <div
                    class="dropdown-menu"
                    aria-labelledby="dropdownMenuButton"
                  >
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#"
                      >Something else here</a
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <!-- Projects table -->
              <table class="table align-items-center mb-0">
                <thead class="thead-light">
                  <tr>
                    <th scope="col" class="text-end">Date & Time</th>
                    <th scope="col" class="text-end">Kelas</th>
                    <th scope="col" class="text-end">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">X</td>
                    <td class="text-end">
                      <span class="badge badge-success">Valid</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">XI</td>
                    <td class="text-end">
                      <span class="badge badge-warning">Diproses</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">XII</td>
                    <td class="text-end">
                      <span class="badge badge-warning">Diproses</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">X</td>
                    <td class="text-end">
                      <span class="badge badge-success">Valid</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">X</td>
                    <td class="text-end">
                      <span class="badge badge-success">Valid</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Aktivitas Pengisian Jurnal</div>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="lineChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if($_SESSION['role']=="Admin") : ?>
    <div class="row">
     
             
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Aktivitas Pengisian Jurnal</div>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="pieChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php
$content = ob_get_clean();
ob_start();
?>
<script>
  // Line Chart untuk Guru (sama seperti Pengawas)
  <?php if($role == "Guru"): ?>
  var barChart = document.getElementById("barChart").getContext("2d");
  var myBarChart = new Chart(barChart, {
    type: "line",
    data: {
      labels: [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
      ],
      datasets: [
        {
          label: "Aktivitas Jurnal",
          borderColor: "#1d7af3",
          pointBorderColor: "#FFF",
          pointBackgroundColor: "#1d7af3",
          pointBorderWidth: 2,
          pointHoverRadius: 4,
          pointHoverBorderWidth: 1,
          pointRadius: 4,
          backgroundColor: "transparent",
          fill: true,
          borderWidth: 2,
          data: [
            <?php
              for ($i = 1; $i <= 12; $i++) {
                echo $aktivitas_per_bulan[$i];
                if ($i < 12) echo ',';
              }
            ?>
          ],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: "bottom",
        labels: {
          padding: 10,
          fontColor: "#1d7af3",
        },
      },
      tooltips: {
        bodySpacing: 4,
        mode: "nearest",
        intersect: 0,
        position: "nearest",
        xPadding: 10,
        yPadding: 10,
        caretPadding: 10,
      },
      layout: {
        padding: { left: 15, right: 15, top: 15, bottom: 15 },
      },
    },
  });
  <?php endif; ?>

  // Line Chart untuk Pengawas
  <?php if($role == "Pengawas"): ?>
  var lineChart = document.getElementById("lineChart").getContext("2d");
  var myLineChart = new Chart(lineChart, {
    type: "line",
    data: {
      labels: [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
      ],
      datasets: [
        {
          label: "Aktivitas Jurnal",
          borderColor: "#1d7af3",
          pointBorderColor: "#FFF",
          pointBackgroundColor: "#1d7af3",
          pointBorderWidth: 2,
          pointHoverRadius: 4,
          pointHoverBorderWidth: 1,
          pointRadius: 4,
          backgroundColor: "transparent",
          fill: true,
          borderWidth: 2,
          data: [
            <?php
              for ($i = 1; $i <= 12; $i++) {
                echo $aktivitas_per_bulan[$i];
                if ($i < 12) echo ',';
              }
            ?>
          ],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: "bottom",
        labels: {
          padding: 10,
          fontColor: "#1d7af3",
        },
      },
      tooltips: {
        bodySpacing: 4,
        mode: "nearest",
        intersect: 0,
        position: "nearest",
        xPadding: 10,
        yPadding: 10,
        caretPadding: 10,
      },
      layout: {
        padding: { left: 15, right: 15, top: 15, bottom: 15 },
      },
    },
  });
  <?php endif; ?>

  // Pie Chart untuk Admin
  <?php if($role == "Admin"): ?>
  var pieChart = document.getElementById("pieChart").getContext("2d");
  var myPieChart = new Chart(pieChart, {
    type: "pie",
    data: {
      datasets: [
        {
          data: [
            <?php
              // Menggunakan data aktivitas untuk pie chart
              $q1 = (isset($aktivitas_per_bulan[1]) ? $aktivitas_per_bulan[1] : 0) + 
                    (isset($aktivitas_per_bulan[2]) ? $aktivitas_per_bulan[2] : 0) + 
                    (isset($aktivitas_per_bulan[3]) ? $aktivitas_per_bulan[3] : 0);
              $q2 = (isset($aktivitas_per_bulan[4]) ? $aktivitas_per_bulan[4] : 0) + 
                    (isset($aktivitas_per_bulan[5]) ? $aktivitas_per_bulan[5] : 0) + 
                    (isset($aktivitas_per_bulan[6]) ? $aktivitas_per_bulan[6] : 0);
              $q3 = (isset($aktivitas_per_bulan[7]) ? $aktivitas_per_bulan[7] : 0) + 
                    (isset($aktivitas_per_bulan[8]) ? $aktivitas_per_bulan[8] : 0) + 
                    (isset($aktivitas_per_bulan[9]) ? $aktivitas_per_bulan[9] : 0);
              $q4 = (isset($aktivitas_per_bulan[10]) ? $aktivitas_per_bulan[10] : 0) + 
                    (isset($aktivitas_per_bulan[11]) ? $aktivitas_per_bulan[11] : 0) + 
                    (isset($aktivitas_per_bulan[12]) ? $aktivitas_per_bulan[12] : 0);
              
              // Jika semua data 0, berikan dummy data
              if ($q1 == 0 && $q2 == 0 && $q3 == 0 && $q4 == 0) {
                echo "25, 30, 20, 25";
              } else {
                echo "$q1, $q2, $q3, $q4";
              }
            ?>
          ],
          backgroundColor: ["#1d7af3", "#f3545d", "#fdaf4b", "#28a745"],
          borderWidth: 0,
        },
      ],
      labels: ["Q1 (Jan-Mar)", "Q2 (Apr-Jun)", "Q3 (Jul-Sep)", "Q4 (Oct-Des)"],
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
      tooltips: {
        enabled: true,
        callbacks: {
          label: function(tooltipItem, data) {
            var dataset = data.datasets[tooltipItem.datasetIndex];
            var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
              return previousValue + currentValue;
            });
            var currentValue = dataset.data[tooltipItem.index];
            var percentage = Math.floor(((currentValue/total) * 100)+0.5);
            return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + '%)';
          }
        }
      },
      layout: {
        padding: {
          left: 20,
          right: 20,
          top: 20,
          bottom: 20,
        },
      },
    },
  });
  <?php endif; ?>
</script>
<?php
$script = ob_get_clean();
include 'layout.php';
renderLayout($content, $script);
