<?php
require 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $expression = trim($_POST['expression'] ?? '');

    // Gunakan regex untuk mendapatkan angka pertama, operator, dan angka kedua
    if (preg_match('/^(\d+)\s*([\+\-\*\/x])\s*(\d+)?$/', $expression, $matches)) {
        $firstNumber = (int)$matches[1];  // Ambil angka pertama
        $operator = $matches[2];          // Ambil operator
        $secondNumber = isset($matches[3]) ? (int)$matches[3] : null; // Ambil angka kedua jika ada

        // Normalisasi operator 'x' menjadi '*'
        if ($operator === 'x' || $operator === 'X') {
            $operator = '*';
        }

        // Jika angka kedua tidak ada, hentikan proses
        if ($secondNumber === null) {
            die(json_encode(["status" => "error", "message" => "Ekspresi tidak lengkap!"]));
        }

        // Hitung hasil berdasarkan operator
        switch ($operator) {
            case '+': $result = $firstNumber + $secondNumber; break;
            case '-': $result = $firstNumber - $secondNumber; break;
            case '*': $result = $firstNumber * $secondNumber; break;
            case '/': 
                $result = ($secondNumber != 0) ? (int) round($firstNumber / $secondNumber) : 0;
                break;
            default: 
                die(json_encode(["status" => "error", "message" => "Operator tidak valid"]));
        }

        // Simpan hanya operator ke kolom `expression`
        $stmt = $conn->prepare("INSERT INTO history (expression, first_numbers, second_numbers, result, createdAt) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("siii", $operator, $firstNumber, $secondNumber, $result);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Data berhasil disimpan!", "result" => $result]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Format input tidak valid!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid!"]);
}
?>
