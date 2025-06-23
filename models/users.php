<?php
class users
{
    // Koneksi Database dan nama tabel
    private $conn;
    private $table_name = "users";

    // Properti Objek User
    public $id;
    public $username;
    public $password;
    public $nama_lengkap;
    public $role;

    // Constructor dengan $db sebagai koneksi database
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Fungsi login user
    function login()
    {
        // Query untuk mengambil user berdasarkan username
        $query = "SELECT id, username, password, nama_lengkap, role FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";

        // Persiapkan query statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':username', $this->username);

        // Eksekusi query
        $stmt->execute();

        // Cek apakah user ditemukan
        if ($stmt->rowCount() > 0) {
            // Ambil detail user
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifikasi password
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->nama_lengkap = $row['nama_lengkap'];
                $this->role = $row['role'];
                return true; // Login berhasil
            }
        }

        return false; // Login gagal
    }
}
