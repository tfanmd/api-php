<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/koneksi.php';

$database = new Database();
$db = $database->getConnection();

$response = array("status" => false, "message" => "");

// Pastikan ada user_id yang dikirim
if (!empty($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $fakultas = isset($_POST['fakultas']) ? $_POST['fakultas'] : '';
    $nomor_telepon = isset($_POST['nomor_telepon']) ? $_POST['nomor_telepon'] : '';
    $url_foto_profil = '';

    // Proses upload foto jika ada file yang dikirim
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../../uploads/"; // Folder untuk menyimpan foto (pastikan folder ini ada dan writable)
        $target_file = $target_dir . basename($_FILES["foto_profil"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi jenis file
        $allowed_types = array('jpg', 'jpeg', 'png');
        if (!in_array($imageFileType, $allowed_types)) {
            $response["message"] .= "Hanya file JPG, JPEG, dan PNG yang diizinkan. ";
            $uploadOk = 0;
        }

        // Batas ukuran file (contoh: 5MB)
        if ($_FILES["foto_profil"]["size"] > 5000000) {
            $response["message"] .= "Ukuran file terlalu besar (maksimal 5MB). ";
            $uploadOk = 0;
        }

        if ($uploadOk) {
            $new_file_name = uniqid() . "." . $imageFileType;
            $target_file = $target_dir . $new_file_name;
            if (move_uploaded_file($_FILES["foto_profil"]["tmp_name"], $target_file)) {
                $url_foto_profil = 'http://' . $_SERVER['SERVER_NAME'] . '/api-moprog/uploads/' . $new_file_name;
            } else {
                $response["message"] .= "Terjadi kesalahan saat mengupload foto. ";
            }
        }
    }

    // Update data user ke database
    $query = "UPDATE users SET fakultas = :fakultas, nomor_telepon = :nomor_telepon";
    if (!empty($url_foto_profil)) {
        $query .= ", url_foto_profil = :url_foto_profil";
    }
    $query .= " WHERE id = :user_id";

    $stmt = $db->prepare($query);

    $stmt->bindParam(':fakultas', $fakultas);
    $stmt->bindParam(':nomor_telepon', $nomor_telepon);
    $stmt->bindParam(':user_id', $user_id);
    if (!empty($url_foto_profil)) {
        $stmt->bindParam(':url_foto_profil', $url_foto_profil);
    }

    if ($stmt->execute()) {
        $response["status"] = true;
        $response["message"] .= "Profil berhasil diupdate.";
    } else {
        $response["message"] .= "Gagal mengupdate data profil ke database.";
    }
} else {
    $response["message"] = "User ID tidak ditemukan.";
}

http_response_code(200);
echo json_encode($response);
