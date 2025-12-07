<?php
include 'koneksi.php';
$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM siswa WHERE id_siswa=$id");
header("Location: datasiswa.php");
?>
