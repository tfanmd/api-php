<?php
// Header yang diperlukan
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include file database dan model
include_once '../../config/koneksi.php'; // Sesuaikan dengan nama file koneksi Anda
include_once '../../models/lab.php';     // Kita akan buat model Lab.php nanti
include_once '../../models/booking.php'; // Kita akan buat model Booking.php nanti

// Inisialisasi Database
$database = new Database();
$db = $database->getConnection();

// Ambil tanggal dari parameter URL (contoh: ?date=2025-06-24)
$date = isset($_GET['date']) ? $_GET['date'] : die();

// --- Logika Utama ---

// 1. Definisikan semua slot waktu yang mungkin ada dalam sehari
$all_time_slots = ["08:00-10:35", "10:40-13:20", "13:25-16:00", "16:10-18:00"]; // Sesuaikan dengan slot Anda

// 2. Ambil semua data booking pada tanggal yang diminta
$stmt_bookings = $db->prepare("SELECT lab_id, DATE_FORMAT(waktu_mulai, '%H:%i') as jam_mulai, DATE_FORMAT(waktu_selesai, '%H:%i') as jam_selesai FROM bookings WHERE DATE(waktu_mulai) = ? AND status = 'Disetujui'");
$stmt_bookings->bindParam(1, $date);
$stmt_bookings->execute();

$booked_slots = [];
while ($row = $stmt_bookings->fetch(PDO::FETCH_ASSOC)) {
    $booked_slots[] = $row['lab_id'] . "_" . $row['jam_mulai'] . "-" . $row['jam_selesai'];
}

// 3. Ambil semua data lab
$stmt_labs = $db->prepare("SELECT id, nama_lab, deskripsi_fasilitas FROM labs");
$stmt_labs->execute();

if ($stmt_labs->rowCount() > 0) {
    $labs_arr = array();
    $labs_arr["records"] = array();

    while ($row_lab = $stmt_labs->fetch(PDO::FETCH_ASSOC)) {
        extract($row_lab); // $id, $nama_lab, $deskripsi_fasilitas

        $time_slots_status = [];
        // 4. Untuk setiap lab, cek status setiap slot waktu
        foreach ($all_time_slots as $slot) {
            $key = $id . "_" . $slot; // Buat kunci unik, contoh: "2_08:00-10:00"

            $time_slots_status[] = array(
                "slot" => $slot,
                "is_available" => !in_array($key, $booked_slots) // Jika tidak ada di array booked, maka available
            );
        }

        $lab_item = array(
            "id" => $id,
            "nama_lab" => $nama_lab,
            "deskripsi" => $deskripsi_fasilitas,
            "time_slots" => $time_slots_status
        );

        array_push($labs_arr["records"], $lab_item);
    }

    // Set response code - 200 OK
    http_response_code(200);
    echo json_encode($labs_arr);
} else {
    // Set response code - 404 Not found
    http_response_code(404);
    echo json_encode(array("message" => "Tidak ada lab ditemukan."));
}
