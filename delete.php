<?php
require 'connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['id'] ?? null;

if (!$itemId) {
    echo json_encode(["status" => "error", "message" => "ID tidak valid"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM histories WHERE id = ?");
$stmt->bind_param("i", $itemId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Item berhasil dihapus"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menghapus item"]);
}

$stmt->close();
$conn->close();
?>
