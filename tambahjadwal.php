<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
    exit;
}

include 'koneksi.php';

// Tangani form submit
if (isset($_POST['submit'])) {
    $id_kelas          = $_POST['id_kelas'];
    $id_mata_pelajaran = $_POST['id_mata_pelajaran'];
    $id_user           = $_POST['id_user'];
    $id_jam_pelajaran  = $_POST['id_jam_pelajaran'];
    $id_tahun_ajaran   = $_POST['id_tahun_ajaran'];

    $query = "INSERT INTO jadwal_mengajar (id_kelas, id_mata_pelajaran, id_user, id_jam_pelajaran, id_tahun_ajaran)
              VALUES ('$id_kelas', '$id_mata_pelajaran', '$id_user', '$id_jam_pelajaran', '$id_tahun_ajaran')";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header("Location: datasekolah.php");
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan jadwal');</script>";
    }
}

// Ambil data referensi untuk dropdown
$kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
$mapel = mysqli_query($koneksi, "SELECT * FROM mata_pelajaran");
$guru  = mysqli_query($koneksi, "SELECT * FROM user WHERE role = 1");
$jam   = mysqli_query($koneksi, "SELECT * FROM jam_pelajaran");
$tahun = mysqli_query($koneksi, "SELECT * FROM tahun_ajaran");
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">
        <a href="datasekolah.php" style="text-decoration: none; color: inherit;">Data Sekolah</a>
      </h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="index.php"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Tambah Jadwal Mengajar</li>
      </ul>
    </div>

    <form method="POST" style="max-width: 700px; margin: 20px auto;">
      <table>
        <tr>
          <th colspan="2" style="text-align: left;">Tambah Jadwal Mengajar</th>
        </tr>

        <tr>
          <td style="width: 30%;"><label for="id_kelas">Kelas</label></td>
          <td>
            <select name="id_kelas" id="id_kelas" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Kelas --</option>
              <?php while($k = mysqli_fetch_assoc($kelas)) {
                echo "<option value='{$k['id_kelas']}'>{$k['nama_kelas']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td><label for="id_mata_pelajaran">Mata Pelajaran</label></td>
          <td>
            <select name="id_mata_pelajaran" id="id_mata_pelajaran" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Mapel --</option>
              <?php while($m = mysqli_fetch_assoc($mapel)) {
                echo "<option value='{$m['id_mata_pelajaran']}'>{$m['nama_mata_pelajaran']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td><label for="id_user">Guru</label></td>
          <td>
            <select name="id_user" id="id_user" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Guru --</option>
              <?php while($g = mysqli_fetch_assoc($guru)) {
                echo "<option value='{$g['id_user']}'>{$g['nama_user']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td><label for="id_jam_pelajaran">Jam Pelajaran</label></td>
          <td>
            <select name="id_jam_pelajaran" id="id_jam_pelajaran" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Jam --</option>
              <?php while($j = mysqli_fetch_assoc($jam)) {
                echo "<option value='{$j['id_jam_pelajaran']}'>{$j['hari']} - {$j['jam_mulai']} s/d {$j['jam_selesai']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td><label for="id_tahun_ajaran">Tahun Ajaran</label></td>
          <td>
            <select name="id_tahun_ajaran" id="id_tahun_ajaran" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Tahun Ajaran --</option>
              <?php while($t = mysqli_fetch_assoc($tahun)) {
                echo "<option value='{$t['id_tahun_ajaran']}'>{$t['tahun']}</option>";
              } ?>
            </select>
          </td>
        </tr>

        <tr>
          <td colspan="2" style="text-align: right; padding-top: 10px;">
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
ob_start(); // jika ada script tambahan
?>
<?php
$script = ob_get_clean();
include 'layout.php';
renderLayout($content, $script);
?>
