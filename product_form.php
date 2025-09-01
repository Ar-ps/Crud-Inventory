<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
include 'partials/header.php'; 
include 'config.php'; 

$id = $_GET['id'] ?? null;
$product = null;

// ambil produk dari DB jika edit
if ($id) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
  $stmt->execute([$id]);
  $product = $stmt->fetch();
}

// Ambil data input lama dari session (jika ada error)
$old = $_SESSION['old_input'] ?? null;
unset($_SESSION['old_input']); // hapus agar tidak nempel terus

// ambil kategori
$categories = $pdo->query("SELECT id, nama FROM categories ORDER BY nama")->fetchAll();
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-800 px-4 py-8">
  <div class="container">
    <!-- Flash Message -->
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
          text: "<?= addslashes($_SESSION['flash_success']); ?>"
        });
      });
    </script>
    <?php unset($_SESSION['flash_success']); endif; ?>

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
          <input type="hidden" name="id" value="<?= htmlspecialchars($old['id'] ?? $product['id'] ?? '') ?>">

          <?php if ($id): ?>
            <div class="mb-4">
              <label class="form-label fw-semibold text-cyan-300">Kode Barang</label>
              <div class="input-group">
                <input type="text" id="kodeInput" name="kode" 
                       class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                       value="<?= htmlspecialchars($old['kode'] ?? $product['kode'] ?? '') ?>"
                       readonly>
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
            <label class="form-label fw-semibold text-cyan-300">Nama Barang</label>
            <input type="text" name="nama" 
                   class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm"
                   placeholder="Masukkan nama barang..."
                   value="<?= htmlspecialchars($old['nama'] ?? $product['nama'] ?? '') ?>" required>
          </div>

          <div class="mb-4">
            <label for="kategori" class="form-label fw-semibold text-cyan-300">Kategori</label>
            <select id="kategori" name="kategori_id" 
                    class="form-control bg-slate-800 text-white border-0 rounded-pill shadow-sm" required>
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>" 
                <?= (($old['kategori_id'] ?? $product['kategori_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nama']) ?>
              </option>
              <?php endforeach; ?>
            </select>
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
