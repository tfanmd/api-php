<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

// Menghapus 'status' dari query
$query = "SELECT id, nama_lab, deskripsi_fasilitas, kapasitas, software, mata_kuliah_terkait FROM labs ORDER BY nama_lab ASC";

$stmt = $db->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $labs_arr = array();
    $labs_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $lab_item = array(
            "id" => $id,
            "nama_lab" => $nama_lab,
            "spesifikasi_ruangan" => $kapasitas . " PC, " . $deskripsi_fasilitas,
            "software" => $software,
            "mata_kuliah" => $mata_kuliah_terkait
            // Key 'status' dihapus dari sini
        );
        array_push($labs_arr["records"], $lab_item);
    }
    http_response_code(200);
    echo json_encode($labs_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Tidak ada data lab ditemukan."));
}
