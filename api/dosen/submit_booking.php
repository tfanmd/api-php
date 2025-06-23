<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/koneksi.php'; // Sesuaikan dengan nama file koneksi Anda

// Mengambil data JSON yang dikirim dari Android
$data = json_decode(file_get_contents("php://input"));

// Pastikan data yang dibutuhkan tidak kosong
if (
    !empty($data->user_id) &&
    !empty($data->lab_id) &&
    !empty($data->waktu_mulai) &&
    !empty($data->waktu_selesai) &&
    !empty($data->nama_matkul) &&
    !empty($data->kelompok_matkul) &&
    !empty($data->sks)
) {
    // Inisialisasi Database
    $database = new Database();
    $db = $database->getConnection();

    // Query untuk memasukkan data booking baru
    $query = "INSERT INTO bookings 
              (user_id, lab_id, kelompok_matkul, waktu_mulai, waktu_selesai, nama_matkul, sks, deskripsi, status) 
              VALUES 
              (:user_id, :lab_id, :kelompok_matkul, :waktu_mulai, :waktu_selesai, :nama_matkul, :sks, :deskripsi, 'Menunggu Persetujuan')";
    // Persiapkan query
    $stmt = $db->prepare($query);

    // Membersihkan data (Sanitize) - Opsional tapi sangat disarankan
    $user_id = htmlspecialchars(strip_tags($data->user_id));
    $lab_id = htmlspecialchars(strip_tags($data->lab_id));
    $waktu_mulai = htmlspecialchars(strip_tags($data->waktu_mulai));
    $waktu_selesai = htmlspecialchars(strip_tags($data->waktu_selesai));
    $nama_matkul = htmlspecialchars(strip_tags($data->nama_matkul));
    $kelompok_matkul = htmlspecialchars(strip_tags($data->kelompok_matkul));
    $sks = htmlspecialchars(strip_tags($data->sks));
    $deskripsi = !empty($data->deskripsi) ? htmlspecialchars(strip_tags($data->deskripsi)) : "";

    // Bind nilai ke parameter query
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":lab_id", $lab_id);
    $stmt->bindParam(":waktu_mulai", $waktu_mulai);
    $stmt->bindParam(":waktu_selesai", $waktu_selesai);
    $stmt->bindParam(":nama_matkul", $nama_matkul);
    $stmt->bindParam(":kelompok_matkul", $kelompok_matkul);
    $stmt->bindParam(":sks", $sks);
    $stmt->bindParam(":deskripsi", $deskripsi);

    // Eksekusi query
    if ($stmt->execute()) {
        // Set response code - 201 Created
        http_response_code(201);
        echo json_encode(array("status" => true, "message" => "Pengajuan booking berhasil dikirim."));
    } else {
        // Set response code - 503 Service unavailable
        http_response_code(503);
        echo json_encode(array("status" => false, "message" => "Gagal menyimpan pengajuan booking."));
    }
} else {
    // Beri tahu pengguna data tidak lengkap
    // Set response code - 400 Bad request
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "Data tidak lengkap. Gagal membuat pengajuan."));
}
