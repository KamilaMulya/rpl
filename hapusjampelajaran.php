<?php
    include 'koneksi.php';
    $id = $_GET['id_jam_pelajaran'];
    mysqli_query($koneksi, "DELETE FROM jam_pelajaran WHERE id_jam_pelajaran = $id");
    header("Location: datasekolah.php");
?>
