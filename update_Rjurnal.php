<?php
session_start();
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'guru') {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$conn = new mysqli("localhost", "root", "", "ejurnalguru");
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

if (isset($_POST['id_jurnal']) && isset($_POST['materi']) && isset($_POST['tanggal'])) {
    $id_jurnal = intval($_POST['id_jurnal']);
    $materi = $_POST['materi'];
    $tanggal = $_POST['tanggal'];
    
    $query = "UPDATE jurnal_guru SET materi = ?, tanggal = ? WHERE id_jurnal_guru = ? AND id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $materi, $tanggal, $id_jurnal, $_SESSION['id_user']);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
}

$conn->close();
?>