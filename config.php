<?php
$pdo = new PDO(
  "mysql:host=localhost;dbname=inventory_db;charset=utf8mb4",
  "root",   // user
  "",       // password XAMPP biasanya kosong
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]
);