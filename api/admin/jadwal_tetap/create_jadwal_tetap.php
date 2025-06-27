<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->lab_id) &&
    !empty($data->hari) &&
    !empty($data->jam_mulai) &&
    !empty($data->jam_selesai) &&
    !empty($data->nama_matkul) &&
    !empty($data->nama_dosen) &&
    !empty($data->periode_akademik)
) {
    // --- CEK JADWAL BENTROK ---
    $check_query = "SELECT id FROM jadwal_tetap WHERE lab_id = :lab_id AND hari = :hari AND (
                        (:jam_mulai < jam_selesai AND :jam_selesai > jam_mulai)
                    )";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':lab_id', $data->lab_id);
    $check_stmt->bindParam(':hari', $data->hari);
    $check_stmt->bindParam(':jam_mulai', $data->jam_mulai);
    $check_stmt->bindParam(':jam_selesai', $data->jam_selesai);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        http_response_code(409); // 409 Conflict
        echo json_encode(array("status" => false, "message" => "Jadwal bentrok dengan yang sudah ada."));
        exit();
    }
    // --- AKHIR CEK BENTROK ---


    // Jika tidak bentrok, lanjutkan proses INSERT
    $query = "INSERT INTO jadwal_tetap (lab_id, hari, jam_mulai, jam_selesai, nama_matkul, nama_dosen, periode_akademik) 
              VALUES (:lab_id, :hari, :jam_mulai, :jam_selesai, :nama_matkul, :nama_dosen, :periode_akademik)";

    $stmt = $db->prepare($query);

    // Bind data
    $stmt->bindParam(':lab_id', $data->lab_id);
    $stmt->bindParam(':hari', $data->hari);
    $stmt->bindParam(':jam_mulai', $data->jam_mulai);
    $stmt->bindParam(':jam_selesai', $data->jam_selesai);
    $stmt->bindParam(':nama_matkul', $data->nama_matkul);
    $stmt->bindParam(':nama_dosen', $data->nama_dosen);
    $stmt->bindParam(':periode_akademik', $data->periode_akademik);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(array("status" => true, "message" => "Jadwal tetap berhasil dibuat."));
    } else {
        http_response_code(503); // Service Unavailable
        echo json_encode(array("status" => false, "message" => "Gagal menyimpan jadwal."));
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(array("status" => false, "message" => "Data tidak lengkap."));
}
