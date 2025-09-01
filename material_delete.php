<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

$id        = $_GET['id'] ?? null;
$productId = $_GET['product_id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM materials WHERE id=?");
    $stmt->execute([$id]);

    $_SESSION['flash_success'] = "Bahan berhasil dihapus.";
} else {
    $_SESSION['flash_error'] = "Gagal menghapus bahan.";
}

header("Location: materials.php?product_id=$productId");
exit;
