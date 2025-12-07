<?php
    include 'koneksi.php';
    $id = $_GET['id_jadwal_mengajar'];
    mysqli_query($koneksi, "DELETE FROM jadwal_mengajar WHERE id_jadwal_mengajar = $id");
    header("Location: datasekolah.php");
?>