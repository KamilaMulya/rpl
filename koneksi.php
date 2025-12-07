<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ejurnalguru';

$koneksi = mysqli_connect($host, $username, $password, $database);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>