<?php
include 'config.php';

$id        = $_POST['id'] ?? null;
$kode      = $_POST['kode'] ?? null;
$nama      = trim($_POST['nama'] ?? '');
$jumlah    = (int)($_POST['jumlah'] ?? 0);
$satuan    = trim($_POST['satuan'] ?? '');   // ✅ ambil satuan
$productId = $_POST['product_id'] ?? null;

// === Validasi dasar ===
if (!$productId) {
    die("❌ Error: product_id wajib diisi.");
}
if (empty($nama) || empty($satuan)) {
    die("❌ Error: Nama dan satuan wajib diisi.");
}

// Pastikan product_id valid
$check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE id=?");
$check->execute([$productId]);
if ($check->fetchColumn() == 0) {
    die("❌ Error: Produk dengan ID $productId tidak ditemukan.");
}

if ($id) {
    // === UPDATE MATERIAL ===
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
    // === INSERT MATERIAL BARU ===
    $words  = preg_split('/\s+/', $nama);
    $prefix = '';

    if (count($words) == 1) {
        // Jika hanya 1 kata → ambil huruf pertama, tengah, terakhir
        $word = strtoupper($words[0]);
        $len  = strlen($word);

        if ($len >= 3) {
            $first  = $word[0];
            $middle = $word[(int)floor($len/2)];
            $last   = $word[$len-1];
            $prefix = $first.$middle.$last;
        } else {
            $prefix = str_pad($word, 3, 'X'); // isi "X" jika terlalu pendek
        }

    } elseif (count($words) == 2) {
        // Jika 2 kata → ambil huruf depan tiap kata
        $prefix = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));

    } elseif (count($words) >= 3) {
        // Jika 3 kata → ambil huruf depan 3 kata pertama
        $prefix = strtoupper(substr($words[0], 0, 1) .
                             substr($words[1], 0, 1) .
                             substr($words[2], 0, 1));
    } else {
        $prefix = "MAT"; // fallback default
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

    // Simpan data baru
    $stmt = $pdo->prepare("INSERT INTO materials (kode, nama, jumlah, satuan, product_id) 
                           VALUES (?,?,?,?,?)");
    $stmt->execute([$kode, $nama, $jumlah, $satuan, $productId]);
}

// Redirect kembali ke halaman materials produk terkait
header("Location: materials.php?product_id=" . $productId);
exit;
