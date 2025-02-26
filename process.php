<?php
require 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $expression = trim($_POST['expression'] ?? '');
    $originalExpression = $expression; // Simpan ekspresi asli sebelum diproses

    // Validasi ekspresi agar hanya berisi angka, desimal, negatif, persen, dan operator yang diperbolehkan
    if (!preg_match('/^-?[\d\s\+\-\*\/xX\.\%]+$/', $expression)) {
        die(json_encode(["status" => "error", "message" => "Ekspresi tidak valid!"]));
    }

    // Normalisasi operator 'x' menjadi '*'
    $expression = str_replace(['x', 'X'], '*', $expression);

    // Konversi persen (%) ke desimal
    $expression = preg_replace_callback('/(\d+(\.\d+)?)%/', function ($matches) {
        return ((float)$matches[1] / 100);
    }, $expression);

    // Hitung hasil ekspresi dengan eval()
    try {
        eval("\$result = $expression;");
    } catch (Throwable $e) {
        die(json_encode(["status" => "error", "message" => "Gagal menghitung ekspresi!"]));
    }

    // Pastikan hasil valid
    if (!isset($result) || !is_numeric($result)) {
        die(json_encode(["status" => "error", "message" => "Hasil tidak valid!"]));
    }

    // Batasi hasil ke 10 karakter pertama
    $result = substr((string) $result, 0, 10);

    // Simpan ekspresi lengkap ke database
    $stmt = $conn->prepare("INSERT INTO histories (expression, result, createdAt) VALUES (?, ?, NOW())");
    $stmt->bind_param("sd", $originalExpression, $result);

    if ($stmt->execute()) {
        $insertedId = $conn->insert_id;

        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil disimpan!",
            "result" => $result,
            "expression" => $originalExpression,
            "id" => $insertedId,
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
