<?php
session_start();
require_once 'koneksi.php';
require_once 'log_helper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $username     = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password     = mysqli_real_escape_string($conn, $_POST['password']);
    $role         = mysqli_real_escape_string($conn, $_POST['role']);
    $status_aktif = (int) $_POST['status_aktif'];

    // Validasi
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $_SESSION['message'] = 'Semua field harus diisi!';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php#user');
        exit;
    }

    // Cek duplikasi username
    $cek = mysqli_query($conn, "SELECT * FROM tb_user WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['message'] = "Username '$username' sudah digunakan!";
        $_SESSION['message_type'] = 'warning';
        header('Location: index.php#user');
        exit;
    }

    // Simpan password tanpa hash (plain text)
    $password_plain = $password;

    // Insert user + log aktivitas
    query_log("
        INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif) 
        VALUES ('$nama_lengkap', '$username', '$password_plain', '$role', $status_aktif)
    ", "Menambahkan user baru dengan username $username dan role $role");

    $_SESSION['message'] = "User '$nama_lengkap' berhasil ditambahkan!";
    $_SESSION['message_type'] = 'success';

    header('Location: index.php#user');
    exit;
}
?>