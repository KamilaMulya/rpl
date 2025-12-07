<?php
session_start();
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'guru') {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$conn = new mysqli("localhost", "root", "", "ejurnalguru");
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT id_jurnal_guru, materi, tanggal FROM jurnal_guru WHERE id_jurnal_guru = ? AND id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $_SESSION['id_user']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Jurnal not found']);
    }
}

$conn->close();
?>