<?php
include __DIR__ . '/../config.php';

// Headers untuk API
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Ambil 1 produk
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
            $stmt->execute([$_GET['id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Ambil bahan/materials terkait produk
                $stmt = $pdo->prepare("SELECT id, kode, nama, jumlah FROM materials WHERE product_id=? ORDER BY id DESC");
                $stmt->execute([$_GET['id']]);
                $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $product['materials'] = $materials;

                echo json_encode($product);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Produk tidak ditemukan"]);
            }
        } else {
            // Semua produk
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tambahkan bahan/materials untuk tiap produk
            foreach ($products as &$p) {
                $stmt = $pdo->prepare("SELECT id, kode, nama, jumlah FROM materials WHERE product_id=? ORDER BY id DESC");
                $stmt->execute([$p['id']]);
                $p['materials'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            echo json_encode($products);
        }
        break;

    case 'POST':
            // Kalau input satu object → bungkus ke array agar seragam
            if (isset($input['nama'])) {
                $input = [$input];
            }
        
            if (!is_array($input) || count($input) === 0) {
                http_response_code(400);
                echo json_encode(["error" => "Input harus berupa object atau array of object"]);
                exit;
            }
        
            $results = [];
        
            foreach ($input as $item) {
                $nama     = trim($item['nama'] ?? '');
                $kategori = trim($item['kategori'] ?? '');
        
                if (!$nama || !$kategori) {
                    http_response_code(400);
                    echo json_encode(["error" => "nama dan kategori wajib diisi"]);
                    exit;
                }
        
                // Generate kode produk
                $prefix = strtoupper(substr($nama, 0, 3));
                $stmt = $pdo->prepare("SELECT kode FROM products WHERE kode LIKE ? ORDER BY id DESC LIMIT 1");
                $stmt->execute([$prefix . '%']);
                $lastKode = $stmt->fetchColumn();
        
                if ($lastKode) {
                    $lastNum = (int)substr($lastKode, strlen($prefix));
                    $nextNum = $lastNum + 1;
                } else {
                    $nextNum = 1;
                }
                $kode = $prefix . str_pad($nextNum, 3, "0", STR_PAD_LEFT);
        
                $stmt = $pdo->prepare("INSERT INTO products (kode, nama, kategori) VALUES (?, ?, ?)");
                $stmt->execute([$kode, $nama, $kategori]);
        
                $results[] = [
                    "id"       => $pdo->lastInsertId(),
                    "kode"     => $kode,
                    "nama"     => $nama,
                    "kategori" => $kategori
                ];
            }
        
            http_response_code(201);
            echo json_encode([
                "message" => "Produk berhasil ditambahkan",
                "count"   => count($results),
                "data"    => $results
            ]);
            break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "id wajib diisi"]);
            exit;
        }

        $id       = $_GET['id'];
        $nama     = trim($input['nama'] ?? '');
        $kategori = trim($input['kategori'] ?? '');

        if (!$nama || !$kategori) {
            http_response_code(400);
            echo json_encode(["error" => "nama dan kategori wajib diisi"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE products SET nama=?, kategori=? WHERE id=?");
        $stmt->execute([$nama, $kategori, $id]);

        echo json_encode(["message" => "Produk berhasil diperbarui", "id" => $id]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "id wajib diisi"]);
            exit;
        }

        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$id]);

        echo json_encode(["message" => "Produk berhasil dihapus", "id" => $id]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method tidak diizinkan"]);
        break;
}
