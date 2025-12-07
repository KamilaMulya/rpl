<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
}
if (!isset($_POST['tahunajaran'])) {
  header('Location: laporan.php');
}
include_once "koneksi.php";

if(isset($_POST['submit'])) {
    $tahun = $_POST['tahunajaran'];
    $querytahun = mysqli_query($koneksi, "SELECT tahun FROM tahun_ajaran WHERE id_tahun_ajaran = $tahun");
    $hasil = mysqli_fetch_assoc($querytahun);
    $query = "
        SELECT 
            u.id_user,
            u.nama_user AS nama_guru,
            jg.id_jurnal_guru,
            jg.tanggal,
            jp.jam_mulai,
            k.id_kelas,
            k.nama_kelas,
            mp.id_mata_pelajaran,
            mp.nama_mata_pelajaran,
            jg.materi,
            jg.catatan,
            ta.tahun,
            jg.dokumentasi_path,
            jg.tanggal_validasi,
            jg.catatan_validasi,
            u_validasi.nama_user AS nama_validator
        FROM jurnal_guru jg
        JOIN user u ON jg.id_user = u.id_user
        JOIN jadwal_mengajar jm ON jg.id_jadwal_mengajar = jm.id_jadwal_mengajar
        JOIN kelas k ON jm.id_kelas = k.id_kelas
        JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
        JOIN jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
        JOIN tahun_ajaran ta ON jm.id_tahun_ajaran = ta.id_tahun_ajaran
        JOIN user u_validasi ON jg.use_id_user = u_validasi.id_user
        WHERE jm.id_tahun_ajaran = $tahun
          AND jg.status_validasi = '2'
        ORDER BY jg.tanggal ASC;
    ";

    $result = mysqli_query($koneksi, $query);

$data_jurnal = [];
$dataGuru = [];
$dataMapel = [];
$dataKelas = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_jurnal[] = $row;

        // Isi data guru unik
        $dataGuru[$row['id_user']] = $row['nama_guru'];
        // Isi data mapel unik
        $dataMapel[$row['id_mata_pelajaran']] = $row['nama_mata_pelajaran'];
        // Isi data kelas unik
        $dataKelas[$row['id_kelas']] = $row['nama_kelas'];
    }
}

$optionsGuru = '';
foreach ($dataGuru as $id => $nama) {
    $optionsGuru .= '<option value="' . htmlspecialchars($id) . '">' . htmlspecialchars($nama) . '</option>';
}
$optionsMapel = '';
foreach ($dataMapel as $id => $nama) {
    $optionsMapel .= '<option value="' . htmlspecialchars($id) . '">' . htmlspecialchars($nama) . '</option>';
}
$optionsKelas = '';
foreach ($dataKelas as $id => $nama) {
    $optionsKelas .= '<option value="' . htmlspecialchars($id) . '">' . htmlspecialchars($nama) . '</option>';
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
  JOIN tahun_ajaran ta ON jm.id_tahun_ajaran = ta.id_tahun_ajaran
  WHERE jg.tanggal BETWEEN 
    STR_TO_DATE(CONCAT(LEFT(ta.tahun, 4), '-07-01'), '%Y-%m-%d') AND 
    STR_TO_DATE(CONCAT(RIGHT(ta.tahun, 4), '-06-30'), '%Y-%m-%d')
  GROUP BY MONTH(jg.tanggal)
  ORDER BY bulan"
);

$labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$total = array_fill(0, 12, 0);
$tervalidasi = array_fill(0, 12, 0);
$belum_validasi = array_fill(0, 12, 0);

while ($row = mysqli_fetch_assoc($querychart)) {
    $index = (int)$row['bulan'] - 1;
    $total[$index] = (int)$row['total'];
    $tervalidasi[$index] = (int)$row['tervalidasi'];
    $belum_validasi[$index] = (int)$row['belum_validasi'];
}
?>
        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Laporan</h3>
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
                  <a href="laporan.php">Laporan</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Laporan Jurnal</a>
                </li>
              </ul>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Laporan Pengisian Jurnal Tahun Ajaran <?= $hasil['tahun'] ?></div>
                      <div class="card-tools">
                        <button class="btn btn-label-success btn-round btn-sm me-2 btn-export" data-tahun='<?= $tahun ?>'>
                          <span class="btn-label">
                            <i class="fa fa-pencil"></i>
                          </span>
                          Export
                        </button>
                        <button type='button' class="btn btn-label-info btn-round btn-sm btn-print" data-tahun='<?= $tahun ?>'>
                          <span class="btn-label">
                            <i class="fa fa-print"></i>
                          </span>
                          Print
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <p style="text-align: center;">Statistik Pengisian Jurnal</p>
                    <div class="chart-container">
                      <canvas id="multipleLineChart"></canvas>
                    </div><br>
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
                            <th>Catatan Jurnal</th>
                            <th>Total Siswa per Kelas</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Dokumentasi</th>
                            <th>Divalidasi Oleh</th>
                            <th>Tanggal Validasi</th>
                            <th>Catatan Validasi</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Jam Mulai</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Materi</th>
                            <th>Catatan Jurnal</th>
                            <th>Total Siswa per Kelas</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Tahun Ajaran</th>
                            <th>Divalidasi Oleh</th>
                            <th>Tanggal Validasi</th>
                            <th>Catatan Validasi</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php if(!empty($data_jurnal)): ?>
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
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['jam_mulai']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                            <td><?= htmlspecialchars($row['nama_mata_pelajaran']) ?></td>
                            <td><?= htmlspecialchars($row['materi']) ?></td>
                            <?php if ($row['catatan'] == ''): ?>
                              <td>-</td>
                            <?php else: ?>
                              <td><?= htmlspecialchars($row['catatan']) ?></td>
                            <?php endif; ?>
                            <td><span><?= $totalSiswa; ?></span></td>
                            <td><span><?= $rekap['sakit']; ?></span></td>
                            <td><span><?= $rekap['izin'] ?></span></td>
                            <td><span><?= $rekap['alpa']; ?></span></td>
                            <td><img src='uploads/dokumentasi/<?= $row['dokumentasi_path'] ?>' alt='<?= $row['dokumentasi_path'] ?>' height='100px'></td>
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
                            <td colspan="15" class="text-center">Tidak ada data.</td>
                          <?php endif; ?>
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
<script>
var SweetAlert2Demo = (function () {
  var initDemos = function () {
    $(".btn-print").click(function (e) {
      const tahun = $(this).data("tahun");

      // Form options sudah di-generate PHP dan di-inject ke variabel
        const optionsGuru = `<?= $optionsGuru ?>`;
        const optionsMapel = `<?= $optionsMapel ?>`;
        const optionsKelas = `<?= $optionsKelas ?>`;

      Swal.fire({
        title: "Isi Detail",
        html: `
          <div>
            <p>Print hanya bisa berdasarkan periode waktu, guru, mata pelajaran, dan kelas.</p>
            <div class="form-group">
              <label for="smallSelect1">Nama Guru</label>
              <select class="form-select form-control-sm .smallSelect1" id="smallSelect1">${optionsGuru}</select>
            </div>
            <div class="form-group">
              <label for="smallSelect2">Mata Pelajaran</label>
              <select class="form-select form-control-sm .smallSelect2" id="smallSelect2">${optionsMapel}</select>
            </div>
            <div class="form-group">
              <label for="smallSelect3">Kelas</label>
              <select class="form-select form-control-sm .smallSelect3" id="smallSelect3">${optionsKelas}</select>
            </div>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Cetak",
        cancelButtonText: "Batal",
        customClass: {
          confirmButton: "btn btn-success",
          cancelButton: "btn btn-danger"
        },
        buttonsStyling: false,
        didOpen: () => {
          const cancelBtn = Swal.getCancelButton();
          if (cancelBtn) cancelBtn.style.marginLeft = '10px';
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const guru = $("#smallSelect1").val();
          const mapel = $("#smallSelect2").val();
          const kelas = $("#smallSelect3").val();

          // Buat form dinamis
    const form = $('<form>', {
      action: 'print.php',
      method: 'POST',
      target: '_blank' // Boleh dihapus kalau tidak ingin buka tab baru
    }).append(
      $('<input>', { type: 'hidden', name: 'tahun', value: tahun }),
      $('<input>', { type: 'hidden', name: 'guru', value: guru }),
      $('<input>', { type: 'hidden', name: 'mapel', value: mapel }),
      $('<input>', { type: 'hidden', name: 'kelas', value: kelas })
    );

    $('body').append(form);
    form.submit();
    form.remove();
        }
      });
    });

    
    $(".btn-export").click(function (e) {
  const tahun = $(this).data("tahun");

  Swal.fire({
    title: "Export ke Excel?",
    text: "Data akan diekspor berdasarkan periode yang dipilih.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Export",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      const form = $('<form>', {
        action: 'export.php',
        method: 'POST',
        target: '_blank'
      }).append(
        $('<input>', { type: 'hidden', name: 'tahun', value: tahun })
      );

      $('body').append(form);
      form.submit();
      form.remove();
    }
  });
});

  };
  return {
    init: function () {
      initDemos();
    },
  };
})();

jQuery(document).ready(function () {
  SweetAlert2Demo.init();
});
</script>
<?php
$script = ob_get_clean();
include 'layout.php';
renderLayout($content, $script);