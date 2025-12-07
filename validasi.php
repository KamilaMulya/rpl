<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Pengawas") {
    header('Location: index.php');
}
include_once "koneksi.php";
?>
        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Validasi Jurnal</h3>
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
                  <a href="#">Validasi</a>
                </li>
              </ul>
            </div>
            <div class="row">
              <div class="col-12 col-lg-7">
                <div class="card card-stats card-warning card-round">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-2">
                        <div class="icon-big text-center">
                          <i class="fas fa-exclamation-triangle"></i>
                        </div>
                      </div>
                      <div class="col-10 col-stats">
                        <div class="numbers">
                          <h4 class="card-title">Perhatian!</h4>
                          <p class="card-category">Jurnal yang sudah divalidasi tidak dapat direvisi atau dihapus.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Daftar Jurnal yang Belum Tervalidasi</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover"
                      >
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <th>Mata Pelajaran</th>
                            <th>Jam</th>
                            <th>Materi</th>
                            <th>Catatan</th>
                            <th>Dokumentasi</th>
                            <th>Absensi</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <th>Mata Pelajaran</th>
                            <th>Jam</th>
                            <th>Materi</th>
                            <th>Catatan</th>
                            <th>Dokumentasi</th>
                            <th>Absensi</th>
                            <th>Aksi</th>
                          </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        $query = "SELECT 
                                      u.nama_user,
                                      k.nama_kelas,
                                      jm.id_kelas,
                                      jg.tanggal,
                                      jg.id_jurnal_guru,
                                      mp.nama_mata_pelajaran,
                                      jp.jam_mulai,
                                      jp.jam_selesai,
                                      jg.materi,
                                      jg.catatan,
                                      jg.dokumentasi_path
                                  FROM 
                                      jurnal_guru jg
                                  JOIN 
                                      jadwal_mengajar jm ON jg.id_jadwal_mengajar = jm.id_jadwal_mengajar
                                  JOIN 
                                      kelas k ON jm.id_kelas = k.id_kelas
                                  JOIN 
                                      mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
                                  JOIN 
                                      jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
                                  JOIN 
                                      user u ON jg.id_user = u.id_user
                                  WHERE status_validasi = 1
                                  ";
                          $result = mysqli_query($koneksi, $query);
                          if (mysqli_num_rows($result) > 0) {
                              $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $id_kelas = $row['id_kelas'];
        $id_jurnal = $row['id_jurnal_guru'];

        // Ambil semua siswa dalam kelas tersebut
        $listSiswa = [];
        $sqlSiswa = $koneksi->prepare("SELECT id_siswa, nama_siswa, jenis_kelamin FROM siswa WHERE id_kelas=? ORDER BY nama_siswa");
        $sqlSiswa->bind_param("i", $id_kelas);
        $sqlSiswa->execute();
        $resSiswa = $sqlSiswa->get_result();
        while($s = $resSiswa->fetch_assoc()) {
            $listSiswa[$s['id_siswa']] = ['nama' => $s['nama_siswa'], 'jk' => $s['jenis_kelamin']];
        }
        $sqlSiswa->close();

        $queryjumlahsiswa = "SELECT count(*) as total_siswa FROM siswa WHERE id_kelas = $id_kelas";
        $resultjumlahsiswa = mysqli_fetch_assoc(mysqli_query($koneksi, $queryjumlahsiswa));

        // Ambil absensi siswa (Sakit, Izin, Alpa)
        $absensiMap = [];
        $rekap = ['sakit'=>0, 'izin'=>0, 'alpa'=>0];
        $sqlAbs = $koneksi->prepare("SELECT id_siswa, status_kehadiran FROM absensi WHERE id_jurnal=?");
        $sqlAbs->bind_param("i", $id_jurnal);
        $sqlAbs->execute();
        $resAbs = $sqlAbs->get_result();
        while($abs = $resAbs->fetch_assoc()) {
            $absensiMap[$abs['id_siswa']] = $abs['status_kehadiran'];
            if (isset($rekap[$abs['status_kehadiran']])) {
                $rekap[$abs['status_kehadiran']]++;
            }
        }
        $sqlAbs->close();
                              echo "<tr>";
                              echo "<td>" . $no . "</td>";
                              echo "<td>" . $row["nama_user"] . "</td>";
                              echo "<td>" . $row["nama_kelas"] . "</td>";
                              echo "<td>" . $row["tanggal"] . "</td>";
                              echo "<td>" . $row["nama_mata_pelajaran"] . "</td>";
                              echo "<td>" . $row["jam_mulai"] . "-". $row["jam_selesai"] . "</td>";
                              echo "<td>" . $row["materi"] . "</td>";
                              echo "<td>" . $row["catatan"] . "</td>";
                              echo "<td><img src='uploads/dokumentasi/" . $row["dokumentasi_path"] . "' alt='" . $row["dokumentasi_path"] . "' height='100px'></td>";
                              echo "<td>";
if (count($listSiswa)) {
    $id_modal = "absensiDetailModal" . $row['id_jurnal_guru'];
    echo "<button class='btn btn-sm btn-info absensi-detail-btn' data-bs-toggle='modal' data-bs-target='#{$id_modal}'>Lihat</button>";

    // Modal HTML
    echo "<div class='modal fade' id='{$id_modal}' tabindex='-1'>
            <div class='modal-dialog modal-lg'>
              <div class='modal-content'>
                <div class='modal-header'>
                  <h5 class='modal-title'>Detail Absensi Siswa</h5>
                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                </div>
                <div class='modal-body'>
                  <p class='text-center'> Total Siswa : " . $resultjumlahsiswa['total_siswa'] . "</p>
                  <div class='table-responsive'>
                    <table class='table table-bordered table-sm'>
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Siswa</th>
                          <th>Jenis Kelamin</th>
                          <th>Status Kehadiran</th>
                        </tr>
                      </thead>
                      <tbody>";
    $noSiswa = 1;
    foreach ($listSiswa as $id_siswa => $data_siswa) {
        $status = isset($absensiMap[$id_siswa]) ? $absensiMap[$id_siswa] : 'hadir';
        $statusBadge = match ($status) {
            'hadir' => "<span class='badge bg-success'>Hadir</span>",
            'sakit' => "<span class='badge bg-warning text-dark'>Sakit</span>",
            'izin'  => "<span class='badge bg-info text-dark'>Izin</span>",
            'alpa'  => "<span class='badge bg-danger'>Alpa</span>",
            default => htmlspecialchars($status),
        };
        $jk = $data_siswa['jk'] == 'L' ? 'Laki-laki' : 'Perempuan';
        echo "<tr>
                <td>{$noSiswa}</td>
                <td>" . htmlspecialchars($data_siswa['nama']) . "</td>
                <td>{$jk}</td>
                <td>{$statusBadge}</td>
              </tr>";
        $noSiswa++;
    }
    echo "        </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>";
} else {
    echo "<span class='text-muted'>Tidak ada siswa</span>";
}
echo "</td>";
                              echo "<td>
                                      <div class='form-button-action'>
                                        <button
                                          type='button'
                                          data-bs-toggle='tooltip'
                                          title='Lihat Jurnal dan Validasi'
                                          class='btn btn-link btn-primary btn-lg btn-validasi'
                                          data-original-title='Validasi'
                                          data-jurnal='{$row["id_jurnal_guru"]}'
                                        >
                                          <i class='fa fa-edit'></i>
                                        </button>
                                      </div>
                                    </td>";
                              echo "</tr>";
                              $no += 1;
                          }
                      } else {
                          echo "<tr><td colspan='6' class='text-center'>Tidak ada data</td></tr>";
                      }
                      ?>
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
<style>
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
  .custom-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6);
  }

  .custom-modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    width: 80%;
    max-height: 80%;
    overflow-y: auto;
    border-radius: 8px;
    box-shadow: 0 0 10px #000;
  }

  .custom-modal-close {
    float: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
  }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      $(document).ready(function () {
        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            const api = this.api();
            const columnCount = api.columns().nodes().length;
                
            api.columns().every(function (index) {
              // Lewati kolom terakhir (index dimulai dari 0)
              if (index === columnCount - 1) return;
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
        //== Demos
        var initDemos = function () {
            $(".btn-validasi").click(function (e) {
              const jurnal = $(this).data("jurnal");
              Swal.fire({
                title: "Validasi Jurnal",
                html: `
                  <div>
                    <div class="form-group">
                      <div class="selectgroup selectgroup-pills">
                        <label class="selectgroup-item">
                          <input
                            type="checkbox"
                            id="check-validasi"
                            name="value"
                            value="Tervalidasi"
                            class="selectgroup-input"
                          />
                          <span class="selectgroup-button">Validasi</span>
                        </label>
                      </div>
                    </div>
                    <input class="form-control" placeholder="Catatan untuk guru pengajar..." id="input-catatan">
                  </div>
                `,
                showCancelButton: true,
                confirmButtonText: "Kirim",
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
                  const isValidated = $("#check-validasi").is(":checked");
                  const catatan = $("#input-catatan").val().trim();
                
                  if (!isValidated && catatan === "") {
                    Swal.fire("Gagal", "Centang validasi atau isi catatan terlebih dahulu.", "warning");
                    return;
                  }
                  // Kirim ke backend via AJAX
                  $.ajax({
                    url: "proses_validasi.php", // Ganti dengan endpoint Laravel-mu kalau perlu
                    type: "POST",
                    data: {
                      id_jurnal: jurnal,
                      status_validasi: isValidated ? 2 : 0,
                      catatan_validasi: catatan,
                      set_tanggal: isValidated ? 1 : 0,
                      set_user: isValidated ? 1 : 0
                    },
                    success: function (response) {
                      if(!isValidated){
                        Swal.fire("Terkirim", "Catatan telah dikirim!", "info").then(() => {
                          location.reload(); // Reload halaman untuk memperbarui tampilan
                        });
                      } else{
                        Swal.fire("Sukses", "Jurnal telah divalidasi!", "success").then(() => {
                          location.reload(); // Reload halaman untuk memperbarui tampilan
                        });
                      }
                    },
                    error: function () {
                      Swal.fire("Error", "Terjadi kesalahan saat mengirim data.", "error");
                    }
                  });
                }
              });
            });
        };
        return {
          //== Init
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