<?php
session_start();
require_once 'koneksi.php';
require_once 'log_helper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_area = mysqli_real_escape_string($conn, trim($_POST['nama_area']));
    $kapasitas = (int) $_POST['kapasitas'];
    $terisi    = (int) $_POST['terisi'];

    // Validasi
    if (empty($nama_area)) {
        $_SESSION['message'] = 'Nama area tidak boleh kosong!';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php#area');
        exit;
    }

    if ($kapasitas < 1) {
        $_SESSION['message'] = 'Kapasitas harus minimal 1!';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php#area');
        exit;
    }

    if ($terisi < 0 || $terisi > $kapasitas) {
        $_SESSION['message'] = 'Jumlah terisi tidak valid!';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php#area');
        exit;
    }

    // Cek duplikasi nama area
    $cek = mysqli_query($conn, "
        SELECT * FROM tb_area_parkir 
        WHERE nama_area = '$nama_area'
    ");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['message'] = 'Nama area sudah ada! Gunakan nama lain.';
        $_SESSION['message_type'] = 'warning';
        header('Location: index.php#area');
        exit;
    }

    // Insert + log aktivitas
    query_log("
        INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi) 
        VALUES ('$nama_area', $kapasitas, $terisi)
    ", "Menambahkan area parkir baru dengan nama $nama_area dan kapasitas $kapasitas");

    $_SESSION['message'] = "Area parkir '$nama_area' berhasil ditambahkan!";
    $_SESSION['message_type'] = 'success';

    header('Location: index.php#area');
    exit;
} else {
    header('Location: index.php#area');
    exit;
}
?>
