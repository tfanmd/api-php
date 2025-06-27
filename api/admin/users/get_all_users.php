<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

// --- PERUBAHAN DI SINI ---
// Tambahkan fakultas dan nomor_telepon ke SELECT
$query = "SELECT id, username, nama_lengkap, role, fakultas, nomor_telepon FROM users ORDER BY role, nama_lengkap ASC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $users_arr = array("records" => array());
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $user_item = array(
            "id" => $id,
            "username" => $username,
            "nama_lengkap" => $nama_lengkap,
            "role" => $role,
            "fakultas" => $fakultas, // Tambahkan
            "nomor_telepon" => $nomor_telepon // Tambahkan
        );
        // --- AKHIR PERUBAHAN ---
        array_push($users_arr["records"], $user_item);
    }
    http_response_code(200);
    echo json_encode($users_arr);
} else {
    // ... (respons error tetap sama)
}
