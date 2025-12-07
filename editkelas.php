<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
}
include 'koneksi.php';

if (!isset($_GET['id_kelas'])) {
    die("Error: ID Kelas tidak ditemukan.");
}

$id = intval($_GET['id_kelas']);
$query = mysqli_query($koneksi, "SELECT * FROM kelas WHERE id_kelas = $id");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Error: Data kelas tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);

// Proses saat form disubmit
if (isset($_POST['submit'])) {
    $nama = $_POST['nama_kelas'];
    $tingkat = $_POST['tingkat'];

    $update = mysqli_query($koneksi, "UPDATE kelas SET nama_kelas='$nama', tingkat='$tingkat' WHERE id_kelas = $id");

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
          <a href="#">Edit Kelas</a>
        </li>
      </ul>
    </div>

    <!-- Form Edit -->
    <form method="POST" style="max-width: 600px; margin: 20px auto;">
        <table>
            <tr>
                <th colspan="2" style="text-align: left;">Edit Kelas</th>
            </tr>
            <tr>
                <td style="width: 30%;"><label for="nama_kelas">Nama Kelas</label></td>
                <td><input type="text" name="nama_kelas" id="nama_kelas" value="<?= $data['nama_kelas'] ?>" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
            </tr>
            <tr>
                <td><label for="tingkat">Tingkat</label></td>
                <td>
                    <select name="tingkat" id="tingkat" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="10" <?= $data['tingkat'] == '10' ? 'selected' : '' ?>>10</option>
                        <option value="11" <?= $data['tingkat'] == '11' ? 'selected' : '' ?>>11</option>
                        <option value="12" <?= $data['tingkat'] == '12' ? 'selected' : '' ?>>12</option>
                    </select>
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
