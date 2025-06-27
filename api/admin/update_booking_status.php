<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Pastikan data yang dibutuhkan ada
if (!empty($data->booking_id) && !empty($data->status)) {
    // Pastikan status yang dikirim valid
    if ($data->status == 'Disetujui' || $data->status == 'Ditolak') {
        $query = "UPDATE bookings SET status = :status WHERE id = :booking_id";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':status', $data->status);
        $stmt->bindParam(':booking_id', $data->booking_id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("status" => true, "message" => "Status booking berhasil diupdate."));
        } else {
            http_response_code(503);
            echo json_encode(array("status" => false, "message" => "Gagal mengupdate status booking."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => false, "message" => "Nilai status tidak valid."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "Data tidak lengkap."));
}
