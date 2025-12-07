<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
    exit;
}

include "koneksi.php";

// Ambil ID siswa dari parameter
$id = $_GET['id'];

// Ambil data siswa dari database
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa = $id"));

// Tangani form submit
if (isset($_POST['submit'])) {
    $nama     = $_POST['nama_siswa'];
    $kelamin  = $_POST['jenis_kelamin'];
    $kelas_id = $_POST['id_kelas'];

    $query = "UPDATE siswa SET nama_siswa='$nama', jenis_kelamin='$kelamin', id_kelas='$kelas_id' WHERE id_siswa = $id";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header("Location: datasiswa.php");
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui siswa');</script>";
    }
}
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">
        <a href="datasiswa.php" style="text-decoration: none; color: inherit;">Daftar Siswa</a>
      </h3>
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
          <a href="#">Edit Siswa</a>
        </li>
      </ul>
    </div>

    <form method="POST" style="max-width: 600px; margin: 20px auto;">
      <table>
        <tr>
          <th colspan="2" style="text-align: left;">Edit Siswa</th>
        </tr>
        <tr>
          <td style="width: 30%;"><label for="nama_siswa">Nama Siswa</label></td>
          <td><input type="text" name="nama_siswa" id="nama_siswa" required value="<?= $data['nama_siswa'] ?>" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td><label for="jenis_kelamin">Jenis Kelamin</label></td>
          <td>
            <select name="jenis_kelamin" id="jenis_kelamin" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Jenis Kelamin --</option>
              <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
              <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="id_kelas">Kelas</label></td>
          <td>
            <select name="id_kelas" id="id_kelas" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Kelas --</option>
              <?php
              $kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
              while ($row = mysqli_fetch_assoc($kelas)) {
                  $selected = $row['id_kelas'] == $data['id_kelas'] ? 'selected' : '';
                  echo "<option value='{$row['id_kelas']}' $selected>{$row['nama_kelas']}</option>";
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: right;">
            <button type="submit" name="submit" class="tambah">Simpan</button>
            <a href="datasiswa.php" class="batal">Batal</a>
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
