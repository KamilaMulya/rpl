<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
    exit;
}

include "koneksi.php";

// Tangani form submit
if (isset($_POST['submit'])) {
    $nama     = $_POST['nama_user'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $no_hp    = $_POST['no_hp'];

    $query = "INSERT INTO user (nama_user, email, password, role, no_hp) VALUES ('$nama', '$email', '$password', '$role', '$no_hp')";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header("Location: daftarpengguna.php");
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan pengguna');</script>";
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
          <a href="#">Tambah Pengguna</a>
        </li>
      </ul>
    </div>
    <form method="POST" style="max-width: 600px; margin: 20px auto;">
      <table>
        <tr>
          <th colspan="2" style="text-align: left;">Tambah Pengguna</th>
        </tr>
        <tr>
          <td style="width: 30%;"><label for="nama_user">Nama</label></td>
          <td><input type="text" name="nama_user" id="nama_user" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td><label for="email">Email</label></td>
          <td><input type="email" name="email" id="email" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td><label for="password">Password</label></td>
          <td><input type="password" name="password" id="password" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
        </tr>
        <tr>
          <td><label for="role">Peran</label></td>
          <td>
            <select name="role" id="role" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
              <option value="">-- Pilih Peran --</option>
              <option value="1">Guru</option>
              <option value="2">Pengawas</option>
              <option value="3">Admin</option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="no_hp">NO HP</label></td>
          <td><input type="text" name="no_hp" id="no_hp" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></td>
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
