<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'partials/header.php'; 
include 'config.php'; 

$id        = $_GET['id'] ?? null;
$productId = $_GET['product_id'] ?? null; 
$material  = null;

// Ambil data material jika edit
if ($id) {
  $stmt = $pdo->prepare("SELECT * FROM materials WHERE id=?");
  $stmt->execute([$id]);
  $material = $stmt->fetch();

  if (!$productId && $material) {
    $productId = $material['product_id'];
  }
}

if (!$productId) {
  die("❌ Error: Halaman ini harus dibuka dengan product_id, contoh: materials.php?product_id=1");
}
?>
<!-- ✅ CDN SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($_SESSION['flash_error'])): ?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "<?= addslashes($_SESSION['flash_error']); ?>"
    });
  });
</script>
<?php unset($_SESSION['flash_error']); endif; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
      icon: "success",
      title: "Berhasil",
      text: "<?= addslashes($_SESSION['flash_success']); ?>",
      showConfirmButton: false,
      timer: 2000
    });
  });
</script>
<?php unset($_SESSION['flash_success']); endif; ?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-800 px-4 py-8">
  <div class="container">
    <!-- Header -->
    <div class="d-flex align-items-center gap-3 mb-5">
      <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-3 rounded-xl shadow-lg">
        <i class="fas fa-flask text-white fs-2"></i>
      </div>
      <div>
        <h1 class="fw-bold text-white mb-1 fs-3">
          <?= $id ? "✏️ Edit Bahan Baku" : "➕ Tambah Bahan Baku" ?>
        </h1>
        <p class="text-slate-300 mb-0">Untuk Produk #<?= htmlspecialchars($productId) ?></p>
      </div>
    </div>

    <!-- Form Card -->
    <div class="card bg-white/10 backdrop-blur-lg border-0 shadow-2xl rounded-4 overflow-hidden">
      <div class="card-body p-5 text-white">
        <form method="post" action="material_save.php">
          <!-- ID bahan (untuk edit) -->
          <input type="hidden" name="id" value="<?= $material['id'] ?? '' ?>">
          <!-- Product aktif (selalu wajib ada) -->
          <input type="hidden" name="product_id" value="<?= htmlspecialchars($productId) ?>">

          <?php if ($id): ?>
            <!-- Saat edit tampilkan kode -->
            <div class="mb-4">
              <label class="form-label fw-semibold text-cyan-300">Kode Bahan</label>
              <div class="input-group">
                <input type="text" id="kodeInput" name="kode" 
                       class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                       value="<?= htmlspecialchars($material['kode']) ?>" readonly>
              </div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="lockKode" checked>
                <label class="form-check-label text-slate-300" for="lockKode">
                  🔒 Kunci kode (biar tidak bisa diubah)
                </label>
              </div>
              <small class="text-slate-400">Hilangkan centang jika ingin mengedit kode manual.</small>
            </div>
          <?php endif; ?>

          <div class="mb-4">
            <label class="form-label fw-semibold text-cyan-300">Nama Bahan</label>
            <input type="text" name="nama" class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                   placeholder="Masukkan nama bahan..."
                   value="<?= htmlspecialchars($material['nama'] ?? '') ?>" required>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-cyan-300">Jumlah</label>
            <input type="number" name="jumlah" min="0" class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                   value="<?= htmlspecialchars($material['jumlah'] ?? 0) ?>" required>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-cyan-300">Satuan</label>
            <select name="satuan" class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm" required>
              <?php
              $satuanList = ['Kg','Gram','Liter','Meter','Pcs','Unit','Box'];
              $selected = $material['satuan'] ?? '';
              foreach ($satuanList as $s) {
                  $isSel = ($selected == $s) ? 'selected' : '';
                  echo "<option value='$s' $isSel>$s</option>";
              }
              ?>
            </select>
          </div>

          <div class="d-flex gap-3 mt-4">
            <button type="submit" 
                    class="btn fw-semibold text-white px-5 py-2 rounded-pill shadow-lg hover:scale-105 transition"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border:none;">
              <i class="fas fa-save me-2"></i> Simpan
            </button>
            <a href="materials.php?product_id=<?= $productId ?>" 
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
  .form-control.bg-slate-800,
  .form-control.bg-slate-800:focus {
    background-color: #1e293b !important;
    color: #fff !important;
    border: 1px solid #3b82f6 !important;
    box-shadow: 0 0 0 0.2rem rgba(59,130,246,0.25) !important;
  }
  .form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 0.8;
  }
</style>

<script>
  // Script untuk mengatur readonly berdasarkan checkbox
  document.addEventListener("DOMContentLoaded", function() {
    const kodeInput = document.getElementById("kodeInput");
    const lockKode = document.getElementById("lockKode");

    if (lockKode) {
      lockKode.addEventListener("change", function() {
        if (this.checked) {
          kodeInput.setAttribute("readonly", true);
        } else {
          kodeInput.removeAttribute("readonly");
        }
      });
    }
  });
</script>

<?php include 'partials/footer.php'; ?>
