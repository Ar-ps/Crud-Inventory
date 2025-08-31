<?php
include 'config.php';

$id       = $_POST['id'] ?? null;
$kode     = $_POST['kode'] ?? null;
$nama     = trim($_POST['nama'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');

// Validasi input dasar
if (empty($nama) || empty($kategori)) {
    die("❌ Error: Nama dan kategori wajib diisi.");
}

if ($id) {
    // === UPDATE PRODUK ===
    if (empty($kode)) {
        $stmt = $pdo->prepare("SELECT kode FROM products WHERE id=?");
        $stmt->execute([$id]);
        $kode = $stmt->fetchColumn();
    }

    $stmt = $pdo->prepare("UPDATE products 
                           SET kode=?, nama=?, kategori=? 
                           WHERE id=?");
    $stmt->execute([$kode, $nama, $kategori, $id]);

} else {
    // === INSERT PRODUK BARU ===
    $words = preg_split('/\s+/', $nama);
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
            // Jika terlalu pendek, ambil semua huruf lalu pad dengan X
            $prefix = str_pad($word, 3, 'X');
        }

    } elseif (count($words) == 2) {
        // Jika 2 kata → ambil huruf pertama dari tiap kata
        $prefix = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));

    } elseif (count($words) >= 3) {
        // Jika 3 kata → ambil huruf depan tiap kata (maks 3 huruf)
        $prefix = strtoupper(substr($words[0], 0, 1) .
                             substr($words[1], 0, 1) .
                             substr($words[2], 0, 1));
    }

    // Cari kode terakhir dengan prefix sama
    $stmt = $pdo->prepare("SELECT kode 
                           FROM products 
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

    // Simpan produk baru
    $stmt = $pdo->prepare("INSERT INTO products (kode, nama, kategori) 
                           VALUES (?,?,?)");
    $stmt->execute([$kode, $nama, $kategori]);
}

// Kembali ke halaman index produk
header("Location: index.php");
exit;
