<?php
    include 'koneksi.php';
    $id = $_GET['id_kelas'];
    mysqli_query($koneksi, "DELETE FROM kelas WHERE id_kelas=$id");
    header("Location: datasekolah.php");
?>
