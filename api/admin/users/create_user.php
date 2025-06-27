<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

error_reporting(0);
ini_set('display_errors', 0);

include_once '../../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// 1. Validasi data dasar yang wajib untuk semua role
if (empty($data->username) || empty($data->password) || empty($data->nama_lengkap) || empty($data->role)) {
    http_response_code(400); // Bad Request
    echo json_encode(array("status" => false, "message" => "Username, Password, Nama, dan Role wajib diisi."));
    exit(); // Hentikan script
}

// 2. --- VALIDASI BARU YANG LEBIH PINTAR ---
//    Jika rolenya adalah dosen, maka field tambahan menjadi wajib
if ($data->role == 'dosen' && (empty($data->fakultas) || empty($data->nomor_telepon))) {
    http_response_code(400); // Bad Request
    echo json_encode(array("status" => false, "message" => "Untuk Dosen, Fakultas dan Nomor Telepon juga wajib diisi."));
    exit(); // Hentikan script
}

// Jika semua validasi lolos, lanjutkan ke proses database

// 3. Cek username duplikat
$check_query = "SELECT id FROM users WHERE username = :username";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(':username', $data->username);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    http_response_code(409); // Conflict
    echo json_encode(array("status" => false, "message" => "Username sudah digunakan."));
    exit();
}

// 4. Proses INSERT ke database
$query = "INSERT INTO users (username, password, nama_lengkap, role, fakultas, nomor_telepon) 
          VALUES (:username, :password, :nama_lengkap, :role, :fakultas, :nomor_telepon)";

$stmt = $db->prepare($query);

$password_hash = password_hash($data->password, PASSWORD_BCRYPT);
$fakultas = isset($data->fakultas) ? $data->fakultas : null;
$nomor_telepon = isset($data->nomor_telepon) ? $data->nomor_telepon : null;

// Bind data
$stmt->bindParam(':username', $data->username);
$stmt->bindParam(':password', $password_hash);
$stmt->bindParam(':nama_lengkap', $data->nama_lengkap);
$stmt->bindParam(':role', $data->role);
$stmt->bindParam(':fakultas', $fakultas);
$stmt->bindParam(':nomor_telepon', $nomor_telepon);

if ($stmt->execute()) {
    http_response_code(201); // Created
    echo json_encode(array("status" => true, "message" => "Pengguna berhasil dibuat."));
} else {
    http_response_code(503); // Service Unavailable
    echo json_encode(array("status" => false, "message" => "Gagal menyimpan pengguna."));
}
