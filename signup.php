<?php
header('Content-Type: application/json'); // agar response JSON
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = $_POST['txtNama'] ?? '';
    $email    = $_POST['txtEmail'] ?? '';
    $password = $_POST['txtPassword'] ?? '';
    $no_hp    = $_POST['txtNoHp'] ?? '';
    $alamat   = $_POST['txtAlamat'] ?? '';

    // validasi sederhana
    if(empty($nama) || empty($email) || empty($password) || empty($no_hp) || empty($alamat)){
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
        exit;
    }

    // cek email sudah ada atau belum
    $cek = $mysqli->prepare("SELECT id_akun FROM akun WHERE email=?");
    $cek->bind_param("s", $email);
    $cek->execute();
    $cek->store_result();
    if($cek->num_rows > 0){
        echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar']);
        exit;
    }

    // hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // insert ke database
    $stmt = $mysqli->prepare("INSERT INTO akun (nama, email, PASSWORD, no_hp, alamat) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $email, $passwordHash, $no_hp, $alamat);

    if($stmt->execute()){
        echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal registrasi']);
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Request tidak valid']);
}
?>
