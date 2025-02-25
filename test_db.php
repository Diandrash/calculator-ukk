<?php
$host = "localhost";
$user = "root"; // Sesuaikan dengan username MySQL
$password = ""; // Kosongkan jika pakai XAMPP
$database = "db_calculator";

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} else {
    echo "Koneksi ke database berhasil!";
}
?>
