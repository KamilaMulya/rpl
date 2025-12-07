<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
    exit;
}

include 'koneksi.php';

if (!isset($_GET['id_jam_pelajaran'])) {
    die("ID tidak ditemukan.");
}

$id = intval($_GET['id_jam_pelajaran']);
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM jam_pelajaran WHERE id_jam_pelajaran = $id"));

if (isset($_POST['submit'])) {
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $hari = $_POST['hari'];

    $update = mysqli_query($koneksi, "UPDATE jam_pelajaran SET jam_mulai='$jam_mulai', jam_selesai='$jam_selesai', hari='$hari' WHERE id_jam_pelajaran=$id");

    if ($update) {
        header("Location: datasekolah.php");
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui jam pelajaran');</script>";
    }
}
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3"><a href="datasekolah.php" style="text-decoration: none; color: inherit;">Data Sekolah</a></h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="index.php"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Edit Jam Pelajaran</li>
      </ul>
    </div>

    <form method="POST" style="max-width: 600px; margin: 20px auto;">
        <table>
            <tr>
                <th colspan="2" style="text-align: left;">Edit Jam Pelajaran</th>
            </tr>
            <tr>
                <td style="width: 30%;"><label for="jam_mulai">Jam Mulai</label></td>
                <td><input type="time" name="jam_mulai" id="jam_mulai" value="<?= $data['jam_mulai'] ?>" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
            </tr>
            <tr>
                <td><label for="jam_selesai">Jam Selesai</label></td>
                <td><input type="time" name="jam_selesai" id="jam_selesai" value="<?= $data['jam_selesai'] ?>" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
            </tr>
            <tr>
                <td><label for="hari">Hari</label></td>
                <td>
                    <select name="hari" id="hari" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                        <option value="">-- Pilih Hari --</option>
                        <?php
                        $hari_opsi = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        foreach ($hari_opsi as $h) {
                            $selected = ($data['hari'] == $h) ? 'selected' : '';
                            echo "<option value='$h' $selected>$h</option>";
                        }
                        ?>
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
