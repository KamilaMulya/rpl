<?php
    include 'koneksi.php';
    $id = $_GET['id_tahun_ajaran'];
    mysqli_query($koneksi, "DELETE FROM tahun_ajaran WHERE id_tahun_ajaran=$id");
    header("Location: datasekolah.php");
?>
