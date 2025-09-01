<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

$id        = $_POST['id'] ?? null;
$kode      = $_POST['kode'] ?? null;
$nama      = trim($_POST['nama'] ?? '');
$jumlah    = (int)($_POST['jumlah'] ?? 0);
$satuan    = trim($_POST['satuan'] ?? '');
$productId = $_POST['product_id'] ?? null;

// simpan input lama supaya form tetap terisi jika error
$_SESSION['old_input'] = [
    "id"         => $id,
    "kode"       => $kode,
    "nama"       => $nama,
    "jumlah"     => $jumlah,
    "satuan"     => $satuan,
    "product_id" => $productId
];

// === Validasi dasar ===
if (!$productId) {
    $_SESSION['flash_error'] = "❌ Error: product_id wajib diisi.";
    header("Location: materials.php");
    exit;
}
if (empty($nama) || empty($satuan)) {
    $_SESSION['flash_error'] = "❌ Error: Nama dan satuan wajib diisi.";
    $redirect = $id 
        ? "material_form.php?id=$id&product_id=$productId" 
        : "material_form.php?product_id=$productId";
    header("Location: $redirect");
    exit;
}

// Pastikan product_id valid + ambil kode produk
$stmt = $pdo->prepare("SELECT kode FROM products WHERE id=?");
$stmt->execute([$productId]);
$productKode = $stmt->fetchColumn();

if (!$productKode) {
    $_SESSION['flash_error'] = "❌ Error: Produk dengan ID $productId tidak ditemukan.";
    header("Location: materials.php");
    exit;
}

try {
    if ($id) {
        // === UPDATE MATERIAL ===
        if (empty($kode)) {
            $stmt = $pdo->prepare("SELECT kode FROM materials WHERE id=?");
            $stmt->execute([$id]);
            $kode = $stmt->fetchColumn();
        }

        // cek duplikat kode
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM materials WHERE kode=? AND id<>?");
        $stmt->execute([$kode, $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("⚠️ Kode $kode sudah digunakan bahan lain!");
        }

        $stmt = $pdo->prepare("UPDATE materials 
                               SET kode=?, nama=?, jumlah=?, satuan=?, product_id=? 
                               WHERE id=?");
        $stmt->execute([$kode, $nama, $jumlah, $satuan, $productId, $id]);

        unset($_SESSION['old_input']);
        $_SESSION['flash_success'] = "✅ Bahan berhasil diupdate!";
        header("Location: materials.php?product_id=$productId");
        exit;

    } else {
        // === INSERT MATERIAL BARU ===
        // Cari kode terakhir untuk produk ini
        $stmt = $pdo->prepare("SELECT kode 
                               FROM materials 
                               WHERE product_id=? 
                               ORDER BY id DESC 
                               LIMIT 1");
        $stmt->execute([$productId]);
        $lastKode = $stmt->fetchColumn();

        if ($lastKode) {
            // Ambil angka urut setelah tanda '-'
            $lastNum = (int)substr($lastKode, strrpos($lastKode, '-') + 1);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        // Format kode bahan: <kodeProduk>-<3digit>
        $kode = $productKode . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        // cek duplikat kode (jaga-jaga)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM materials WHERE kode=?");
        $stmt->execute([$kode]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("⚠️ Kode $kode sudah ada, coba lagi!");
        }

        $stmt = $pdo->prepare("INSERT INTO materials (kode, nama, jumlah, satuan, product_id, created_at) 
                               VALUES (?,?,?,?,?,NOW())");
        $stmt->execute([$kode, $nama, $jumlah, $satuan, $productId]);

        unset($_SESSION['old_input']);
        $_SESSION['flash_success'] = "✅ Bahan baru berhasil ditambahkan!";
        header("Location: materials.php?product_id=$productId");
        exit;
    }

} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    $redirect = $id 
        ? "material_form.php?id=$id&product_id=$productId" 
        : "material_form.php?product_id=$productId";
    header("Location: $redirect");
    exit;
}
