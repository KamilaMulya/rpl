<?php
$logout_done = false;

// Jalankan ini hanya jika user minta logout
if (isset($_GET['action']) && $_GET['action'] === 'doLogout') {
    session_start();
    session_unset();
    session_destroy();
    $logout_done = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Logout</title>

  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />

  <!-- SweetAlert -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f8f9fa;
    }

    .swal-button--confirm {
      background-color: #28a745 !important;
      color: white !important;
      font-weight: 600;
      border-radius: 5px;
      padding: 10px 20px;
    }

    .swal-button--cancel {
      background-color: #dc3545 !important;
      color: white !important;
      font-weight: 600;
      border-radius: 5px;
      padding: 10px 20px;
    }

    .swal-footer {
      display: flex !important;
      justify-content: center !important;
      gap: 15px;
    }
  </style>
</head>
<body>

<script>
$(document).ready(function() {
  <?php if (!$logout_done): ?>
    // Tampilkan konfirmasi logout dulu
    swal({
      title: "Apakah Anda yakin?",
      text: "Ingin keluar dari E-JurnalGuru?",
      icon: "warning",
      buttons: {
        cancel: {
          visible: true,
          text: "Tidak, batalkan",
          className: "swal-button--cancel"
        },
        confirm: {
          text: "Ya, keluar sekarang",
          className: "swal-button--confirm"
        }
      }
    }).then((willLogout) => {
      if (willLogout) {
        // Kalau user klik ya, redirect ke URL yang memproses session_destroy()
        window.location.href = 'logout.php?action=doLogout';
      } else {
        // Kalau batal, kembali ke homepage atau halaman lain
        window.location.href = 'index.php';
      }
    });
  <?php else: ?>
    // Setelah session dihapus, tampilkan pesan berhasil logout
    swal({
      title: "Berhasil keluar!",
      text: "Anda telah keluar dari E-JurnalGuru.",
      icon: "success",
      button: {
        text: "OK",
        className: "swal-button--confirm"
      }
    }).then(() => {
      window.location.href = 'login.php';
    });
  <?php endif; ?>
});
</script>

</body>
</html>
