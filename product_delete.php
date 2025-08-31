<?php
include 'config.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php");
