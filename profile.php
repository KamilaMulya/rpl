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
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email'] ?? ''));
    $no_hp = mysqli_real_escape_string($koneksi, trim($_POST['no_hp'] ?? ''));

    if ($nama === '' || $email === '' || $no_hp === '') {
        $showAlert = 'empty';
    } else {
        $sql = "UPDATE user 
                SET nama_user='$nama', email='$email', no_hp='$no_hp'
                WHERE id_user='$id_user'";
        $result = mysqli_query($koneksi, $sql);

        if ($result) {
            $showAlert = 'success';
        } else {
            $showAlert = 'error_db';
        }
    }
}

$sql = "SELECT nama_user, email, no_hp FROM user WHERE id_user='$id_user'";
$result = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Edit Profil</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f6f9fc;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .form-container {
      background-color: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 30px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
    }
    h2 {
      margin-bottom: 30px;
      font-weight: 600;
      color: #2e3a59;
      text-align: center;
    }
    label {
      display: block;
      margin-bottom: 6px;
      color: #2e3a59;
      font-weight: 500;
    }
    input[type="text"],
    input[type="email"] {
      width: 95%;
      padding: 12px;
      border: none;
      border-radius: 12px;
      background-color: #f9fafb;
      margin-bottom: 20px;
      box-shadow: 4px 4px 6px rgba(0,0,0,0.15);
      font-size: 14px;
    }
    select {
      width: 100%;
      padding: 12px;
      border-radius: 12px;
      background-color: #f9fafb;
      border: none;
      font-size: 14px;
      box-shadow: 4px 4px 6px rgba(0,0,0,0.15);
    }
    button {
      background-color: #66aaf9;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #4d97f0;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="form-container">
    <h2>Edit Profil</h2>
    <form method="POST" action="" onsubmit="return validatePhone()">
      <label for="nama">Nama:</label>
      <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama_user'] ?? '') ?>" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>

     <label for="no_hp">No HP:</label>
<input type="text" id="no_hp" name="no_hp"
       value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>"
       pattern="\d{12}" title="Isi nomor HP tepat 12 digit"
       required><br><br>

      
      <div style="display: flex; justify-content: space-between; gap: 10px;">
        <button type="submit" style="flex: 1;">Simpan Perubahan</button>
        <a href="tampilanprofil.php" style="flex: 1; text-align: center; background-color: #ccc; color: black; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; transition: background-color 0.3s ease;">Kembali</a>
      </div>

    </form>
  </div>

  <script>
    <?php if ($showAlert === 'success'): ?>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Profil berhasil diperbarui!',
        confirmButtonText: 'OK'
      }).then(() => {
        window.location.href = 'tampilanprofil.php';
      });
    <?php elseif ($showAlert === 'error_db'): ?>
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Terjadi kesalahan saat mengupdate profil.'
      });
    <?php elseif ($showAlert === 'empty'): ?>
      Swal.fire({
        icon: 'warning',
        title: 'Form Tidak Lengkap',
        text: 'Semua field wajib diisi!'
      });
    <?php endif; ?>
  </script>
  <script>
document.getElementById('no_hp').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 12); // hanya angka dan maksimal 12 digit
});
</script>
<script>
function validatePhone() {
    const noHp = document.getElementById('no_hp').value;
    if (!/^\d{12}$/.test(noHp)) {
        alert("Nomor HP harus tepat 12 digit angka.");
        return false;
    }
    return true;
}
</script>
</body>
</html>