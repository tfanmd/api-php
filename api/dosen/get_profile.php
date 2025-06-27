<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();

$query = "SELECT id, username, nama_lengkap, nip, fakultas, jenis_kelamin, nomor_telepon, url_foto_profil FROM users WHERE id = ? LIMIT 0,1";

$stmt = $db->prepare($query);
$stmt->bindParam(1, $user_id);
$stmt->execute();

$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_data) {
    http_response_code(200);
    echo json_encode($user_data);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "User tidak ditemukan."));
}
