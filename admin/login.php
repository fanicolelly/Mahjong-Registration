<?php
// TODO: logika login admin (hanya akun dengan role admin yang boleh masuk, lalu diarahkan ke dashboard.php, when database udh siap)
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Mahjong House</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/auth.css?v=<?php echo time(); ?>">
</head>
<body>

  <div class="auth-wrapper">
    <a href="../index.php" class="auth-brand">
      <img src="../assets/img/logo.svg" alt="Logo Mahjong House">
      <span>Mahjong <span class="gold">House</span></span>
    </a>

    <!-- Card Admin Login -->
    <div class="auth-card admin-card">
      <div class="auth-header">
        <span class="admin-badge">
          <i class="fa-solid fa-shield-halved"></i> Area Admin
        </span>
        <h1 class="auth-title">Admin Login</h1>
      </div>

      <!-- Form Admin Login -->
      <form method="post" action="" class="auth-form">
        <div class="form-group">
          <label for="email" class="form-label">Email Admin</label>
          <input type="email" id="email" name="email" class="form-input" placeholder="admin@mahjonghouse.id" required autocomplete="email">
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input-wrap">
            <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password admin" required autocomplete="current-password">
            <button type="button" class="btn-toggle-password" tabindex="-1">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">Login</button>
      </form>
    </div>

    <!-- Link Kembali ke Home -->
    <a href="../index.php" class="auth-back-link">
      <i class="fa-solid fa-arrow-left"></i> Kembali ke Home
    </a>
  </div>

  <!-- Custom JS Auth -->
  <script src="../assets/js/auth.js?v=<?php echo time(); ?>"></script>
</body>
</html>
