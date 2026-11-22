<?php
// sesi PHP native
session_start();

// Cek status user authentication dari session
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn && isset($_SESSION['nama']) ? $_SESSION['nama'] : '';
$userRole = $isLoggedIn && isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';

// Cek notifikasi session (flash message)
$notifMessage = isset($_SESSION['notif']) ? $_SESSION['notif'] : null;
if ($notifMessage) {
    // Hapus notif agar hanya tampil satu kali
    unset($_SESSION['notif']);
}

// Tentukan target URL link booking dan riwayat berdasarkan status login
$bookingUrl = $isLoggedIn ? 'booking/form.php' : 'login.php?pesan=harus_login';
$myBookingsUrl = $isLoggedIn ? 'riwayat/index.php' : 'login.php?pesan=harus_login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to Mahjong House - Book Your Mahjong Table Now!</title>

  <!-- Google Fonts: Playfair Display & Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Custom CSS Home Page dengan Cache-Busting (?v=time) agar update CSS langsung terbaca browser -->
  <link rel="stylesheet" href="assets/css/home.css?v=<?php echo time(); ?>">
</head>
<body>

  <!-- TOAST NOTIFICATION (Pesan Sesi Dinamis) -->
  <?php if (!empty($notifMessage)): ?>
    <div class="toast-container">
      <div id="toastNotification" class="toast">
        <div class="toast-content">
          <i class="fa-solid fa-circle-check toast-icon"></i>
          <span><?php echo htmlspecialchars($notifMessage, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <button type="button" id="toastCloseBtn" class="toast-close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>
  <?php endif; ?>

  <!-- NAVBAR (nanti dipindah ke includes/header.php) -->
  <header class="navbar-wrapper">
    <div class="container">
      <nav class="navbar">
        <!-- Brand Logo (Kiri) -->
        <a href="index.php" class="brand">
          <img src="assets/img/logo.svg" alt="Logo Mahjong House" class="brand-icon">
          <span>Mahjong <span class="gold">House</span></span>
        </a>

        <!-- Hamburger Button untuk Tampilan Mobile -->
        <button class="hamburger" id="navHamburger">
          <span></span>
          <span></span>
          <span></span>
        </button>

        <!-- Container Menu (Tengah) & Tombol Aksi (Kanan) -->
        <div class="nav-menu-container" id="navMenuContainer">
          <ul class="nav-menu">
            <li><a href="index.php" class="nav-link active">Home</a></li>
            <li><a href="tutorial.php" class="nav-link">Tutorial</a></li>
            <li><a href="<?php echo $bookingUrl; ?>" class="nav-link">Booking</a></li>
            <li><a href="<?php echo $myBookingsUrl; ?>" class="nav-link">My Bookings</a></li>
          </ul>

          <div class="nav-actions">
            <?php if (!$isLoggedIn): ?>
              <!-- Pengguna Belum Login -->
              <a href="login.php" class="btn-nav-login">Login</a>
              <a href="register.php" class="btn-nav-signup">Sign Up</a>
            <?php else: ?>
              <!-- Pengguna Sudah Login -->
              <div class="user-dropdown">
                <button type="button" class="user-toggle" id="userDropdownToggle">
                  <span class="user-avatar">
                    <i class="fa-solid fa-user"></i>
                  </span>
                  <span>Hi, <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                  <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
                </button>
                <div class="dropdown-menu" id="userDropdownMenu">
                  <div class="dropdown-header">Akun Saya</div>
                  <?php if ($userRole === 'admin'): ?>
                    <a href="admin/dashboard.php" class="dropdown-item">
                      <i class="fa-solid fa-gauge-high"></i> Dashboard Admin
                    </a>
                  <?php endif; ?>
                  <a href="profil.php" class="dropdown-item">
                    <i class="fa-solid fa-id-card"></i> Profil
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="logout.php" class="dropdown-item">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                  </a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <main>
    <!-- HERO SECTION -->
    <section class="hero">
      <div class="container">
        <div class="hero-grid">
          <!-- Kolom Teks Hero -->
          <div class="hero-content">
            <h1 class="hero-title">
              Welcome to <span class="gold-gradient">Mahjong House</span>
            </h1>
            <p class="hero-tagline">
              Book your mahjong table now!
            </p>
            <p class="hero-subtitle">
              Enjoy mahjong at automatic shuffle tables, in air-conditioned private rooms with a warm oriental atmosphere.
            </p>
            <div>
              <a href="<?php echo $bookingUrl; ?>" class="btn-primary">
                <i class="fa-solid fa-calendar-check"></i> Book Your Table
              </a>
            </div>
          </div>

          <!-- Kolom Kartu Availability -->
          <div class="hero-visual">
            <div class="availability-card">
              <div class="card-top-bar">
                <span class="availability-label">Availability</span>
                <span class="status-badge">Buka Hari Ini</span>
              </div>
              <div class="card-content-wrap">
                <div class="card-features-list">
                  <div class="card-feature-item">
                    <div class="feature-bullet">
                      <i class="fa-solid fa-table-cells-large"></i>
                    </div>
                    <span>8 Meja Otomatis</span>
                  </div>
                  <div class="card-feature-item">
                    <div class="feature-bullet">
                      <i class="fa-solid fa-wind"></i>
                    </div>
                    <span>Full AC &amp; Bebas Asap Rokok</span>
                  </div>
                  <div class="card-feature-item">
                    <div class="feature-bullet">
                      <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <span>Free Snack &amp; Free-Flow Mineral</span>
                  </div>
                </div>

                <div class="card-price-row">
                  <div class="price-text">
                    Rp 40.000 <span class="unit">/ jam</span>
                  </div>
                  <a href="<?php echo $bookingUrl; ?>" class="btn-card-select">
                    Pilih Meja <i class="fa-solid fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- KUNJUNGI KAMI (LOKASI & JAM OPERASIONAL) -->
    <section class="location-section section-padding">
      <div class="container">
        <div class="section-header text-center">
          <span class="section-tag">Kunjungi Kami</span>
          <h2 class="section-title">Lokasi &amp; Jam Operasional</h2>
        </div>

        <div class="location-box">
          <!-- Detail Informasi Lokasi & Kontak -->
          <div class="location-info-list">
            <div class="location-item">
              <div class="location-icon-wrap">
                <i class="fa-solid fa-location-dot"></i>
              </div>
              <div class="location-detail">
                <h4>Alamat Lengkap</h4>
                <p>Jl. Boulevard No. 88, Pakuwon City, Surabaya, Jawa Timur 60112</p>
              </div>
            </div>

            <div class="location-item">
              <div class="location-icon-wrap">
                <i class="fa-solid fa-clock"></i>
              </div>
              <div class="location-detail">
                <h4>Jam Operasional</h4>
                <p>Senin – Minggu: 10:00 – 22:00 WIB (Buka Setiap Hari)</p>
              </div>
            </div>

            <div class="location-item">
              <div class="location-icon-wrap">
                <i class="fa-solid fa-phone"></i>
              </div>
              <div class="location-detail">
                <h4>Kontak &amp; Reservasi</h4>
                <p>WhatsApp: +62 812-3456-7890 | Telepon: (031) 5928888</p>
              </div>
            </div>
          </div>

          <!-- Aksi Google Maps -->
          <div class="location-maps-cta">
            <h3>Ingin Lihat Lokasi Mahjong House?</h3>
            <a href="https://maps.google.com/?q=Pakuwon+City+Surabaya" target="_blank" rel="noopener noreferrer" class="btn-maps">
              <i class="fa-solid fa-map-location-dot"></i> Buka Google Maps
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER (nanti dipindah ke includes/footer.php) -->
  <footer class="footer">
    <div class="container">
      <h3 class="footer-heading">Hubungi Kami</h3>
      <div class="footer-contacts-wrap">
        <div class="footer-contact-row">
          <i class="fa-solid fa-location-dot"></i>
          <span>Jl. Boulevard No. 88, Pakuwon City, Surabaya, Jawa Timur 60112</span>
        </div>
        <div class="footer-contact-row">
          <i class="fa-solid fa-phone"></i>
          <span>WhatsApp: +62 812-3456-7890 | Telepon: (031) 5928888</span>
        </div>
        <div class="footer-contact-row">
          <i class="fa-solid fa-envelope"></i>
          <span>info@mahjonghouse.id</span>
        </div>
      </div>
    </div>
  </footer>

  <!-- Custom JavaScript Home Page dengan Cache-Busting -->
  <script src="assets/js/home.js?v=<?php echo time(); ?>"></script>
</body>
</html>
