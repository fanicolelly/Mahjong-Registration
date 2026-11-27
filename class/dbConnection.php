<?php
/**
 * Class dbConnection
 * ------------------
 * Mengatur koneksi PHP ke database MySQL menggunakan PDO.
 * Cara pakai:
 *   $dbConnect = new dbConnection('mahjong');
 *   $conn = $dbConnect->getConnection();
 */
class dbConnection
{
    // Data members (properti koneksi)
    private $database;
    private $address;
    private $username;
    private $password;
    private $connection;

    /**
     * Constructor: otomatis jalan saat membuat objek new dbConnection(...)
     * Parameter address, username, dan password punya nilai default,
     * jadi cukup panggil: new dbConnection('mahjong')
     *
     * Catatan AMPPS: kalau MySQL kamu pakai password 'mysql', ubah $password = 'mysql'
     */
    public function __construct($database = 'mahjong', $address = '127.0.0.1', $username = 'root', $password = '')
    {
        $this->database = $database;
        $this->address  = $address;
        $this->username = $username;
        $this->password = $password;

        try {
            $conn = new PDO(
                "mysql:host=" . $this->address . ";dbname=" . $this->database . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            // Munculkan error SQL sebagai Exception agar mudah didebug
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Hasil query otomatis berupa array asosiatif (contoh: $row['nama'])
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $this->connection = $conn;
        } catch (PDOException $e) {
            // Hentikan program dan tampilkan pesan jika gagal konek ke MySQL
            die('Koneksi database gagal: ' . $e->getMessage());
        }
    }

    /**
     * Mengambil objek koneksi PDO agar bisa dipakai oleh class lain (User, Meja, Booking).
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
