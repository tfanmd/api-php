<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

// Query untuk mengambil data booking yang statusnya 'Menunggu Persetujuan'
// Kita JOIN 3 tabel untuk mendapatkan semua info yang dibutuhkan
$query = "SELECT 
            b.id as booking_id,
            b.waktu_mulai,
            b.waktu_selesai,
            b.nama_matkul,
            b.sks,
            b.kelompok_matkul,
            b.deskripsi as deskripsi_booking,
            u.nama_lengkap as nama_dosen,
            l.nama_lab
          FROM 
            bookings b
          JOIN 
            users u ON b.user_id = u.id
          JOIN 
            labs l ON b.lab_id = l.id
          WHERE 
            b.status = 'Menunggu Persetujuan'
          ORDER BY 
            b.created_at ASC"; // Tampilkan yang paling lama mengajukan terlebih dahulu

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $requests_arr = array();
    $requests_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $request_item = array(
            "booking_id" => $booking_id,
            "waktu_mulai" => $waktu_mulai,
            "waktu_selesai" => $waktu_selesai,
            "nama_matkul" => $nama_matkul,
            "sks" => $sks,
            "kelompok_matkul" => $kelompok_matkul,
            "deskripsi_booking" => $deskripsi_booking,
            "nama_dosen" => $nama_dosen,
            "nama_lab" => $nama_lab
        );
        array_push($requests_arr["records"], $request_item);
    }
    http_response_code(200);
    echo json_encode($requests_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Tidak ada permintaan booking baru."));
}
