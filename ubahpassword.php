<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$showAlert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_now = trim($_POST['password_now'] ?? '');
    $password_new = trim($_POST['password_new'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    // Ambil password lama dari database
    $sql = "SELECT password FROM user WHERE id_user = '$id_user'";
    $result = mysqli_query($koneksi, $sql);
    $data = mysqli_fetch_assoc($result);

    // Bandingkan langsung karena password TIDAK DIHASH
    if (!$data || $password_now !== $data['password']) {
        $showAlert = 'error_password';
    } elseif ($password_new !== $password_confirm) {
        $showAlert = 'mismatch';
    } else {
        // Simpan password baru langsung tanpa hash
        $update = "UPDATE user SET password='$password_new' WHERE id_user='$id_user'";
        if (mysqli_query($koneksi, $update)) {
            $showAlert = 'success';
        } else {
            $showAlert = 'error_db';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ganti Password</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f7f7f7;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        font-size: 14px;
        color: rgba(0, 0, 0, 0.58);
    }

    .form-container {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }

    h2 {
      margin-bottom: 20px;
      font-size: 28px;
      text-align: center;
      color: rgba(0, 0, 0, 0.77);
    }

    input[type="password"] {
      width: 94%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ddd;
      margin-bottom: 15px;
    }

    .button-group {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    button, .back-button {
      flex: 1;
      padding: 12px;
      background: #00c853;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
      display: inline-block;
    }

    button:hover {
      background: #00b34d;
    }

    .back-button {
      background: #9e9e9e;
    }

    .back-button:hover {
      background: #7e7e7e;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Ubah Password</h2>
  <form method="POST">
    <label for="password_now">Masukkan Password Sekarang</label>
    <input type="password" id="password_now" name="password_now" placeholder="Password" required>

    <label for="password_new">Masukkan Password Baru</label>
    <input type="password" id="password_new" name="password_new" placeholder="Password Baru" required>

    <label for="password_confirm">Verifikasi Password Baru</label>
    <input type="password" id="password_confirm" name="password_confirm" placeholder="Verifikasi Password" required>

    <div class="button-group">
      <button type="submit">Ubah</button>
      <a href="tampilanprofil.php" class="back-button">Kembali</a>
    </div>
  </form>
</div>

<script>
  <?php if ($showAlert === 'success'): ?>
    Swal.fire({
      title: "Berhasil!",
      text: "Password berhasil diubah.",
      icon: "success",
      showConfirmButton: false,
      timer: 2000
    }).then(() => {
      window.location.href = "tampilanprofil.php";
    });
  <?php elseif ($showAlert === 'error_password'): ?>
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Password lama tidak sesuai!",
    });
  <?php elseif ($showAlert === 'mismatch'): ?>
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Password baru dan konfirmasi tidak sama!",
    });
  <?php elseif ($showAlert === 'error_db'): ?>
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Gagal mengupdate password. Silakan coba lagi.",
    });
  <?php endif; ?>
</script>
</body>
</html>