<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

// deteksi apakah input dari JSON (API Postman) atau dari form HTML
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data) {
    $id         = $data['id'] ?? null;
    $kode       = $data['kode'] ?? null;
    $nama       = trim($data['nama'] ?? '');
    $kategoriId = $data['kategori_id'] ?? null;
} else {
    $id         = $_POST['id'] ?? null;
    $kode       = $_POST['kode'] ?? null;
    $nama       = trim($_POST['nama'] ?? '');
    $kategoriId = $_POST['kategori_id'] ?? null;
}

// simpan input terakhir agar form tidak terhapus jika error
$_SESSION['old_input'] = [
    "id"          => $id,
    "kode"        => $kode,
    "nama"        => $nama,
    "kategori_id" => $kategoriId
];

// validasi input dasar
if (empty($nama) || empty($kategoriId)) {
    $msg = "❌ Nama dan kategori wajib diisi!";
    if ($data) {
        header('Content-Type: application/json');
        echo json_encode(["error" => $msg]);
    } else {
        $_SESSION['flash_error'] = $msg;
        $redirect = $id ? "product_form.php?id=".$id : "product_form.php";
        header("Location: $redirect");
    }
    exit;
}

try {
    if ($id) {
        // === UPDATE ===
        if (empty($kode)) {
            $stmt = $pdo->prepare("SELECT kode FROM products WHERE id=?");
            $stmt->execute([$id]);
            $kode = $stmt->fetchColumn();
        }

        // cek duplikat kode
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE kode=? AND id<>?");
        $stmt->execute([$kode, $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("⚠️ Kode $kode sudah digunakan produk lain!");
        }

        $stmt = $pdo->prepare("UPDATE products SET kode=?, nama=?, kategori_id=? WHERE id=?");
        $stmt->execute([$kode, $nama, $kategoriId, $id]);

        unset($_SESSION['old_input']);
        $msg = "Produk berhasil diupdate";

    } else {
        // === INSERT BARU ===
        // ambil kode kategori (2 digit)
        $stmt = $pdo->prepare("SELECT kode FROM categories WHERE id=?");
        $stmt->execute([$kategoriId]);
        $kategoriKode = $stmt->fetchColumn();

        if (!$kategoriKode) {
            throw new Exception("❌ Kategori dengan ID $kategoriId tidak ditemukan!");
        }

        // ambil kode produk terakhir di kategori ini
        $stmt = $pdo->prepare("SELECT kode FROM products WHERE kategori_id=? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$kategoriId]);
        $lastKode = $stmt->fetchColumn();

        if ($lastKode) {
            // ambil 4 digit terakhir
            $lastNum = (int)substr($lastKode, 2);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        // format kode produk: <kodeKategori><4digit>
        $kode = $kategoriKode . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // cek duplikat kode
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE kode=?");
        $stmt->execute([$kode]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("⚠️ Kode $kode sudah ada, coba lagi!");
        }

        $stmt = $pdo->prepare("INSERT INTO products (kode, nama, kategori_id, created_at) VALUES (?,?,?,NOW())");
        $stmt->execute([$kode, $nama, $kategoriId]);

        unset($_SESSION['old_input']);
        $msg = "Produk berhasil ditambahkan";
    }

    // respon
    if ($data) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "success", "message" => $msg, "kode" => $kode]);
    } else {
        $_SESSION['flash_success'] = $msg;
        header("Location: index.php");
    }

} catch (Exception $e) {
    if ($data) {
        header('Content-Type: application/json');
        echo json_encode(["error" => $e->getMessage()]);
    } else {
        $_SESSION['flash_error'] = $e->getMessage();
        $redirect = $id ? "product_form.php?id=".$id : "product_form.php";
        header("Location: $redirect");
    }
}
