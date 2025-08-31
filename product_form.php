<?php 
include 'partials/header.php'; 
include 'config.php'; 

$id = $_GET['id'] ?? null;
$product = null;

if ($id) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
  $stmt->execute([$id]);
  $product = $stmt->fetch();
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-800 px-4 py-8">
  <div class="container">
    <!-- Header -->
    <div class="d-flex align-items-center gap-3 mb-5">
      <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl shadow-lg">
        <i class="fas fa-cube text-white fs-2"></i>
      </div>
      <div>
        <h1 class="fw-bold text-white mb-1 fs-3">
          <?= $id ? "✏️ Edit Barang" : "➕ Tambah Barang" ?>
        </h1>
        <p class="text-slate-300 mb-0">Kelola data inventaris produk Anda</p>
      </div>
    </div>

    <!-- Form Card -->
    <div class="card bg-white/10 backdrop-blur-lg border-0 shadow-2xl rounded-4 overflow-hidden">
      <div class="card-body p-5 text-white">
        <form method="post" action="product_save.php">
          <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">

          <?php if ($id): ?>
            <div class="mb-4">
              <label class="form-label fw-semibold text-cyan-300">Kode Barang</label>
              <input type="text" name="kode" 
                     class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                     value="<?= htmlspecialchars($product['kode']) ?>" readonly>
              <small class="text-slate-400">Kode otomatis, tidak bisa diubah</small>
            </div>
          <?php endif; ?>

          <div class="mb-4">
            <label class="form-label fw-semibold text-cyan-300">Nama Barang</label>
            <input type="text" name="nama" 
                   class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                   placeholder="Masukkan nama barang..."
                   value="<?= htmlspecialchars($product['nama'] ?? '') ?>" required>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-cyan-300">Kategori</label>
            <input type="text" name="kategori" 
                   class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                   placeholder="Contoh: Furniture, Elektronik"
                   value="<?= htmlspecialchars($product['kategori'] ?? '') ?>">
          </div>

          <div class="d-flex gap-3 mt-4">
            <button type="submit" 
                    class="btn fw-semibold text-white px-5 py-2 rounded-pill shadow-lg hover:scale-105 transition"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border:none;">
              <i class="fas fa-save me-2"></i> Simpan
            </button>
            <a href="index.php" 
               class="btn fw-semibold text-white px-5 py-2 rounded-pill shadow-lg hover:scale-105 transition"
               style="background: linear-gradient(135deg, #64748b 0%, #334155 100%); border:none;">
              ↩️ Batal
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .hover\:scale-105:hover { transform: scale(1.05); }
  .transition { transition: all 0.3s ease; }
  .rounded-4 { border-radius: 1.5rem !important; }
  /* Biar input tetap gelap ketika focus */
  .form-control.bg-slate-800,
  .form-control.bg-slate-800:focus {
    background-color: #1e293b !important; /* slate-800 */
    color: #fff !important;
    border: 1px solid #3b82f6 !important; /* opsional: border biru */
    box-shadow: 0 0 0 0.2rem rgba(59,130,246,0.25) !important; /* glow halus */
  }

  /* Placeholder tetap abu-abu saat focus */
  .form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 0.8;
  }
</style>

<?php include 'partials/footer.php'; ?>
