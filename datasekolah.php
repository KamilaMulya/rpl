<?php
ob_start();
session_start();
if ($_SESSION["role"] != "Admin") {
    header('Location: index.php');
}

include 'koneksi.php';

// Ambil data untuk ditampilkan
$query = "
    SELECT 
        jm.id_jadwal_mengajar,
        k.nama_kelas,
        mp.nama_mata_pelajaran,
        u.nama_user,
        jp.jam_mulai,
        jp.jam_selesai,
        jp.hari,
        ta.tahun
    FROM jadwal_mengajar jm
    JOIN kelas k ON jm.id_kelas = k.id_kelas
    JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
    JOIN user u ON jm.id_user = u.id_user
    JOIN jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
    JOIN tahun_ajaran ta ON jm.id_tahun_ajaran = ta.id_tahun_ajaran
    ORDER BY jp.hari, jp.jam_mulai
";
$result = mysqli_query($koneksi, $query);

$kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
$mapel = mysqli_query($koneksi, "SELECT * FROM mata_pelajaran");
$jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal_mengajar");
$jam = mysqli_query($koneksi, "SELECT * FROM jam_pelajaran");
$tahun = mysqli_query($koneksi, "SELECT * FROM tahun_ajaran");
?>

<div class="container">
  <div class="page-inner">
    <h3 class="fw-bold mb-4">Data Sekolah SMA</h3>

    <!-- Kelas -->
    <h4 style="text-align: center;">Kelas</h4>
    <a href="tambahkelas.php" class="tambah">➕ Tambah Kelas</a>
    <table>
      <tr>
        <th>ID</th>
        <th>Nama Kelas</th>
        <th>Tingkat</th>
        <th>Aksi</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($kelas)) { ?>
      <tr>
        <td><?= $row['id_kelas'] ?></td>
        <td><?= $row['nama_kelas'] ?></td>
        <td><?= $row['tingkat'] ?></td>
        <td class="aksi">
          <a href="editkelas.php?id_kelas=<?= $row['id_kelas'] ?>" class="edit">Edit</a>
          <a href="hapuskelas.php?id_kelas=<?= $row['id_kelas'] ?>" class="hapus" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
    </table>

    <!-- Mata Pelajaran -->
    <h4 style="text-align: center;">Mata Pelajaran</h4>
    <a href="tambahpelajaran.php" class="tambah">➕ Tambah Mata Pelajaran</a>
    <table>
      <tr>
        <th>ID</th>
        <th>Nama Mata Pelajaran</th>
        <th>Aksi</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($mapel)) { ?>
      <tr>
        <td><?= $row['id_mata_pelajaran'] ?></td>
        <td><?= $row['nama_mata_pelajaran'] ?></td>
        <td class="aksi">
          <a href="editpelajaran.php?id_mata_pelajaran=<?= $row['id_mata_pelajaran'] ?>" class="edit">Edit</a>
          <a href="hapuspelajaran.php?id_mata_pelajaran=<?= $row['id_mata_pelajaran'] ?>" class="hapus" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
    </table>

    <!-- Jadwal Mengajar -->
    <h4 style="text-align: center;">Jadwal Mengajar</h4>
    <a href="tambahjadwal.php" class="tambah">➕ Tambah Jadwal Mengajar</a>
    <table>
      <tr>
        <th>ID</th>
        <th>Kelas</th>
        <th>Mapel</th>
        <th>Guru</th>
        <th>Jam</th>
        <th>Hari</th>
        <th>Tahun Ajaran</th>
        <th>Aksi</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row['id_jadwal_mengajar'] ?></td>
        <td><?= $row['nama_kelas'] ?></td>
        <td><?= $row['nama_mata_pelajaran'] ?></td>
        <td><?= $row['nama_user'] ?></td>
        <td><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></td>
        <td><?= $row['hari'] ?></td>
        <td><?= $row['tahun'] ?></td>
        <td class="aksi">
          <a href="editjadwal.php?id_jadwal_mengajar=<?= $row['id_jadwal_mengajar'] ?>" class="edit">Edit</a>
          <a href="hapusjadwal.php?id_jadwal_mengajar=<?= $row['id_jadwal_mengajar'] ?>" class="hapus" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
    </table>

    <!-- Jam Pelajaran -->
    <h4 style="text-align: center;">Jam Pelajaran</h4>
    <a href="tambahjampelajaran.php" class="tambah">➕ Tambah Jam Pelajaran</a>
    <table>
      <tr>
        <th>ID</th>
        <th>Jam Mulai</th>
        <th>Jam Selesai</th>
        <th>Hari</th>
        <th>Aksi</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($jam)) { ?>
      <tr>
        <td><?= $row['id_jam_pelajaran'] ?></td>
        <td><?= $row['jam_mulai'] ?></td>
        <td><?= $row['jam_selesai'] ?></td>
        <td><?= $row['hari'] ?></td>
        <td class="aksi">
          <a href="editjampelajaran.php?id_jam_pelajaran=<?= $row['id_jam_pelajaran'] ?>" class="edit">Edit</a>
          <a href="hapusjampelajaran.php?id_jam_pelajaran=<?= $row['id_jam_pelajaran'] ?>" class="hapus" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
    </table>


    <!-- Tahun Ajaran -->
    <h4 style="text-align: center;">Tahun Ajaran</h4>
    <a href="tambahtahun.php" class="tambah">➕ Tambah Tahun Ajaran</a>
    <table>
      <tr>
        <th>ID</th>
        <th>Tahun Ajaran</th>
        <th>Aksi</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($tahun)) { ?>
      <tr>
        <td><?= $row['id_tahun_ajaran'] ?></td>
        <td><?= $row['tahun'] ?></td>
        <td class="aksi">
          <a href="edittahun.php?id_tahun_ajaran=<?= $row['id_tahun_ajaran'] ?>" class="edit">Edit</a>
          <a href="hapustahun.php?id_tahun_ajaran=<?= $row['id_tahun_ajaran'] ?>" class="hapus" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
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
