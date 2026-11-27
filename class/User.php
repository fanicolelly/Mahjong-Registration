<?php
/**
 * Class User
 * ----------
 * Mengelola tabel `users` dan pengecekan `role`:
 * - Register customer baru (dari register.php) -> otomatis role 'R002' (customer)
 * - Login customer (dari login.php) -> cek role harus 'customer'
 * - Login admin (dari admin/login.php) -> cek role harus 'admin'
 */
class User
{
    private $conn;

    // Terima objek koneksi PDO dari dbConnection->getConnection()
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Membuat ID user otomatis: U001, U002, U003, dst.
     */
    private function generateId()
    {
        $stmt = $this->conn->query("SELECT id_user FROM users ORDER BY LENGTH(id_user) DESC, id_user DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();

        $next = $lastId ? ((int) substr($lastId, 1)) + 1 : 1;
        return 'U' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Registrasi customer baru (dipakai di register.php).
     */
    public function register($nama, $email, $no_telp, $password)
    {
        $nama = trim($nama);
        $email = trim(strtolower($email));
        // Ambil angka saja karena kolom no_telp di database bertipe NUMERIC(20)
        $no_telp = preg_replace('/\D/', '', $no_telp);

        if ($nama === '' || $email === '' || $no_telp === '' || $password === '') {
            return ['success' => false, 'message' => 'Semua field wajib diisi.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid.'];
        }

        // Cek apakah email atau no_telp sudah terdaftar
        $cek = $this->conn->prepare("SELECT id_user FROM users WHERE email = ? OR no_telp = ? LIMIT 1");
        $cek->execute([$email, $no_telp]);
        if ($cek->fetch()) {
            return ['success' => false, 'message' => 'Email atau nomor telepon sudah terdaftar.'];
        }

        // Hash password agar aman dan unik di database
        $id_user = $this->generateId();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $id_role = 'R002'; // Master role: R002 = customer

        $sql = "INSERT INTO users (id_user, nama, email, no_telp, password, id_role)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_user, $nama, $email, $no_telp, $password_hash, $id_role]);

        return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.', 'id_user' => $id_user];
    }

    /**
     * Login dengan pengecekan role.
     * - Untuk login.php: panggil login($email, $password, 'customer')
     * - Untuk admin/login.php: panggil login($email, $password, 'admin')
     */
    public function login($email, $password, $roleHarus = null)
    {
        $email = trim(strtolower($email));

        if ($email === '' || $password === '') {
            return ['success' => false, 'message' => 'Email dan password wajib diisi.'];
        }

        // Ambil data user beserta nama rolenya dari tabel `role`
        $sql = "SELECT u.*, r.role
                FROM users u
                JOIN role r ON u.id_role = r.id_role
                WHERE u.email = ?
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Cek apakah email ada dan password cocok
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email atau password salah.'];
        }

        // Cek kesesuaian role (customer vs admin)
        if ($roleHarus !== null && $user['role'] !== $roleHarus) {
            return ['success' => false, 'message' => 'Akun Anda bukan ' . $roleHarus . ', tidak bisa login di halaman ini.'];
        }

        // Jangan simpan password ke session/return
        unset($user['password']);

        return ['success' => true, 'message' => 'Login berhasil.', 'data' => $user];
    }

    /**
     * Simpan data user ke $_SESSION (sesuai key yang dipakai di index.php).
     */
    public function setSession($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];
    }

    /**
     * Hapus session saat logout.
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }
}
