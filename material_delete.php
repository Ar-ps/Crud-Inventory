<?php
include 'config.php';

$id = $_GET['id'] ?? 0;
$productId = $_GET['product_id'] ?? null; // tangkap product_id dari query string

// hapus data bahan
$stmt = $pdo->prepare("DELETE FROM materials WHERE id=?");
$stmt->execute([$id]);

// redirect kembali
if ($productId) {
    header("Location: materials.php?product_id=" . $productId);
} else {
    header("Location: materials.php");
}
exit;
