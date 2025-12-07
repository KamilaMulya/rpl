<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
}
include 'koneksi.php';

if (!isset($_GET['id_jadwal_mengajar'])) {
    die("ID tidak ditemukan.");
}

$id = intval($_GET['id_jadwal_mengajar']);
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM jadwal_mengajar WHERE id_jadwal_mengajar = $id"));

$kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
$mapel = mysqli_query($koneksi, "SELECT * FROM mata_pelajaran");
$guru  = mysqli_query($koneksi, "SELECT * FROM user WHERE role = 1");
$jam   = mysqli_query($koneksi, "SELECT * FROM jam_pelajaran");

if (isset($_POST['submit'])) {
    $id_kelas = $_POST['id_kelas'];
    $id_mata_pelajaran = $_POST['id_mata_pelajaran'];
    $id_user = $_POST['id_user'];
    $id_jam_pelajaran = $_POST['id_jam_pelajaran'];

    $update = mysqli_query($koneksi, "UPDATE jadwal_mengajar SET 
                id_kelas='$id_kelas', 
                id_mata_pelajaran='$id_mata_pelajaran', 
                id_user='$id_user', 
                id_jam_pelajaran='$id_jam_pelajaran' 
                WHERE id_jadwal_mengajar=$id");

    if ($update) {
        header("Location: datasekolah.php");
        exit;
    } else {
        echo "Gagal memperbarui data.";
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
          <a href="#">Edit Jadwal Mengajar</a>
        </li>
      </ul>
    </div>

    <form method="POST" style="max-width: 600px; margin: 20px auto;">
      <table>
        <tr><th colspan="2">Edit Jadwal Mengajar</th></tr>

        <tr>
          <td>Kelas</td>
          <td>
            <select name="id_kelas" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <?php while($k = mysqli_fetch_assoc($kelas)) {
                $sel = $k['id_kelas'] == $data['id_kelas'] ? 'selected' : '';
                echo "<option value='{$k['id_kelas']}' $sel>{$k['nama_kelas']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td>Mata Pelajaran</td>
          <td>
            <select name="id_mata_pelajaran" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <?php while($m = mysqli_fetch_assoc($mapel)) {
                $sel = $m['id_mata_pelajaran'] == $data['id_mata_pelajaran'] ? 'selected' : '';
                echo "<option value='{$m['id_mata_pelajaran']}' $sel>{$m['nama_mata_pelajaran']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td>Guru</td>
          <td>
            <select name="id_user" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <?php while($g = mysqli_fetch_assoc($guru)) {
                $sel = $g['id_user'] == $data['id_user'] ? 'selected' : '';
                echo "<option value='{$g['id_user']}' $sel>{$g['nama_user']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td>Jam Pelajaran</td>
          <td>
            <select name="id_jam_pelajaran" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <?php while($j = mysqli_fetch_assoc($jam)) {
                $sel = $j['id_jam_pelajaran'] == $data['id_jam_pelajaran'] ? 'selected' : '';
                echo "<option value='{$j['id_jam_pelajaran']}' $sel>{$j['hari']} - {$j['jam_mulai']} s/d {$j['jam_selesai']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td colspan="2" style="text-align:right;">
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
