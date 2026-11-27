<?php
/**
 * Class Meja
 * ----------
 * Mengelola CRUD tabel `meja` untuk Admin & daftar meja untuk Customer.
 * Master status_meja:
 * - 'SM001' => 'Active' (bisa dibooking)
 * - 'SM002' => 'Inactive' (tidak bisa dibooking)
 * - 'SM003' => 'Under Maintenance' (tidak bisa dibooking)
 */
class Meja
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Membuat ID meja otomatis: M001, M002, M003, dst.
     */
    private function generateId()
    {
        $stmt = $this->conn->query("SELECT id_meja FROM meja ORDER BY LENGTH(id_meja) DESC, id_meja DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();

        $next = $lastId ? ((int) substr($lastId, 1)) + 1 : 1;
        return 'M' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * READ: Ambil semua meja beserta statusnya (dipakai di Admin & tampilan list meja User).
     * Ada kolom tambahan 'bisa_dibooking' (true jika SM001 / Active).
     */
    public function getAll()
    {
        $sql = "SELECT m.*, sm.status_meja
                FROM meja m
                JOIN status_meja sm ON m.id_status_meja = sm.id_status_meja
                ORDER BY LENGTH(m.id_meja) ASC, m.id_meja ASC";
        $rows = $this->conn->query($sql)->fetchAll();

        foreach ($rows as &$row) {
            $row['bisa_dibooking'] = ($row['id_status_meja'] === 'SM001');
        }
        return $rows;
    }

    /**
     * READ: Ambil 1 data meja berdasarkan id_meja.
     */
    public function getById($id_meja)
    {
        $sql = "SELECT m.*, sm.status_meja
                FROM meja m
                JOIN status_meja sm ON m.id_status_meja = sm.id_status_meja
                WHERE m.id_meja = ?
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_meja]);
        $row = $stmt->fetch();

        if ($row) {
            $row['bisa_dibooking'] = ($row['id_status_meja'] === 'SM001');
        }
        return $row;
    }

    /**
     * Ambil semua pilihan status dari master tabel `status_meja` (untuk dropdown Admin).
     */
    public function getAllStatus()
    {
        return $this->conn->query("SELECT * FROM status_meja ORDER BY id_status_meja ASC")->fetchAll();
    }

    /**
     * Cek apakah meja berstatus Active ('SM001') sehingga boleh dibooking.
     */
    public function isBookable($id_meja)
    {
        $meja = $this->getById($id_meja);
        return $meja && $meja['id_status_meja'] === 'SM001';
    }

    /**
     * CREATE (Admin): Tambah meja baru.
     */
    public function create($nama_meja, $harga, $id_status_meja = 'SM001')
    {
        $nama_meja = trim($nama_meja);
        if ($nama_meja === '' || !is_numeric($harga) || $harga <= 0) {
            return ['success' => false, 'message' => 'Nama meja dan harga harus valid.'];
        }

        $id_meja = $this->generateId();
        $sql = "INSERT INTO meja (id_meja, nama_meja, harga, id_status_meja) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_meja, $nama_meja, $harga, $id_status_meja]);

        return ['success' => true, 'message' => 'Meja berhasil ditambahkan.', 'id_meja' => $id_meja];
    }

    /**
     * UPDATE (Admin): Ubah nama, harga, dan status meja.
     * id_meja tidak diubah (hanya dipakai di WHERE).
     * Aturan status:
     * - Jika meja sudah 'SM002' (Inactive), statusnya tidak bisa diubah/diaktifkan lagi.
     * - Jika meja 'SM003' (Under Maintenance), bisa diubah kembali menjadi 'SM001' (Active) atau 'SM002' (Inactive).
     */
    public function update($id_meja, $nama_meja, $harga, $id_status_meja)
    {
        $mejaLama = $this->getById($id_meja);
        if (!$mejaLama) {
            return ['success' => false, 'message' => 'Meja tidak ditemukan.'];
        }

        $nama_meja = trim($nama_meja);
        if ($nama_meja === '' || !is_numeric($harga) || $harga <= 0) {
            return ['success' => false, 'message' => 'Nama meja dan harga harus valid.'];
        }

        if (!in_array($id_status_meja, ['SM001', 'SM002', 'SM003'], true)) {
            return ['success' => false, 'message' => 'Status meja tidak valid.'];
        }

        // Cek aturan transisi status: status Inactive (SM002) tidak boleh diubah/diaktifkan lagi
        if ($mejaLama['id_status_meja'] === 'SM002' && $id_status_meja !== 'SM002') {
            return ['success' => false, 'message' => 'Meja yang sudah Inactive tidak bisa diaktifkan lagi.'];
        }

        $sql = "UPDATE meja SET nama_meja = ?, harga = ?, id_status_meja = ? WHERE id_meja = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$nama_meja, $harga, $id_status_meja, $id_meja]);

        return ['success' => true, 'message' => 'Data meja berhasil diubah.'];
    }

    /**
     * DELETE (Admin): Soft delete meja dengan mengubah statusnya menjadi Inactive ('SM002').
     * Data meja tetap tersimpan di database agar riwayat booking lama tidak rusak.
     */
    public function delete($id_meja)
    {
        if (!$this->getById($id_meja)) {
            return ['success' => false, 'message' => 'Meja tidak ditemukan.'];
        }

        $stmt = $this->conn->prepare("UPDATE meja SET id_status_meja = 'SM002' WHERE id_meja = ?");
        $stmt->execute([$id_meja]);

        return ['success' => true, 'message' => 'Meja berhasil dinonaktifkan.'];
    }
}
