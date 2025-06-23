<?php
class Database
{
    // Spesifikasi koneksi database
    private $host = "localhost";
    private $db_name = "andro-moprog";
    private $username = "root"; // Ganti jika username Anda berbeda
    private $password = ""; // Ganti jika password Anda berbeda
    public $conn;

    // Fungsi untuk mendapatkan koneksi
    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
