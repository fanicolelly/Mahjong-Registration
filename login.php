<?php
// Cek pesan info dari parameter URL (whitelist pesan)
$infoMessage = '';
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'harus_login') {
        $infoMessage = 'Silakan login atau daftar untuk melanjutkan booking.';
    } elseif ($_GET['pesan'] === 'registrasi_sukses') {
        $infoMessage = 'Akun berhasil dibuat, silakan login.';
    }
}

// TODO: logika login (cek email dan password ke database when database udah ada)
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Customer - Mahjong House</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- Tailwind CSS v4 CDN & Custom Layer Components -->
  <?php include 'includes/tailwind.php'; ?>
</head>
<body class="font-body">

  <div class="auth-wrapper">
    <!-- Brand Logo -->
    <a href="index.php" class="auth-brand">
      <img src="assets/img/logo.svg" alt="Logo Mahjong House">
      <span>Mahjong <span class="gold">House</span></span>
    </a>

    <!-- Card Login -->
    <div class="auth-card">
      <div class="auth-header">
        <h1 class="auth-title">Login</h1>
      </div>

      <!-- Kotak Pesan Info dari URL -->
      <?php if (!empty($infoMessage)): ?>
        <div class="auth-info-box">
          <i class="fa-solid fa-circle-info"></i>
          <span><?php echo htmlspecialchars($infoMessage, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
      <?php endif; ?>

      <!-- Form Login -->
      <form method="post" action="" class="auth-form">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" required autocomplete="email">
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input-wrap">
            <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" required autocomplete="current-password">
            <button type="button" class="btn-toggle-password" tabindex="-1">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">Login</button>
      </form>

      <!-- Link Registrasi -->
      <div class="auth-switch-text">
        Don't have an account? <a href="register.php">Sign up</a>
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
