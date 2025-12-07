<?php
session_start();
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'guru') {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$conn = new mysqli("localhost", "root", "", "ejurnalguru");
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $query = "DELETE FROM jurnal_guru WHERE id_jurnal_guru = ? AND id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $_SESSION['id_user']);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }
}

$conn->close();
?>