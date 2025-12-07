<?php
ob_start();
session_start();

$id_user = $_SESSION['user']['id_user'];

include "koneksi.php"; 
$sql = "SELECT nama_user, email, no_hp, password, role FROM user WHERE id_user='$id_user'";
$result = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($result);
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Profil Pengguna</h3>
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
          <a href="#">Profil</a>
        </li>
      </ul>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Informasi Pengguna</div>
          </div>
          <div class="card-body">
            <div class="text-center mb-4">
              <i class="fas fa-user-circle fa-5x"></i>
            </div>
            <table class="table table-borderless">
              
              <tr>
                <th>Nama</th>
                <td><?= htmlspecialchars($data['nama_user']) ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($data['email']) ?></td>
              </tr>
              <tr>
                <th>Password</th>
                <td><?= htmlspecialchars($data['password']) ?></td>
              </tr>
              <tr>
                <th>Role</th>
                <td>
  <?= $data['role'] == 1 ? 'Guru' : ($data['role'] == 2 ? 'Pengawas' : 'Admin') ?>
</td>
              </tr>
              <tr>
                <th>Telepon</th>
                <td><?= htmlspecialchars($data['no_hp']) ?></td>
              </tr>
            </table>
            <div class="text-center mt-4">
              <a href="index.php" class="btn btn-primary">Kembali</a>
               <a href="profile.php" class="btn btn-warning me-2">Edit Profil</a>
              <a href="ubahpassword.php" class="btn btn-secondary me-2">Ubah Password</a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php
$content = ob_get_clean();
ob_start();
?>
<!-- Tambahkan JavaScript khusus jika perlu -->
<?php
$script = ob_get_clean();
include 'layout.php';
renderLayout($content, $script);
