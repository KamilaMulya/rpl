<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
}
include 'koneksi.php';

// Validasi ID
if (!isset($_GET['id_tahun_ajaran'])) {
    die("Error: Tahun Ajaran tidak ditemukan.");
}

$id = intval($_GET['id_tahun_ajaran']);
$query = mysqli_query($koneksi, "SELECT * FROM tahun_ajaran WHERE id_tahun_ajaran = $id");

// Cek jika data tidak ditemukan
if (!$query || mysqli_num_rows($query) == 0) {
    die("Error: Data Tahun Ajaran tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);

// Proses saat form disubmit
if (isset($_POST['submit'])) {
    $tahun = $_POST['tahun'];

    $update = mysqli_query($koneksi, "UPDATE tahun_ajaran SET tahun='$tahun' WHERE id_tahun_ajaran = $id");

    if ($update) {
        header("Location: datasekolah.php");
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan perubahan.');</script>";
    }
}
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3"><a href="datasekolah.php" style="text-decoration: none; color: inherit;">Data Sekolah</a></h3>
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
          <a href="#">Edit Tahun Ajaran</a>
        </li>
      </ul>
    </div>

    <form method="POST" style="max-width: 600px; margin: 20px auto;">
      <table>
        <tr>
          <th colspan="2" style="text-align: left;">Edit Tahun Ajaran</th>
        </tr>
        <tr>
          <td style="width: 30%;"><label for="tahun">Tahun Ajaran</label></td>
          <td>
            <input type="text" name="tahun" id="tahun" value="<?= htmlspecialchars($data['tahun']) ?>" required
              style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
          </td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: right;">
            <button type="submit" name="submit" class="tambah">Simpan</button>
            <a href="datasekolah.php" class="batal">Batal</a>
          </td>
        </tr>
      </table>
    </form>
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
?>
