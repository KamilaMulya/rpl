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
      <h3 class="fw-bold mb-3">Data Siswa</h3>
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
          <a href="#">Data Siswa</a>
        </li>
      </ul>
    </div>

    <a href="tambahsiswa.php" class="tambah">➕ Tambah Siswa</a>
    <table>
      <tr>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Jenis Kelamin</th>
        <th>Kelas</th>
        <th>Aksi</th>
      </tr>
      <?php
      // Fungsi untuk menampilkan teks jenis kelamin
      function jenisKelaminText($jk) {
          return $jk == 'L' ? 'Laki-laki' : 'Perempuan';
      }

      // Query join siswa dan kelas
      $query = "
        SELECT siswa.id_siswa, siswa.nama_siswa, siswa.jenis_kelamin, kelas.nama_kelas
        FROM siswa
        INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas
      ";
      $result = mysqli_query($koneksi, $query);
      $no = 1;
      while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['nama_siswa']}</td>
                  <td>" . jenisKelaminText($row['jenis_kelamin']) . "</td>
                  <td>{$row['nama_kelas']}</td>
                  <td class='aksi'>
                      <a class='edit' href='editsiswa.php?id={$row['id_siswa']}'>Edit</a>
                      <a class='hapus' href='hapussiswa.php?id={$row['id_siswa']}' onclick=\"return confirm('Yakin ingin hapus?')\">Hapus</a>
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
?>
