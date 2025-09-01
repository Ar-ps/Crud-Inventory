<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>CRUD Inventori</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <style>
  /* ✅ Navbar Toggler Fix */
  .navbar-toggler {
    border: 2px solid #38bdf8 !important;
    padding: 6px 10px;
    border-radius: 6px;
  }
  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23ffffff' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba(255,255,255,0.9)' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
  }

  /* ✅ Styling menu */
  .nav-link {
    color: #e2e8f0 !important; /* slate-200 */
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  .nav-link:hover {
    color: #ffffff !important;
    background-color: #334155; /* slate-700 */
  }

  .gradient-text {
    background: linear-gradient(135deg, #38bdf8, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
</style>
</head>
<body class="bg-slate-900 text-slate-200">

<!-- Navbar -->
<nav class="navbar shadow-lg sticky-top bg-slate-800">
  <div class="container-fluid px-4">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center fw-bold text-white" href="index.php">
      <i class="fas fa-cubes me-2 text-cyan-400"></i>
      <span class="gradient-text">Inventori CRUD</span>
    </a>

    <!-- Menu langsung tampil (tanpa collapse) -->
    <ul class="navbar-nav ms-auto d-flex flex-row gap-2">
      <li class="nav-item">
        <a class="nav-link px-3 py-2" href="index.php">
          <i class="fas fa-box me-1 text-indigo-400"></i> Barang
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-3 py-2" href="materials.php">
          <i class="fas fa-warehouse me-1 text-emerald-400"></i> Semua Bahan
        </a>
      </li>
      <!-- ✅ Account Setting -->
      <li class="nav-item">
        <a class="nav-link px-3 py-2" href="acount.php">
          <i class="fas fa-user-cog me-1 text-cyan-400"></i> Account Setting
        </a>
      </li>
      <!-- ✅ Tombol Logout -->
      <li class="nav-item">
        <a class="btn btn-danger px-3 py-2 fw-semibold shadow-sm rounded-lg d-flex align-items-center gap-2" href="logout.php" onclick="return confirm('Yakin ingin logout?')">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </div>
</nav>

<!-- Konten Utama -->
<main class="container py-4">
  <div class="text-center mt-5">
    <h1 class="fw-bold text-white">Selamat Datang di Inventori CRUD</h1>
    <p class="text-slate-300">Pilih menu di atas untuk mulai mengelola data barang atau bahan baku.</p>
  </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
