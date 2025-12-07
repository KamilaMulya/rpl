<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
}

include "koneksi.php"; 
?>
        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Daftar Pengguna</h3>
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
                  <a href="#">Daftar Pengguna</a>
                </li>
              </ul>
            </div>
            <a href="tambahpengguna.php" class="tambah">➕ Tambah Pengguna</a>
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Aksi</th>
                </tr>
                <?php
                function roleText($role) {
                    return match ($role) {
                        '1' => 'Guru',
                        '2' => 'Pengawas',
                        '3' => 'Admin',
                        default => 'Tidak diketahui'
                    };
                }

                $result = mysqli_query($koneksi, "SELECT * FROM user");
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama_user']}</td>
                            <td>{$row['email']}</td>
                            <td>" . roleText($row['role']) . "</td>
                            <td class='aksi'>
                                <a class='edit' href='editpengguna.php?id={$row['id_user']}'>Edit</a>
                                <a class='hapus' href='hapuspengguna.php?id={$row['id_user']}' onclick=\"return confirm('Yakin ingin hapus?')\">Hapus</a>
                            </td>
                        </tr>";
                      $no++;
                }
                ?>
            </table>

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