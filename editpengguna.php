<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
    exit;
}

include "koneksi.php";

// Ambil data berdasarkan ID
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id"));

// Tangani form submit
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    $no_hp = $_POST['no_hp'];

    $update = mysqli_query($koneksi, "UPDATE user SET nama_user='$nama', email='$email', password='$pass', role='$role' , no_hp='$no_hp' WHERE id_user=$id");

    if ($update) {
        header("Location: daftarpengguna.php");
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui pengguna');</script>";
    }
}
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">
        <a href="daftarpengguna.php" style="text-decoration: none; color: inherit;">Daftar Pengguna</a>
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
          <a href="#">Edit Pengguna</a>
        </li>
      </ul>
    </div>

    <form method="POST" style="max-width: 600px; margin: 20px auto;">
      <table>
        <tr>
          <th colspan="2" style="text-align: left;">Form Edit Pengguna</th>
        </tr>
        <tr>
          <td style="width: 30%;"><label for="nama">Nama</label></td>
          <td><input type="text" name="nama" id="nama" required value="<?= $data['nama_user'] ?>" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td><label for="email">Email</label></td>
          <td><input type="email" name="email" id="email" required value="<?= $data['email'] ?>" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td><label for="password">Password</label></td>
          <td><input type="text" name="password" id="password" required value="<?= $data['password'] ?>" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td><label for="role">Peran</label></td>
          <td>
            <select name="role" id="role" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option disabled>-- Pilih Peran --</option>
              <option value="1" <?= $data['role'] == '1' ? 'selected' : '' ?>>Guru</option>
              <option value="2" <?= $data['role'] == '2' ? 'selected' : '' ?>>Pengawas</option>
              <option value="3" <?= $data['role'] == '3' ? 'selected' : '' ?>>Admin</option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="no_hp">No Hp</label></td>
          <td><input type="text" name="no_hp" id="no_hp" required value="<?= $data['no_hp'] ?>" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: right;">
            <button type="submit" name="submit" class="tambah">Simpan</button>
            <a href="daftarpengguna.php" class="batal">Batal</a>
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
