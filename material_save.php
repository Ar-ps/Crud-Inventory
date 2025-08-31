<?php
include 'config.php';

$id        = $_POST['id'] ?? null;
$kode      = $_POST['kode'] ?? null;
$nama      = trim($_POST['nama'] ?? '');
$jumlah    = (int)($_POST['jumlah'] ?? 0);
$satuan    = trim($_POST['satuan'] ?? '');   // ✅ ambil satuan
$productId = $_POST['product_id'] ?? null;

// Validasi wajib
if (!$productId) {
    die("❌ Error: product_id wajib diisi.");
}
if (empty($nama) || empty($satuan)) {
    die("❌ Error: Nama dan satuan wajib diisi.");
}

// Validasi product_id ada di tabel products
$check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE id=?");
$check->execute([$productId]);
if ($check->fetchColumn() == 0) {
    die("❌ Error: Produk dengan ID $productId tidak ditemukan.");
}

if ($id) {
    // === UPDATE material ===
    if (empty($kode)) {
        $stmt = $pdo->prepare("SELECT kode FROM materials WHERE id=?");
        $stmt->execute([$id]);
        $kode = $stmt->fetchColumn();
    }

    $stmt = $pdo->prepare("UPDATE materials 
                           SET kode=?, nama=?, jumlah=?, satuan=?, product_id=? 
                           WHERE id=?");
    $stmt->execute([$kode, $nama, $jumlah, $satuan, $productId, $id]);

} else {
    // === INSERT material baru ===
    // Buat prefix singkatan dari nama bahan
    $prefix = '';
    if (!empty($nama)) {
        $words = preg_split('/\s+/', $nama);
        if (count($words) == 1) {
            $prefix = strtoupper(substr($words[0], 0, 3));
        } else {
            foreach ($words as $w) {
                if (!empty($w)) {
                    $prefix .= strtoupper(substr($w, 0, 1));
                }
            }
            $prefix = substr($prefix, 0, 3);
        }
    } else {
        $prefix = "MAT"; // default
    }

    // Cari kode terakhir dengan prefix sama
    $stmt = $pdo->prepare("SELECT kode 
                            FROM materials 
                            WHERE kode LIKE ? 
                            ORDER BY id DESC 
                            LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $lastKode = $stmt->fetchColumn();

    if ($lastKode) {
        $lastNum = (int)substr($lastKode, strlen($prefix));
        $nextNum = $lastNum + 1;
    } else {
        $nextNum = 1;
    }

    $kode = $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

    // Simpan data baru dengan satuan
    $stmt = $pdo->prepare("INSERT INTO materials (kode, nama, jumlah, satuan, product_id) 
                           VALUES (?,?,?,?,?)");
    $stmt->execute([$kode, $nama, $jumlah, $satuan, $productId]);
}

header("Location: materials.php?product_id=" . $productId);
exit;
