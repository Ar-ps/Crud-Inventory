<?php
include __DIR__ . '/../config.php';

// Header umum
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM materials WHERE id=?");
            $stmt->execute([$_GET['id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Data tidak ditemukan"]);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM materials ORDER BY id DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        }
        break;

    case 'POST':
        // Jika input tunggal, bungkus ke array
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
            $nama      = trim($item['nama'] ?? '');
            $jumlah    = (int)($item['jumlah'] ?? 0);
            $satuan    = trim($item['satuan'] ?? '');
            $productId = $item['product_id'] ?? null;

            if (!$nama || !$productId || !$satuan) {
                http_response_code(400);
                echo json_encode(["error" => "nama, satuan, dan product_id wajib diisi"]);
                exit;
            }

            // generate kode unik
            $prefix = strtoupper(substr($nama, 0, 3));
            $stmt = $pdo->prepare("SELECT kode FROM materials WHERE kode LIKE ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$prefix . '%']);
            $lastKode = $stmt->fetchColumn();

            if ($lastKode) {
                $lastNum = (int)substr($lastKode, strlen($prefix));
                $nextNum = $lastNum + 1;
            } else {
                $nextNum = 1;
            }
            $kode = $prefix . str_pad($nextNum, 3, "0", STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO materials (kode, nama, jumlah, satuan, product_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$kode, $nama, $jumlah, $satuan, $productId]);

            $results[] = [
                "id" => $pdo->lastInsertId(),
                "kode" => $kode,
                "nama" => $nama,
                "jumlah" => $jumlah,
                "satuan" => $satuan,
                "product_id" => $productId
            ];
        }

        http_response_code(201);
        echo json_encode([
            "message" => "Data berhasil ditambahkan",
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
        $id     = $_GET['id'];
        $nama   = trim($input['nama'] ?? '');
        $jumlah = (int)($input['jumlah'] ?? 0);
        $satuan = trim($input['satuan'] ?? '');

        if (!$nama || !$satuan) {
            http_response_code(400);
            echo json_encode(["error" => "nama dan satuan wajib diisi"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE materials SET nama=?, jumlah=?, satuan=? WHERE id=?");
        $stmt->execute([$nama, $jumlah, $satuan, $id]);

        echo json_encode(["message" => "Data berhasil diperbarui", "id" => $id]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "id wajib diisi"]);
            exit;
        }
        $id = $_GET['id'];

        $stmt = $pdo->prepare("DELETE FROM materials WHERE id=?");
        $stmt->execute([$id]);

        echo json_encode(["message" => "Data berhasil dihapus", "id" => $id]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method tidak diizinkan"]);
        break;
}
