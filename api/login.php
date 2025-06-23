<?php
// Header untuk mengizinkan Cross-Origin Resource Sharing (CORS) dan menentukan tipe konten
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Include file koneksi dan model
include_once '../config/koneksi.php';
include_once '../models/users.php';

// Inisialisasi objek database dan user
$database = new Database();
$db = $database->getConnection();
$user = new users($db);

// Mengambil data yang di-post dari Android
$data = json_decode(file_get_contents("php://input"));

// Pastikan data tidak kosong
if (!empty($data->username) && !empty($data->password)) {
    // Set properti user
    $user->username = $data->username;
    $user->password = $data->password;

    // Coba untuk login
    if ($user->login()) {
        // Buat array untuk response
        $user_arr = array(
            "status" => true,
            "message" => "Login Berhasil!",
            "id" => $user->id,
            "username" => $user->username,
            "nama_lengkap" => $user->nama_lengkap,
            "role" => $user->role
        );
        // Set HTTP response code - 200 OK
        http_response_code(200);
        echo json_encode($user_arr);
    } else {
        // Set HTTP response code - 401 Unauthorized
        http_response_code(401);
        echo json_encode(array("status" => false, "message" => "Login Gagal. Username atau Password salah."));
    }
} else {
    // Set HTTP response code - 400 Bad Request
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "Login Gagal. Data tidak lengkap."));
}
