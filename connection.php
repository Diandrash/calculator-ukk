<?php
$host = "localhost";
$user = "root";  
$pass = "";      
$dbname = "db_calculator";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(`Connection lost: ` . $conn->connect_error);
}

