<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Pastikan ID pengguna dikirim
if (!empty($data->id)) {

    // Validasi: Jangan biarkan admin menghapus dirinya sendiri
    // Anda bisa menambahkan logika ini jika Anda memiliki session untuk admin yang sedang login.
    // Untuk saat ini, kita akan fokus pada fungsi dasarnya.

    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $db->prepare($query);

    // Bind ID
    $stmt->bindParam(':id', $data->id);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(array("status" => true, "message" => "Pengguna berhasil dihapus."));
        } else {
            // ID tidak ditemukan
            http_response_code(404);
            echo json_encode(array("status" => false, "message" => "Pengguna tidak ditemukan."));
        }
    } else {
        http_response_code(503);
        echo json_encode(array("status" => false, "message" => "Gagal menghapus pengguna."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "ID Pengguna dibutuhkan."));
}
