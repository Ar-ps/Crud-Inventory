<?php 
include 'partials/header.php'; 
include 'config.php'; 

// --- Pagination fixed 10 per page --- //
$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total produk
$countProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Ambil data produk dengan limit & offset
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT $limit OFFSET $offset");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-800">
  <!-- Hero Header Section -->
  <div class="container-fluid px-4 pt-8 pb-6">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="d-flex align-items-center mb-3">
          <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl shadow-lg me-4">
            <i class="fas fa-cube text-white fs-2"></i>
          </div>
          <div>
            <h1 class="display-5 fw-bold text-white mb-1">Manajemen Produk</h1>
            <p class="text-slate-300 mb-0 fs-5">Kelola inventaris dengan mudah dan efisien</p>
          </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="card bg-gradient-to-r from-emerald-500 to-teal-600 border-0 shadow-lg">
              <div class="card-body text-white p-4">
                <div class="d-flex align-items-center">
                  <i class="fas fa-boxes fs-1 opacity-75 me-3"></i>
                  <div>
                    <h2 class="fw-bold mb-0"><?= $countProducts ?></h2>
                    <small class="opacity-90">Total Produk</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 text-end">
        <a href="product_form.php" 
           class="btn btn-lg btn-primary shadow-xl px-5 py-3 rounded-pill text-decoration-none transform hover:scale-105 transition-all duration-300"
           style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
          <i class="fas fa-plus-circle me-2"></i>
          Tambah Produk Baru
        </a>
        <p class="text-slate-400 mt-2 mb-0 small">Ekspansi inventaris Anda</p>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container-fluid px-4 pb-8">
    <div class="card border-0 shadow-2xl bg-white/10 backdrop-blur-lg rounded-4 overflow-hidden">
      <!-- Card Header -->
      <div class="card-header border-0 py-4" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h3 class="text-white mb-0 fw-bold d-flex align-items-center">
              <i class="fas fa-inventory text-cyan-400 me-3"></i>
              Katalog Produk Premium
            </h3>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-slate-700 border-slate-600 text-slate-300">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" 
                     class="form-control bg-slate-700 border-slate-600 text-black" 
                     placeholder="Cari produk..."
                     id="searchInput">
            </div>
          </div>
        </div>
      </div>

      <!-- Table Container -->
      <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0" style="background: rgba(15, 23, 42, 0.9);">
          <thead>
            <tr style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
              <th class="text-center text-cyan-300 fw-semibold py-3 px-2" style="width:5%">No</th>
              <th class="text-center text-cyan-300 fw-semibold py-3 px-4" style="width:15%">Kode Produk</th>
              <th class="text-start text-cyan-300 fw-semibold py-3 px-2" style="width:25%">Nama Produk</th>
              <th class="text-start text-cyan-300 fw-semibold py-3 px-4" style="width:15%">Kategori</th>
              <th class="text-center text-cyan-300 fw-semibold py-3 px-4" style="width:15%">Waktu Pembuatan</th>
              <th class="text-center text-cyan-300 fw-semibold py-3 px-4" style="width:25%">Manajemen</th>
            </tr>
          </thead>
          <tbody id="productTableBody">
            <?php 
              $no = $offset+1;
              if (empty($products)): ?>
                <tr>
                  <td colspan="6" class="text-center text-slate-400 py-5">Belum ada data produk</td>
                </tr>
              <?php else:
              foreach($products as $row): 
            ?>
            <tr class="hover:bg-slate-800/50 transition-all duration-300 product-row border-slate-700">
              <!-- No -->
              <td class="text-center text-slate-200 fw-bold"><?= $no++ ?></td>

              <!-- Kode Produk -->
              <td class="text-center">
                <span class="badge bg-secondary fs-6 px-3 py-2 rounded-pill shadow-sm">
                  <?= htmlspecialchars($row['kode']) ?>
                </span>
              </td>

              <!-- Nama Produk -->
              <td>
                <div class="product-name">
                  <h6 class="text-white fw-semibold mb-1"><?= htmlspecialchars($row['nama']) ?></h6>
                  <small class="text-slate-400">SKU: <?= htmlspecialchars($row['kode']) ?></small>
                </div>
              </td>

              <!-- Kategori -->
              <td>
                <span class="badge bg-gradient-to-r from-pink-500 to-rose-500 px-3 py-2 rounded-pill shadow-sm text-white">
                  <i class="fas fa-folder-open me-1"></i>
                  <?= htmlspecialchars($row['kategori']) ?>
                </span>
              </td>

              <!-- Waktu Pembuatan -->
              <td class="text-center">
                <div class="product-time">
                  <h6 class="text-white fw-semibold mb-1">
                    <?= date("d-m-Y", strtotime($row['created_at'])) ?>
                  </h6>
                  <small class="text-slate-400">
                    <?= date("H:i", strtotime($row['created_at'])) ?> WIB
                  </small>
                </div>
              </td>

              <!-- Manajemen -->
              <td class="text-center">
                <div class="btn-group shadow-lg" role="group">
                  <a href="product_form.php?id=<?= $row['id'] ?>" 
                     class="btn btn-warning btn-sm px-3 py-2 text-dark fw-semibold rounded-start-pill"
                     data-bs-toggle="tooltip" title="Edit Produk">
                    <i class="fas fa-edit me-1"></i>Edit
                  </a>
                  <a href="materials.php?product_id=<?= $row['id'] ?>" 
                     class="btn btn-info btn-sm px-3 py-2 text-white fw-semibold"
                     data-bs-toggle="tooltip" title="Lihat Detail Bahan">
                    <i class="fas fa-list-ul me-1"></i>Detail
                  </a>
                  <a href="product_delete.php?id=<?= $row['id'] ?>" 
                     onclick="return confirm('🗑️ Yakin ingin menghapus produk ini?\n\nTindakan ini tidak dapat dibatalkan!')" 
                     class="btn btn-danger btn-sm px-3 py-2 text-white fw-semibold rounded-end-pill"
                     data-bs-toggle="tooltip" title="Hapus Produk">
                    <i class="fas fa-trash-alt me-1"></i>Hapus
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php 
        $totalPages = ceil($countProducts / $limit);
        if ($totalPages > 1): 
      ?>
      <div class="card-footer bg-dark d-flex justify-content-end">
        <nav aria-label="Page navigation">
          <ul class="pagination pagination-lg mb-0">
            <?php for ($i=1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $i==$page?'active':'' ?>">
                <a class="page-link bg-slate-800 border-0 text-white fw-semibold px-3 py-1 rounded-pill" href="?page=<?= $i ?>">
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
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productTableBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include 'partials/footer.php'; ?>
