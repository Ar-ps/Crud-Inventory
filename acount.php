<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'partials/header.php';
include 'config.php';

// ==== Flash Message ====
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-800">
  <div class="container-fluid px-4 pt-8 pb-6">

    <!-- ✅ SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($flash_success): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= addslashes($flash_success) ?>',
        showConfirmButton: false,
        timer: 2000
      });
    </script>
    <?php endif; ?>

    <?php if ($flash_error): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Peringatan',
        text: '<?= addslashes($flash_error) ?>',
        confirmButtonColor: '#d33'
      });
    </script>
    <?php endif; ?>

    <!-- Header -->
    <div class="row align-items-center mb-4">
      <div class="col-lg-8">
        <div class="d-flex align-items-center mb-3">
          <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-3 rounded-xl shadow-lg me-4">
            <i class="fas fa-user-cog text-white fs-2"></i>
          </div>
          <div>
            <h1 class="display-5 fw-bold text-white mb-1">Pengaturan Akun</h1>
            <p class="text-slate-300 mb-0 fs-5">Kelola informasi akun Anda</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container-fluid px-4 pb-8">
    <div class="card border-0 shadow-2xl bg-white/10 backdrop-blur-lg rounded-4 overflow-hidden">

      <!-- Card Header -->
      <div class="card-header border-0 py-4 text-white fw-bold d-flex align-items-center gap-2" 
           style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
        <i class="fas fa-user-edit text-cyan-400 fs-4"></i>
        <span class="fs-5">Formulir Akun</span>
      </div>

      <!-- Card Body -->
      <div class="card-body text-white p-5">
        <form method="post" action="acount_update.php" class="space-y-4">
          
          <!-- Username -->
          <div class="mb-4">
            <label class="form-label fw-semibold text-cyan-300">👤 Username</label>
            <input type="text" name="username" id="usernameInput"
                   class="form-control custom-input bg-slate-800 text-white border-0 rounded-pill shadow-sm px-4 py-2"
                   value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" required readonly>

            <!-- Checklist di bawah input -->
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" id="lockUsername" checked>
              <label class="form-check-label text-slate-300" for="lockUsername">
                🔒 Kunci Username (biar tidak bisa diubah)
              </label>
            </div>
            <small class="text-slate-400">Hilangkan centang jika ingin mengedit username.</small>
          </div>

          <!-- Password -->
          <div class="mb-4">
            <label class="form-label fw-semibold text-cyan-300">🔑 Password Baru</label>
            <input type="password" name="password" 
                   class="form-control custom-input bg-slate-800 text-white border-0 rounded-pill shadow-sm px-4 py-2" 
                   placeholder="Kosongkan jika tidak ingin mengganti password">
            <small class="text-slate-400">Kosongkan jika tidak ingin mengubah password.</small>
          </div>

          <!-- Tombol -->
          <div class="d-flex gap-3 mt-4">
            <button type="submit" 
                    class="btn fw-semibold text-white px-5 py-2 rounded-pill shadow-lg hover:scale-105 transition"
                    style="background: linear-gradient(135deg,#10b981 0%,#059669 100%); border:none;">
              <i class="fas fa-save me-1"></i> Simpan
            </button>
            <a href="index.php" 
               class="btn fw-semibold text-white px-5 py-2 rounded-pill shadow-lg hover:scale-105 transition"
               style="background: linear-gradient(135deg,#64748b 0%,#334155 100%); border:none;">
              ↩️ Kembali
            </a>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Script untuk toggle readonly username -->
<script>
  const lockCheckbox = document.getElementById('lockUsername');
  const usernameInput = document.getElementById('usernameInput');

  // default terkunci
  usernameInput.setAttribute('readonly', true);
  usernameInput.classList.add('opacity-75');

  lockCheckbox.addEventListener('change', function() {
    if (this.checked) {
      usernameInput.setAttribute('readonly', true);
      usernameInput.classList.add('opacity-75');
    } else {
      usernameInput.removeAttribute('readonly');
      usernameInput.classList.remove('opacity-75');
    }
  });
</script>

<!-- CSS agar input tetap gelap saat focus -->
<style>
  .custom-input:focus {
    background-color: #1e293b !important; /* tetap gelap */
    color: #fff !important;
    border: none !important;
    box-shadow: 0 0 0 2px #38bdf8 !important; /* efek fokus */
  }
</style>

<?php include 'partials/footer.php'; ?>
