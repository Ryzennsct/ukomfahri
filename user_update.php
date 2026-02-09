<?php
session_start();
include 'koneksi.php';
require_once 'log_helper.php';

$id     = $_POST['id_user'];
$nama   = $_POST['nama_lengkap'];
$user   = $_POST['username'];
$pass   = $_POST['password'];
$role   = $_POST['role'];
$status = $_POST['status_aktif'];

// Jika password diisi → update + hash
if (!empty($pass)) {
    $password = password_hash($pass, PASSWORD_DEFAULT);

    query_log("
        UPDATE tb_user SET
            nama_lengkap = '$nama',
            username     = '$user',
            password     = '$password',
            role         = '$role',
            status_aktif = '$status'
        WHERE id_user = '$id'
    ", "Mengubah data user ID $id (Username: $user, Role: $role)");
} else {
    // Jika password tidak diubah
    query_log("
        UPDATE tb_user SET
            nama_lengkap = '$nama',
            username     = '$user',
            role         = '$role',
            status_aktif = '$status'
        WHERE id_user = '$id'
    ", "Mengubah data user ID $id (Username: $user, Role: $role)");
}

header("Location: index.php?edit=success");
exit;
?>
