/* Fitur: Tombol Tampil/Sembunyikan Password */

document.addEventListener('DOMContentLoaded', () => {
  // Ambil semua tombol toggle password di halaman
  const toggleButtons = document.querySelectorAll('.btn-toggle-password');

  toggleButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      // Cari input password yang berada di dalam wrapper yang sama
      const wrapper = btn.closest('.password-input-wrap');
      if (!wrapper) return;

      const passwordInput = wrapper.querySelector('input');
      const icon = btn.querySelector('i');

      if (!passwordInput) return;

      // Toggle tipe input antara password dan text
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        if (icon) {
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        }
      } else {
        passwordInput.type = 'password';
        if (icon) {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      }
    });
  });
});
