<?php
include "connection.php"; // Koneksi database

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["expression"])) {
        die(json_encode(["status" => "error", "message" => "Data tidak lengkap"]));
    }

    $expression = trim($_POST["expression"]); // Hapus spasi di awal & akhir

    // Ekstraksi angka & operator menggunakan regex
    if (preg_match('/(\d+)\s*([\+\-\*/x])\s*(\d+)/', $expression, $matches)) {
        $first = (int)$matches[1];   // Bilangan pertama
        $operator = $matches[2];     // Operator
        $second = (int)$matches[3];  // Bilangan kedua

        // Ganti 'x' dengan '*' untuk operasi perkalian
        if ($operator === 'x') {
            $operator = '*';
        }

        // Hitung hasil berdasarkan operator
        switch ($operator) {
            case '+': $result = $first + $second; break;
            case '-': $result = $first - $second; break;
            case '*': $result = $first * $second; break;
            case '/': 
                if ($second == 0) {
                    die(json_encode(["status" => "error", "message" => "Error (div by 0)"]));
                }
                $result = (int)($first / $second); // Pastikan hasil tetap integer
                break;
            default:
                die(json_encode(["status" => "error", "message" => "Operator tidak valid"]));
        }

        // Gunakan prepared statement agar lebih aman
        $stmt = $conn->prepare("INSERT INTO history (first_numbers, second_numbers, expression, result) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            die(json_encode(["status" => "error", "message" => "Query gagal: " . $conn->error]));
        }

        // Bind parameter (i = integer, s = string)
        $stmt->bind_param("iisi", $first, $second, $expression, $result);

        // Eksekusi query
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "result" => $result]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan ke database: " . $stmt->error]);
        }

        // Tutup statement
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Format ekspresi tidak valid"]);
    }

    // Tutup koneksi database
    $conn->close();
}
