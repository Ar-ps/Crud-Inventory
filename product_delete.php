<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$id]);

    $_SESSION['flash_success'] = "Produk berhasil dihapus.";
} else {
    $_SESSION['flash_error'] = "Produk tidak ditemukan.";
}

header("Location: index.php");
exit;
