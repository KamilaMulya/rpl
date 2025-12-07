<?php
    include 'koneksi.php';
    $id = $_GET['id_mata_pelajaran'];
    mysqli_query($koneksi, "DELETE FROM mata_pelajaran WHERE id_mata_pelajaran=$id");
    header("Location: datasekolah.php");
?>
