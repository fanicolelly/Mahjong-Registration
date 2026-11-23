<?php
// TODO: logika registrasi (validasi dan insert data user ke database) akan ditambahkan setelah database siap
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up Customer - Mahjong House</title>

  <!-- Google Fonts: Playfair Display & Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Custom CSS Auth -->
  <link rel="stylesheet" href="assets/css/auth.css?v=<?php echo time(); ?>">
</head>
<body>

  <div class="auth-wrapper">
    <!-- Brand Logo -->
    <a href="index.php" class="auth-brand">
      <img src="assets/img/logo.svg" alt="Logo Mahjong House">
      <span>Mahjong <span class="gold">House</span></span>
    </a>

    <!-- Card Sign Up -->
    <div class="auth-card">
      <div class="auth-header">
        <h1 class="auth-title">Sign Up</h1>
      </div>

      <!-- Form Sign Up -->
      <form method="post" action="" class="auth-form">
        <div class="form-group">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" class="form-input" placeholder="Masukkan nama lengkap" required autocomplete="name">
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" required autocomplete="email">
        </div>

        <div class="form-group">
          <label for="no_hp" class="form-label">Nomor HP</label>
          <input type="tel" id="no_hp" name="no_hp" class="form-input" placeholder="08xxxxxxxxxx" required autocomplete="tel">
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input-wrap">
            <input type="password" id="password" name="password" class="form-input" placeholder="Buat password" required autocomplete="new-password">
            <button type="button" class="btn-toggle-password" tabindex="-1">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">Sign Up</button>
      </form>

      <!-- Link Menuju Login -->
      <div class="auth-switch-text">
        Already have an account? <a href="login.php">Log in</a>
      </div>
    </div>

    <!-- Link Kembali ke Home -->
    <a href="index.php" class="auth-back-link">
      <i class="fa-solid fa-arrow-left"></i> Kembali ke Home
    </a>
  </div>

  <!-- Custom JS Auth -->
  <script src="assets/js/auth.js?v=<?php echo time(); ?>"></script>
</body>
</html>
