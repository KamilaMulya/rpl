<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Pengawas") {
    header('Location: index.php');
}
include_once "koneksi.php";
$data_tahun = [];
$query = "SELECT * FROM tahun_ajaran;";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_tahun[] = $row;
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
                  <a href="#">Rekapitulasi</a>
                </li>
              </ul>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Tentukan Tahun Ajaran</h4>
                  </div>
                  <div class="card-body">
                    <div class="row">
                        <form method="post" action="rekap.php">
                            <div class="d-flex gap-2 w-75">
                                <select name="tahunajaran" id="tahunajaran" required>
                                  <option value="" disabled>Pilih Tahun Ajaran</option>
                                  <?php if(!empty($data_tahun)): ?>
                                  <?php foreach($data_tahun as $row): ?>
                                    <option value="<?= htmlspecialchars($row['id_tahun_ajaran']) ?>"><?= htmlspecialchars($row['tahun']) ?></option>   
                                  <?php endforeach; ?>
                                  <?php endif; ?>
                                </select>
                                <button name="submit" class="btn btn-success">Tampilkan</button>
                            </div> 
                        </form>
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
<?php
$script = ob_get_clean();
include 'layout.php';
renderLayout($content, $script);