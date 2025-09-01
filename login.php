<?php
session_start();
include 'config.php';

// Cek login
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if (!$username || !$password) {
    $_SESSION['flash_error'] = "Username dan Password wajib diisi!";
    header("Location: login.php"); exit;
  }

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['flash_success'] = "Selamat datang, {$user['username']}!";
    header("Location: index.php"); exit;
  } else {
    $_SESSION['flash_error'] = "Username atau Password salah!";
    header("Location: login.php"); exit;
  }
}

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
  /* Override bootstrap agar input tetap putih */
  .form-control.bg-slate-800 {
    color: #ffffff !important;
  }
  .form-control.bg-slate-800:focus {
    color: #ffffff !important;
    background-color: #1e293b !important; /* warna slate lebih gelap saat focus */
    border-color: #6366f1 !important;     /* border ungu */
    box-shadow: 0 0 0 .25rem rgba(99, 102, 241, 0.25); /* efek focus */
  }
  </style>
  <title>Login Inventori</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-purple-900 to-slate-800">

<?php if ($flash_success): ?>
<script>
Swal.fire({icon:'success',title:'Berhasil',text:'<?= addslashes($flash_success) ?>',timer:2000,showConfirmButton:false});
</script>
<?php endif; ?>

<?php if ($flash_error): ?>
<script>
Swal.fire({icon:'error',title:'Login Gagal',text:'<?= addslashes($flash_error) ?>',confirmButtonColor:'#d33'});
</script>
<?php endif; ?>

<div class="w-full max-w-md">
  <div class="card shadow-2xl border-0 bg-white/10 backdrop-blur-xl rounded-3xl">
    <div class="card-body p-5">
      <div class="text-center mb-4">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 rounded-full inline-block shadow-lg">
          <i class="fas fa-user-lock text-white fs-2"></i>
        </div>
        <h2 class="mt-3 fw-bold text-white">Login Inventori</h2>
        <p class="text-slate-300">Masuk untuk mengelola produk</p>
      </div>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label text-white">Username</label>
          <input type="text" name="username" class="form-control bg-slate-800 text-white border-slate-600"
          placeholder="Masukkan username">
        </div>
        <div class="mb-3">
          <label class="form-label text-white">Password</label>
          <input type="password" name="password" class="form-control bg-slate-800 text-white border-slate-600"
          placeholder="Masukkan password">
        </div>
        <button type="submit" 
          class="btn w-100 py-2 text-white fw-semibold rounded-pill shadow-lg"
          style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border:none;">
          <i class="fas fa-sign-in-alt me-2"></i> Masuk
        </button>
      </form>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
</body>
</html>
