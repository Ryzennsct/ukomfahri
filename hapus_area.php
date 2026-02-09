<?php
session_start();
require_once 'koneksi.php';
require_once 'log_helper.php';

if (isset($_GET['id'])) {
    $id_area = (int) $_GET['id'];

    // Ambil nama area sebelum dihapus (untuk notifikasi & log)
    $get_nama = mysqli_query($conn, "SELECT nama_area FROM tb_area_parkir WHERE id_area = $id_area");
    $data = mysqli_fetch_assoc($get_nama);
    $nama_area = $data['nama_area'] ?? 'Unknown';

    // Validasi: cek apakah data ada
    if (!$data) {
        $_SESSION['message'] = 'Data area tidak ditemukan!';
        $_SESSION['message_type'] = 'warning';
        header('Location: index.php#area');
        exit;
    }

    // Hapus data + log aktivitas
    query_log("
        DELETE FROM tb_area_parkir 
        WHERE id_area = $id_area
    ", "Menghapus area parkir dengan nama $nama_area (ID: $id_area)");

    $_SESSION['message'] = "Area parkir '$nama_area' berhasil dihapus!";
    $_SESSION['message_type'] = 'success';

    header('Location: index.php#area');
    exit;

} else {
    header('Location: index.php#area');
    exit;
}
?>
