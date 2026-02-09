<?php
session_start();
include 'koneksi.php';
require_once 'log_helper.php';

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = $_GET['id'];

// Hapus user + catat log aktivitas
query_log("
    DELETE FROM tb_user 
    WHERE id_user = '$id'
", "Menghapus data user dengan ID $id");

header("Location: index.php?hapus=success");
exit;
?>
