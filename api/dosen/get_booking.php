<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/koneksi.php';

// Inisialisasi Database
$database = new Database();
$db = $database->getConnection();

// Ambil user_id dari parameter URL (contoh: ?user_id=1)
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();

// Query untuk mengambil data booking milik user tertentu, di-JOIN dengan tabel labs
// Diurutkan berdasarkan tanggal pembuatan paling baru
$query = "SELECT 
            b.id,
            b.nama_matkul,
            b.kelompok_matkul,
            b.sks,
            b.waktu_mulai,
            b.waktu_selesai,
            b.status,
            b.created_at,
            l.nama_lab,
            l.deskripsi_fasilitas 
          FROM 
            bookings b 
          JOIN 
            labs l ON b.lab_id = l.id 
          WHERE 
            b.user_id = ? 
          ORDER BY 
            b.created_at DESC";

// Persiapkan query
$stmt = $db->prepare($query);

// Bind user_id
$stmt->bindParam(1, $user_id);

// Eksekusi query
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $bookings_arr = array();
    $bookings_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $booking_item = array(
            "id" => $id,
            "nama_matkul" => $nama_matkul,
            "kelompok_matkul" => $kelompok_matkul,
            "sks" => $sks,
            "waktu_mulai" => $waktu_mulai,
            "waktu_selesai" => $waktu_selesai,
            "status" => $status,
            "created_at" => $created_at,
            "nama_lab" => $nama_lab,
            "deskripsi_lab" => $deskripsi_fasilitas
        );
        array_push($bookings_arr["records"], $booking_item);
    }

    http_response_code(200);
    echo json_encode($bookings_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Tidak ada riwayat booking ditemukan."));
}
