<?php
$host = "localhost";
$dbname = "inventory_db";   // sesuaikan dengan nama database kamu
$user = "root";             // default XAMPP pakai root
$pass = "";                 // default XAMPP password kosong

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,          // error mode
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // hasil fetch jadi array asosiatif
        ]
    );
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
