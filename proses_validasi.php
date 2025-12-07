<?php
include_once("koneksi.php");
session_start();

// Ambil data dari POST
$id_jurnal        = mysqli_real_escape_string($koneksi, $_POST['id_jurnal']);
$status_validasi  = mysqli_real_escape_string($koneksi, $_POST['status_validasi']);
$catatan_validasi = mysqli_real_escape_string($koneksi, $_POST['catatan_validasi']);
$set_tanggal      = isset($_POST['set_tanggal']) ? $_POST['set_tanggal'] : 0;
$set_user         = isset($_POST['set_user']) ? $_POST['set_user'] : 0;

// Ambil tanggal dan user jika diperlukan
$tanggal_validasi = ($set_tanggal == 1) ? date('Y-m-d H:i:s') : null;
$id_user = ($set_user == 1 && isset($_SESSION['user']['id_user'])) ? $_SESSION['user']['id_user'] : null;

// Persiapan query
$sql = "";
if ($status_validasi == "2") { // gunakan string untuk perbandingan aman (semua input POST berbentuk string)
    $sql = "UPDATE jurnal_guru SET 
                status_validasi = '2',
                tanggal_validasi = '$tanggal_validasi',
                use_id_user = '$id_user'";

    if (!empty($catatan_validasi)) {
        $sql .= ", catatan_validasi = '$catatan_validasi'";
    }

    $sql .= " WHERE id_jurnal_guru = '$id_jurnal'";
} else {
    if (!empty($catatan_validasi)) {
        $sql = "UPDATE jurnal_guru SET 
                    catatan_validasi = '$catatan_validasi'
                WHERE id_jurnal_guru = '$id_jurnal'";
    }
}

// Jalankan query jika $sql tidak kosong
if (!empty($sql)) {
    $hasil = mysqli_query($koneksi, $sql);
    if ($hasil) {
        echo "sukses";
    } else {
        echo "gagal: " . mysqli_error($koneksi);
    }
} else {
    echo "tidak ada perubahan";
}

// Tutup koneksi
mysqli_close($koneksi);
?>