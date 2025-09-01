<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'partials/header.php'; 
include 'config.php'; 

$productId = $_GET['product_id'] ?? null;
$product   = null;
$materials = [];

// ==== Flash Message ====
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// --- Pagination fixed 10 per page --- //
$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ==== Sorting ====
$validSorts = ['kode','nama','jumlah','satuan','created_at','produk_nama']; 
$sort = $_GET['sort'] ?? 'id'; // default by id
$order = strtolower($_GET['order'] ?? 'desc'); 
$order = ($order === 'asc') ? 'ASC' : 'DESC';
if (!in_array($sort, $validSorts) && $sort !== 'id') {
  $sort = 'id';
}

// Hitung total bahan
if ($productId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM materials WHERE product_id=?");
    $stmt->execute([$productId]);
    $countMaterials = $stmt->fetchColumn();
} else {
    $countMaterials = $pdo->query("SELECT COUNT(*) FROM materials")->fetchColumn();
}

// --- Ambil data bahan dengan LIMIT & OFFSET + SORT ---
if ($productId) {
    $stmt = $pdo->prepare("SELECT * FROM materials WHERE product_id=? ORDER BY $sort $order LIMIT $limit OFFSET $offset");
    $stmt->execute([$productId]);
    $materials = $stmt->fetchAll();

    // Detail produk
    $stmt = $pdo->prepare("
          SELECT p.*, c.nama AS kategori
          FROM products p
          LEFT JOIN categories c ON p.kategori_id = c.id
          WHERE p.id=?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
} else {
    $stmt = $pdo->query("SELECT m.*, p.nama as produk_nama 
                         FROM materials m 
                         LEFT JOIN products p ON m.product_id=p.id 
                         ORDER BY $sort $order 
                         LIMIT $limit OFFSET $offset");
    $materials = $stmt->fetchAll();
}
?>
<!-- ✅ CDN SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($flash_success): ?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: '<?= addslashes($flash_success) ?>',
      showConfirmButton: false,
      timer: 2000
    });
  });
</script>
<?php endif; ?>

<?php if ($flash_error): ?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
      icon: 'error',
      title: 'Peringatan',
      text: '<?= addslashes($flash_error) ?>',
      confirmButtonColor: '#d33'
    });
  });
</script>
<?php endif; ?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-800 px-4 py-8">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-6">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-gradient-to-r from-pink-500 to-rose-500 p-3 rounded-xl shadow-lg">
        <i class="fas fa-flask text-white fs-2"></i>
      </div>
      <div>
        <?php if ($product): ?>
          <h1 class="display-6 fw-bold text-white mb-1">Bahan Baku Produk</h1>
          <p class="text-slate-300 mb-0 fs-6">
            Kelola bahan untuk <span class="fw-semibold text-rose-400"><?= htmlspecialchars($product['nama']) ?></span>
          </p>
        <?php else: ?>
          <h1 class="display-6 fw-bold text-white mb-1">Daftar Semua Bahan Baku</h1>
          <p class="text-slate-300 mb-0 fs-6">Kelola inventaris bahan baku produk Anda</p>
        <?php endif; ?>
      </div>
    </div>
    <div class="d-flex gap-2">
      <?php if ($product): ?>
        <a href="material_form.php?product_id=<?= $product['id'] ?>" 
           class="btn btn-lg shadow px-4 py-2 rounded-pill text-white fw-semibold"
           style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
          <i class="fas fa-plus-circle me-2"></i>Tambah Bahan
        </a>
      <?php endif; ?>
      <a href="index.php" 
         class="btn btn-lg shadow px-4 py-2 rounded-pill text-white fw-semibold"
         style="background: linear-gradient(135deg, #64748b 0%, #334155 100%); border: none;">
        ↩️ Kembali
      </a>
    </div>
  </div>

  <!-- Stats Card -->
  <div class="row g-3 mb-5">
    <div class="col-md-4">
      <div class="card bg-gradient-to-r from-emerald-500 to-teal-600 border-0 shadow-lg">
        <div class="card-body text-white p-4">
          <div class="d-flex align-items-center">
            <i class="fas fa-boxes fs-1 opacity-75 me-3"></i>
            <div>
              <h2 class="fw-bold mb-0"><?= $countMaterials ?></h2>
              <small class="opacity-90">
                <?= $product ? 'Bahan Aktif Produk Ini' : 'Total Semua Bahan Aktif' ?>
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Produk -->
  <?php if ($product): ?>
    <div class="card bg-white/10 backdrop-blur-lg border-0 shadow-lg rounded-4 mb-5 text-white p-4">
      <div class="row">
        <div class="col-md-4"><strong>Kode:</strong> <?= htmlspecialchars($product['kode']) ?></div>
        <div class="col-md-4"><strong>Nama:</strong> <?= htmlspecialchars($product['nama']) ?></div>
        <div class="col-md-4"><strong>Kategori:</strong> <?= htmlspecialchars($product['kategori'] ?? '—') ?></div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Tabel -->
  <div class="card border-0 shadow-2xl bg-white/10 backdrop-blur-lg rounded-4 overflow-hidden">
    <div class="card-header border-0 py-4" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h3 class="text-white mb-0 fw-bold d-flex align-items-center">
            <i class="fas fa-box-open text-cyan-400 me-2"></i>
            <?= $product ? 'Daftar Bahan untuk Produk' : 'Katalog Semua Bahan Baku' ?>
          </h3>
        </div>
        
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-slate-700 border-slate-600 text-slate-300">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" 
                   class="form-control bg-slate-700 border-slate-600 text-black" 
                   placeholder="Cari bahan..."
                   id="searchInput">
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-dark table-hover mb-0" style="background: rgba(15, 23, 42, 0.9);">
        <thead>
          <tr style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <th class="text-cyan-300 fw-semibold py-3 px-4 text-center">No</th>

            <th class="text-cyan-300 fw-semibold py-3 px-4">
              <a href="?<?= $productId ? 'product_id='.$productId.'&' : '' ?>sort=kode&order=<?= ($sort==='kode' && $order==='ASC')?'desc':'asc' ?>" class="text-cyan-300 text-decoration-none">
                Kode <?= ($sort==='kode')?($order==='ASC'?'⬆️':'⬇️'):'' ?>
              </a>
            </th>

            <th class="text-cyan-300 fw-semibold py-3 px-4">
              <a href="?<?= $productId ? 'product_id='.$productId.'&' : '' ?>sort=nama&order=<?= ($sort==='nama' && $order==='ASC')?'desc':'asc' ?>" class="text-cyan-300 text-decoration-none">
                Nama Bahan <?= ($sort==='nama')?($order==='ASC'?'⬆️':'⬇️'):'' ?>
              </a>
            </th>

            <th class="text-cyan-300 fw-semibold py-3 px-4 text-center">
              <a href="?<?= $productId ? 'product_id='.$productId.'&' : '' ?>sort=jumlah&order=<?= ($sort==='jumlah' && $order==='ASC')?'desc':'asc' ?>" class="text-cyan-300 text-decoration-none">
                Jumlah <?= ($sort==='jumlah')?($order==='ASC'?'⬆️':'⬇️'):'' ?>
              </a>
            </th>

            <th class="text-cyan-300 fw-semibold py-3 px-4 text-center">
              <a href="?<?= $productId ? 'product_id='.$productId.'&' : '' ?>sort=satuan&order=<?= ($sort==='satuan' && $order==='ASC')?'desc':'asc' ?>" class="text-cyan-300 text-decoration-none">
                Satuan <?= ($sort==='satuan')?($order==='ASC'?'⬆️':'⬇️'):'' ?>
              </a>
            </th>

            <?php if (!$product): ?>
              <th class="text-cyan-300 fw-semibold py-3 px-4">
                <a href="?sort=produk_nama&order=<?= ($sort==='produk_nama' && $order==='ASC')?'desc':'asc' ?>" class="text-cyan-300 text-decoration-none">
                  Produk <?= ($sort==='produk_nama')?($order==='ASC'?'⬆️':'⬇️'):'' ?>
                </a>
              </th>
            <?php endif; ?>

            <th class="text-cyan-300 fw-semibold py-3 px-4 text-center">
              <a href="?<?= $productId ? 'product_id='.$productId.'&' : '' ?>sort=created_at&order=<?= ($sort==='created_at' && $order==='ASC')?'desc':'asc' ?>" class="text-cyan-300 text-decoration-none">
                Waktu Pembuatan <?= ($sort==='created_at')?($order==='ASC'?'⬆️':'⬇️'):'' ?>
              </a>
            </th>

            <th class="text-cyan-300 fw-semibold py-3 px-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="materialsTableBody">
          <?php if (empty($materials)): ?>
            <tr>
              <td colspan="<?= $product ? '7' : '8' ?>" class="text-center text-slate-400 py-5">
                Belum ada data bahan
              </td>
            </tr>
          <?php else: ?>
            <?php $no = $offset+1; foreach ($materials as $row): ?>
            <tr class="product-row border-slate-700">
              <td class="py-3 px-4 text-center text-slate-200 fw-bold"><?= $no++ ?></td>
              <td class="py-3 px-2">
                <span class="badge bg-gradient-to-r from-blue-500 to-indigo-600 px-3 py-2 rounded-pill shadow-sm">
                  <?= htmlspecialchars($row['kode']) ?>
                </span>
              </td>
              <td class="py-3 px-4 text-white fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
              <td class="py-3 px-4 text-slate-200 fw-semibold text-center"><?= htmlspecialchars($row['jumlah'] ?? 0) ?></td>
              <td class="py-3 px-4 text-slate-200 fw-semibold text-center"><?= htmlspecialchars($row['satuan'] ?? '-') ?></td>
              <?php if (!$product): ?>
                <td class="py-3 px-2 text-slate-300"><?= htmlspecialchars($row['produk_nama'] ?? '-') ?></td>
              <?php endif; ?>
              <td class="py-3 px-4 text-center">
                <div class="material-time">
                  <h6 class="text-white fw-semibold mb-1">
                    <?= date("d-m-Y", strtotime($row['created_at'])) ?>
                  </h6>
                  <small class="text-slate-400">
                    <?= date("H:i", strtotime($row['created_at'])) ?> WIB
                  </small>
                </div>
              </td>
              <td class="py-3 px-4 text-center">
                <div class="d-flex justify-content-center gap-2">
                  <a href="material_form.php?id=<?= $row['id'] ?>&product_id=<?= $row['product_id'] ?>" 
                     class="btn btn-sm px-4 py-2 fw-semibold text-dark rounded-pill shadow-lg"
                     style="background: linear-gradient(135deg,#facc15 0%,#eab308 100%); border:none;">
                    <i class="fas fa-edit me-1"></i>Edit
                  </a>
                  <a href="material_delete.php?id=<?= $row['id'] ?>&product_id=<?= $row['product_id'] ?>" 
                     onclick="return confirm('🗑️ Yakin ingin menghapus bahan ini?')"
                     class="btn btn-sm px-4 py-2 fw-semibold text-white rounded-pill shadow-lg"
                     style="background: linear-gradient(135deg,#ef4444 0%,#b91c1c 100%); border:none;">
                    <i class="fas fa-trash-alt me-1"></i>Hapus
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php 
      $totalPages = ceil($countMaterials / $limit);
      if ($totalPages > 1): 
    ?>
    <div class="card-footer bg-dark d-flex justify-content-end">
      <nav aria-label="Page navigation">
        <ul class="pagination pagination-lg mb-0">
          <?php for ($i=1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i==$page?'active':'' ?>">
              <a class="page-link bg-slate-800 border-0 text-white fw-semibold px-3 py-1 rounded-pill" 
                 href="?<?= $productId ? 'product_id='.$productId.'&' : '' ?>page=<?= $i ?>&sort=<?= $sort ?>&order=<?= strtolower($order) ?>">
                <?= $i ?>
              </a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
    <?php endif; ?>
  </div>
</div>

<style>
  .product-row:hover {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.3) 0%, rgba(51, 65, 85, 0.3) 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
});

document.getElementById('searchInput').addEventListener('keyup', function() {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll('#materialsTableBody tr');
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});
</script>

<?php include 'partials/footer.php'; ?>
