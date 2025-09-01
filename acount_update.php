<?php
session_start();
include 'config.php';

$userId   = $_SESSION['user_id'] ?? null;
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (!$userId) {
  die("❌ Anda harus login dulu.");
}

if (empty($username)) {
  die("❌ Username wajib diisi.");
}

try {
  if (!empty($password)) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET username=?, password=? WHERE id=?");
    $stmt->execute([$username, $hash, $userId]);
  } else {
    $stmt = $pdo->prepare("UPDATE users SET username=? WHERE id=?");
    $stmt->execute([$username, $userId]);
  }

  $_SESSION['username'] = $username;
  $_SESSION['flash_success'] = "Akun berhasil diperbarui!";
  header("Location: acount.php");
  exit;
} catch (Exception $e) {
  $_SESSION['flash_error'] = "Error: " . $e->getMessage();
  header("Location: acount.php");
  exit;
}
