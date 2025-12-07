<?php
// get_siswa_by_jadwal.php
session_start();

// Konfigurasi koneksi database
$conn = new mysqli("localhost", "root", "", "ejurnalguru");

// Cek koneksi
if ($conn->connect_error) {
    die(json_encode(['error' => "Koneksi gagal: " . $conn->connect_error]));
}

// Verifikasi peran pengguna (opsional, tapi disarankan untuk keamanan)
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'guru') {
    die(json_encode(['error' => 'Unauthorized access.']));
}

$id_jadwal_mengajar = $_GET['jadwal'] ?? null; // Ambil id_jadwal_mengajar dari parameter GET
$students = []; // Inisialisasi array untuk menyimpan data siswa

if ($id_jadwal_mengajar) {
    // Langkah 1: Ambil id_kelas dari tabel jadwal_mengajar berdasarkan id_jadwal_mengajar
    $query_get_kelas_id = "SELECT id_kelas FROM jadwal_mengajar WHERE id_jadwal_mengajar = ?";
    $stmt_get_kelas = $conn->prepare($query_get_kelas_id);
    $stmt_get_kelas->bind_param("i", $id_jadwal_mengajar);
    $stmt_get_kelas->execute();
    $result_get_kelas = $stmt_get_kelas->get_result();
    $kelas_data = $result_get_kelas->fetch_assoc();
    $stmt_get_kelas->close();

    if ($kelas_data) {
        $id_kelas = $kelas_data['id_kelas'];

        // Langkah 2: Ambil data siswa (id_siswa, nama_siswa, jenis_kelamin) berdasarkan id_kelas
        $query_siswa = "SELECT id_siswa, nama_siswa, jenis_kelamin FROM siswa WHERE id_kelas = ? ORDER BY nama_siswa";
        $stmt_siswa = $conn->prepare($query_siswa);
        $stmt_siswa->bind_param("i", $id_kelas);
        $stmt_siswa->execute();
        $result_siswa = $stmt_siswa->get_result();

        // Masukkan setiap baris siswa ke dalam array $students
        while ($row = $result_siswa->fetch_assoc()) {
            $students[] = $row;
        }
        $stmt_siswa->close(); // Tutup statement siswa
    }
}

$conn->close(); // Tutup koneksi database
header('Content-Type: application/json'); // Atur header respons sebagai JSON
echo json_encode($students); // Kembalikan data siswa dalam format JSON
?>