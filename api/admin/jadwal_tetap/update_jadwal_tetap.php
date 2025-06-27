<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Pastikan semua data yang dibutuhkan ada
if (
    !empty($data->id) &&
    !empty($data->lab_id) &&
    !empty($data->hari) &&
    !empty($data->jam_mulai) &&
    !empty($data->jam_selesai) &&
    !empty($data->nama_matkul) &&
    !empty($data->nama_dosen) &&
    !empty($data->periode_akademik)
) {
    // Query untuk update data
    $query = "UPDATE jadwal_tetap SET 
                lab_id = :lab_id, 
                hari = :hari, 
                jam_mulai = :jam_mulai, 
                jam_selesai = :jam_selesai, 
                nama_matkul = :nama_matkul, 
                nama_dosen = :nama_dosen, 
                periode_akademik = :periode_akademik 
              WHERE 
                id = :id";

    $stmt = $db->prepare($query);

    // Bind data
    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':lab_id', $data->lab_id);
    $stmt->bindParam(':hari', $data->hari);
    $stmt->bindParam(':jam_mulai', $data->jam_mulai);
    $stmt->bindParam(':jam_selesai', $data->jam_selesai);
    $stmt->bindParam(':nama_matkul', $data->nama_matkul);
    $stmt->bindParam(':nama_dosen', $data->nama_dosen);
    $stmt->bindParam(':periode_akademik', $data->periode_akademik);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("status" => true, "message" => "Jadwal tetap berhasil diperbarui."));
    } else {
        http_response_code(503);
        echo json_encode(array("status" => false, "message" => "Gagal memperbarui jadwal."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "Data tidak lengkap."));
}
