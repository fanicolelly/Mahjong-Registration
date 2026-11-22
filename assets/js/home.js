document.addEventListener('DOMContentLoaded', () => {
  /* TOAST NOTIFICATION (Auto-dismiss & Manual Close) */
  const toast = document.getElementById('toastNotification');
  const toastCloseBtn = document.getElementById('toastCloseBtn');

  if (toast) {
    // Tampilkan toast dengan transisi masuk
    setTimeout(() => {
      toast.classList.add('show');
    }, 100);

    // Otomatis tutup setelah 4 detik (~4000ms)
    const toastTimer = setTimeout(() => {
      dismissToast();
    }, 4000);

    // Tombol close manual
    if (toastCloseBtn) {
      toastCloseBtn.addEventListener('click', () => {
        clearTimeout(toastTimer);
        dismissToast();
      });
    }

    // Fungsi menutup dan menghapus toast dari DOM
    function dismissToast() {
      toast.classList.remove('show');
      toast.classList.add('hide');
      setTimeout(() => {
        toast.remove();
      }, 350);
    }
  }

  /* NAVBAR MOBILE (Hamburger Menu Toggle) */
  const hamburger = document.getElementById('navHamburger');
  const navMenuContainer = document.getElementById('navMenuContainer');

  if (hamburger && navMenuContainer) {
    hamburger.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = navMenuContainer.classList.toggle('open');
      hamburger.classList.toggle('is-active', isOpen);
      hamburger.setAttribute('aria-expanded', isOpen);
    });

    // Tutup menu jika klik di luar navbar
    document.addEventListener('click', (e) => {
      if (!navMenuContainer.contains(e.target) && !hamburger.contains(e.target)) {
        navMenuContainer.classList.remove('open');
        hamburger.classList.remove('is-active');
        hamburger.setAttribute('aria-expanded', 'false');
      }
    });

    // Tutup menu ketika link navigasi diklik pada tampilan HP
    const navLinks = navMenuContainer.querySelectorAll('.nav-link');
    navLinks.forEach((link) => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 992) {
          navMenuContainer.classList.remove('open');
          hamburger.classList.remove('is-active');
          hamburger.setAttribute('aria-expanded', 'false');
        }
      });
    });
  }

  /* USER PROFILE DROPDOWN (Ketika User Sudah Login) */
  const userToggle = document.getElementById('userDropdownToggle');
  const userDropdownMenu = document.getElementById('userDropdownMenu');

  if (userToggle && userDropdownMenu) {
    userToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdownMenu.classList.toggle('active');
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', (e) => {
      if (!userToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
        userDropdownMenu.classList.remove('active');
      }
    });

    // Tutup dropdown jika tekan tombol Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        userDropdownMenu.classList.remove('active');
      }
    });
  }
});