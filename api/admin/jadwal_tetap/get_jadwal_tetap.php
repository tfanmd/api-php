<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

// Query untuk mengambil semua jadwal tetap, di-JOIN dengan nama lab
// Diurutkan berdasarkan hari (Senin-Sabtu) lalu berdasarkan jam mulai
$query = "SELECT 
            jt.id, 
            jt.hari, 
            jt.jam_mulai, 
            jt.jam_selesai, 
            jt.nama_matkul, 
            jt.nama_dosen, 
            jt.periode_akademik,
            l.nama_lab
          FROM 
            jadwal_tetap jt
          JOIN 
            labs l ON jt.lab_id = l.id
          ORDER BY 
            FIELD(jt.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), 
            jt.jam_mulai ASC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $jadwal_arr = array();
    $jadwal_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $jadwal_item = array(
            "id" => $id,
            "nama_lab" => $nama_lab,
            "hari" => $hari,
            "jam_mulai" => date("H:i", strtotime($jam_mulai)), // Format jam
            "jam_selesai" => date("H:i", strtotime($jam_selesai)), // Format jam
            "nama_matkul" => $nama_matkul,
            "nama_dosen" => $nama_dosen,
            "periode_akademik" => $periode_akademik
        );
        array_push($jadwal_arr["records"], $jadwal_item);
    }
    http_response_code(200);
    echo json_encode($jadwal_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Belum ada jadwal tetap yang dibuat."));
}
