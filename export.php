<?php
include_once "koneksi.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=jurnal_guru_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$data_jurnal = [];
$tahun = $_POST['tahun'];
$querytahun = mysqli_query($koneksi, "SELECT tahun FROM tahun_ajaran WHERE id_tahun_ajaran = $tahun");
$hasil = mysqli_fetch_assoc($querytahun);
$query = "
     SELECT 
            u.nama_user AS nama_guru,
            jg.tanggal,
            jg.id_jurnal_guru,
            jp.jam_mulai,
            k.id_kelas,
            k.nama_kelas,
            mp.nama_mata_pelajaran,
            jg.materi,
            jg.catatan,
            jg.tanggal_validasi,
            jg.catatan_validasi,
            u_validasi.nama_user AS nama_validator
        FROM jurnal_guru jg
        JOIN user u ON jg.id_user = u.id_user
        JOIN jadwal_mengajar jm ON jg.id_jadwal_mengajar = jm.id_jadwal_mengajar
        JOIN kelas k ON jm.id_kelas = k.id_kelas
        JOIN mata_pelajaran mp ON jm.id_mata_pelajaran = mp.id_mata_pelajaran
        JOIN jam_pelajaran jp ON jm.id_jam_pelajaran = jp.id_jam_pelajaran
        JOIN user u_validasi ON jg.use_id_user = u_validasi.id_user
        WHERE jm.id_tahun_ajaran = $tahun
          AND jg.status_validasi = '2'
        ORDER BY jg.tanggal ASC;
";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_jurnal[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <body>
    <table>
      <thead>
        <tr>
          <th colspan="14">JURNAL MENGAJAR GURU</th>
        </tr>
        <tr>
          <th colspan="14" class="text-center">TAHUN AJARAN <?= !empty($_POST['tahun']) ? $hasil['tahun'] : "-" ?></th>
        </tr>
        <tr>
          <th rowspan="2" style="border: solid">Nama</th>
          <th rowspan="2" style="border: solid">Tanggal</th>
          <th rowspan="2" style="border: solid">Jam Mulai</th>
          <th rowspan="2" style="border: solid">Kelas</th>
          <th rowspan="2" style="border: solid">Mata Pelajaran</th>
          <th rowspan="2" style="border: solid">Materi</th>
          <th colspan="4" style="border: solid">Jumlah Siswa</th>
          <th rowspan="2" style="border: solid">Catatan Jurnal</th>
          <th rowspan="2" style="border: solid">Divalidasi Oleh</th>
          <th rowspan="2" style="border: solid">Tanggal Validasi</th>
          <th rowspan="2" style="border: solid">Catatan Validasi</th>
        </tr>
        <tr>
          <th style="border: solid">S</th>
          <th style="border: solid">I</th>
          <th style="border: solid">A</th>
          <th style="border: solid">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($data_jurnal)): ?>
        <?php foreach($data_jurnal as $row): ?>
        <tr>
          <?php 
          $idjurnalguru = $row['id_jurnal_guru'];
          $idkelas = $row['id_kelas'];
          $queryabsen = "
            SELECT
              SUM(CASE WHEN a.status_kehadiran = 'sakit' THEN 1 ELSE 0 END) AS sakit,
              SUM(CASE WHEN a.status_kehadiran = 'izin' THEN 1 ELSE 0 END) AS izin,
              SUM(CASE WHEN a.status_kehadiran = 'alpa' THEN 1 ELSE 0 END) AS alpa,
              COUNT(s.id_siswa) AS total
            FROM absensi a
            JOIN siswa s ON a.id_siswa = s.id_siswa
            WHERE a.id_jurnal = '$idjurnalguru' AND s.id_kelas = '$idkelas'
          ";
          $resultAbsen = mysqli_query($koneksi, $queryabsen);
          $dataAbsen = mysqli_fetch_assoc($resultAbsen);
          $queryTotal = "SELECT COUNT(*) as total FROM siswa WHERE id_kelas = '$idkelas'";
          $resultTotal = mysqli_query($koneksi, $queryTotal);
          $dataTotal = mysqli_fetch_assoc($resultTotal);
          ?>
          <td style="border: solid"><?= htmlspecialchars($row['nama_guru']) ?></td>
          <td style="border: solid"><?= htmlspecialchars($row['tanggal']) ?></td>
          <td style="border: solid"><?= htmlspecialchars($row['jam_mulai']) ?></td>
          <td style="border: solid"><?= htmlspecialchars($row['nama_kelas']) ?></td>
          <td style="border: solid"><?= htmlspecialchars($row['nama_mata_pelajaran']) ?></td>
          <td style="border: solid"><?= htmlspecialchars($row['materi']) ?></td>
          <?php if ($dataAbsen['sakit'] == '0' || $dataAbsen['sakit'] == ''): ?>
            <td style="border: solid">-</td>
          <?php else: ?>
            <td style="border: solid"><?= htmlspecialchars($dataAbsen['sakit']) ?></td>
          <?php endif; ?>
          <?php if ($dataAbsen['izin'] == '0' || $dataAbsen['izin'] == ''): ?>
            <td style="border: solid">-</td>
          <?php else: ?>
            <td style="border: solid"><?= htmlspecialchars($dataAbsen['izin']) ?></td>
          <?php endif; ?>
          <?php if ($dataAbsen['alpa'] == '0' || $dataAbsen['alpa'] == ''): ?>
            <td style="border: solid">-</td>
          <?php else: ?>
            <td style="border: solid"><?= htmlspecialchars($dataAbsen['alpa']) ?></td>
          <?php endif; ?>
          <?php if ($dataTotal['total'] == '0' || $dataTotal['total'] == ''): ?>
            <td style="border: solid">-</td>
          <?php else: ?>
            <td style="border: solid"><?= htmlspecialchars($dataTotal['total']) ?></td>
          <?php endif; ?>
          <?php if ($row['catatan'] == ''): ?>
            <td style="border: solid">-</td>
          <?php else: ?>
            <td style="border: solid"><?= htmlspecialchars($row['catatan']) ?></td>
          <?php endif; ?>
          <td style="border: solid"><?= htmlspecialchars($row['nama_validator']) ?></td>
          <td style="border: solid"><?= htmlspecialchars($row['tanggal_validasi']) ?></td>
          <?php if ($row['catatan_validasi'] == ''): ?>
            <td style="border: solid">-</td>
          <?php else: ?>
            <td style="border: solid"><?= htmlspecialchars($row['catatan_validasi']) ?></td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
          <td style="border: solid" colspan="14" class="text-center">Tidak ada data.</td>
        <?php endif; ?>
      </tbody>
    </table>
  </body>
</html>