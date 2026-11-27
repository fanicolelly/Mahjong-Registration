<?php
/**
 * Class Booking
 * -------------
 * Mengelola tabel `booking` dan `detail_booking`, serta membaca master `waktu` & `status_book`.
 * Master status_book:
 * - 'SB001' => 'draft'
 * - 'SB002' => 'confirmed'
 * - 'SB003' => 'completed'
 */
class Booking
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Membuat ID otomatis berdasarkan tabel, kolom, dan prefix (contoh: B001 atau D001).
     */
    private function generateId($table, $column, $prefix)
    {
        $sql = "SELECT {$column} FROM {$table} ORDER BY LENGTH({$column}) DESC, {$column} DESC LIMIT 1";
        $lastId = $this->conn->query($sql)->fetchColumn();

        $next = $lastId ? ((int) substr($lastId, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil semua jam operasional dari master tabel `waktu` (W001 - W012).
     */
    public function getAllWaktu()
    {
        return $this->conn->query("SELECT * FROM waktu ORDER BY waktu_mulai ASC")->fetchAll();
    }

    /**
     * Ambil semua status dari master tabel `status_book`.
     */
    public function getAllStatus()
    {
        return $this->conn->query("SELECT * FROM status_book ORDER BY id_status_book ASC")->fetchAll();
    }

    /**
     * Ambil daftar jam beserta status apakah masih tersedia atau sudah dibooking
     * pada meja dan tanggal tertentu.
     */
    public function getAvailableWaktu($id_meja, $tanggal_booking)
    {
        $semuaWaktu = $this->getAllWaktu();

        // Cari id_waktu yang sudah terisi di meja & tanggal tersebut
        $sql = "SELECT db.id_waktu
                FROM detail_booking db
                JOIN booking b ON db.id_booking = b.id_booking
                WHERE b.id_meja = ? AND b.tanggal_booking = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_meja, $tanggal_booking]);
        $sudahDibook = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($semuaWaktu as &$slot) {
            $slot['tersedia'] = !in_array($slot['id_waktu'], $sudahDibook);
        }
        return $semuaWaktu;
    }

    /**
     * Simpan booking baru beserta detail jamnya ke tabel `booking` dan `detail_booking`.
     * Contoh $list_id_waktu: ['W001', 'W002']
     */
    public function createBooking($id_user, $id_meja, $tanggal_booking, $list_id_waktu, $id_status_book = 'SB001')
    {
        $list_id_waktu = array_unique($list_id_waktu);
        if (empty($list_id_waktu)) {
            return ['success' => false, 'message' => 'Pilih minimal 1 jam waktu booking.'];
        }

        // 1. Cek meja ada dan berstatus Active ('SM001')
        $stmtMeja = $this->conn->prepare("SELECT harga, id_status_meja FROM meja WHERE id_meja = ? LIMIT 1");
        $stmtMeja->execute([$id_meja]);
        $meja = $stmtMeja->fetch();

        if (!$meja) {
            return ['success' => false, 'message' => 'Meja tidak ditemukan.'];
        }
        if ($meja['id_status_meja'] !== 'SM001') {
            return ['success' => false, 'message' => 'Meja tidak bisa dibooking karena statusnya tidak Active.'];
        }

        // 2. Cek apakah jam yang dipilih bentrok dengan booking lain
        $tandaTanya = implode(',', array_fill(0, count($list_id_waktu), '?'));
        $sqlCek = "SELECT db.id_waktu
                   FROM detail_booking db
                   JOIN booking b ON db.id_booking = b.id_booking
                   WHERE b.id_meja = ? AND b.tanggal_booking = ? AND db.id_waktu IN ({$tandaTanya})";
        $stmtCek = $this->conn->prepare($sqlCek);
        $stmtCek->execute(array_merge([$id_meja, $tanggal_booking], $list_id_waktu));
        if ($stmtCek->fetch()) {
            return ['success' => false, 'message' => 'Salah satu jam yang dipilih sudah dibooking orang lain.'];
        }

        try {
            // Pakai transaction supaya insert ke booking dan detail_booking sukses bersamaan
            $this->conn->beginTransaction();

            // 3. Insert ke tabel `booking`
            $id_booking = $this->generateId('booking', 'id_booking', 'B');
            $sqlBooking = "INSERT INTO booking (id_booking, id_user, id_meja, tanggal_booking, id_status_book)
                           VALUES (?, ?, ?, ?, ?)";
            $this->conn->prepare($sqlBooking)->execute([$id_booking, $id_user, $id_meja, $tanggal_booking, $id_status_book]);

            // 4. Insert setiap jam yang dipilih ke tabel `detail_booking`
            $harga_per_jam = $meja['harga'];
            $detailPertama = $this->generateId('detail_booking', 'id_detail', 'D');
            $nomorDetail = (int) substr($detailPertama, 1);

            $sqlDetail = "INSERT INTO detail_booking (id_detail, id_booking, id_waktu, harga_per_jam)
                          VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($sqlDetail);

            foreach ($list_id_waktu as $id_waktu) {
                $id_detail = 'D' . str_pad($nomorDetail, 3, '0', STR_PAD_LEFT);
                $stmtDetail->execute([$id_detail, $id_booking, $id_waktu, $harga_per_jam]);
                $nomorDetail++;
            }

            $this->conn->commit();

            return [
                'success'     => true,
                'message'     => 'Booking berhasil dibuat.',
                'id_booking'  => $id_booking,
                'total_harga' => $harga_per_jam * count($list_id_waktu),
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Gagal membuat booking: ' . $e->getMessage()];
        }
    }

    /**
     * Ambil daftar booking milik 1 user (untuk halaman My Bookings).
     */
    public function getByUser($id_user)
    {
        $sql = "SELECT b.*, m.nama_meja, sb.status_book,
                       COUNT(db.id_detail) AS jumlah_jam,
                       COALESCE(SUM(db.harga_per_jam), 0) AS total_harga
                FROM booking b
                JOIN meja m ON b.id_meja = m.id_meja
                JOIN status_book sb ON b.id_status_book = sb.id_status_book
                LEFT JOIN detail_booking db ON b.id_booking = db.id_booking
                WHERE b.id_user = ?
                GROUP BY b.id_booking
                ORDER BY b.waktu_booking DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_user]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil semua data booking (untuk halaman Admin).
     */
    public function getAll()
    {
        $sql = "SELECT b.*, u.nama AS nama_user, m.nama_meja, sb.status_book,
                       COUNT(db.id_detail) AS jumlah_jam,
                       COALESCE(SUM(db.harga_per_jam), 0) AS total_harga
                FROM booking b
                JOIN users u ON b.id_user = u.id_user
                JOIN meja m ON b.id_meja = m.id_meja
                JOIN status_book sb ON b.id_status_book = sb.id_status_book
                LEFT JOIN detail_booking db ON b.id_booking = db.id_booking
                GROUP BY b.id_booking
                ORDER BY b.waktu_booking DESC";
        return $this->conn->query($sql)->fetchAll();
    }

    /**
     * Ambil rincian jam dari 1 id_booking.
     */
    public function getDetail($id_booking)
    {
        $sql = "SELECT db.*, w.waktu_mulai, w.waktu_selesai
                FROM detail_booking db
                JOIN waktu w ON db.id_waktu = w.id_waktu
                WHERE db.id_booking = ?
                ORDER BY w.waktu_mulai ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_booking]);
        return $stmt->fetchAll();
    }

    /**
     * Ubah status booking (SB001 draft / SB002 confirmed / SB003 completed).
     */
    public function updateStatus($id_booking, $id_status_book)
    {
        $stmt = $this->conn->prepare("UPDATE booking SET id_status_book = ? WHERE id_booking = ?");
        $stmt->execute([$id_status_book, $id_booking]);
        return ['success' => true, 'message' => 'Status booking berhasil diubah.'];
    }

    /**
     * Hapus booking (detail_booking otomatis ikut terhapus karena ON DELETE CASCADE).
     */
    public function delete($id_booking)
    {
        $stmt = $this->conn->prepare("DELETE FROM booking WHERE id_booking = ?");
        $stmt->execute([$id_booking]);
        return ['success' => true, 'message' => 'Booking berhasil dihapus.'];
    }
}
