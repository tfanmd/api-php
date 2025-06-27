<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Pastikan ID jadwal dikirim
if (!empty($data->id)) {

    $query = "DELETE FROM jadwal_tetap WHERE id = :id";
    $stmt = $db->prepare($query);

    // Bind ID
    $stmt->bindParam(':id', $data->id);

    // Eksekusi query
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(array("status" => true, "message" => "Jadwal tetap berhasil dihapus."));
        } else {
            // ID tidak ditemukan
            http_response_code(404);
            echo json_encode(array("status" => false, "message" => "Jadwal tidak ditemukan."));
        }
    } else {
        http_response_code(503);
        echo json_encode(array("status" => false, "message" => "Gagal menghapus jadwal."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "Data tidak lengkap. ID dibutuhkan."));
}
