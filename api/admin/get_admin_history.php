<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT 
            b.id as booking_id,
            b.waktu_mulai,
            b.nama_matkul,
            b.kelompok_matkul, -- BARU: Ambil data kelompok_matkul
            b.status,
            u.nama_lengkap as nama_dosen,
            l.nama_lab
          FROM 
            bookings b
          JOIN 
            users u ON b.user_id = u.id
          JOIN 
            labs l ON b.lab_id = l.id
          WHERE 
            b.status IN ('Disetujui', 'Ditolak')
          ORDER BY 
            b.waktu_mulai DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $history_arr = array();
    $history_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $history_item = array(
            "booking_id" => $booking_id,
            "waktu_mulai" => $waktu_mulai,
            "nama_matkul" => $nama_matkul,
            "kelompok_matkul" => $kelompok_matkul, // BARU: Tambahkan ke response JSON
            "status" => $status,
            "nama_dosen" => $nama_dosen,
            "nama_lab" => $nama_lab
        );
        array_push($history_arr["records"], $history_item);
    }
    http_response_code(200);
    echo json_encode($history_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Tidak ada riwayat booking yang sudah diproses."));
}
