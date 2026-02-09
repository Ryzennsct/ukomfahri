<?php
session_start();
require_once 'koneksi.php';
require_once 'log_helper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_area   = (int) $_POST['id_area'];
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
        $_SESSION['message'] = 'Jumlah terisi tidak boleh lebih dari kapasitas!';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php#area');
        exit;
    }

    // Cek duplikasi nama (kecuali data sendiri)
    $cek = mysqli_query($conn, "
        SELECT * FROM tb_area_parkir 
        WHERE nama_area = '$nama_area' 
          AND id_area != $id_area
    ");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['message'] = 'Nama area sudah digunakan oleh area lain!';
        $_SESSION['message_type'] = 'warning';
        header('Location: index.php#area');
        exit;
    }

    // Update + log aktivitas
    query_log("
        UPDATE tb_area_parkir SET 
            nama_area = '$nama_area',
            kapasitas = $kapasitas,
            terisi    = $terisi
        WHERE id_area = $id_area
    ", "Mengubah data area parkir ID $id_area (Nama: $nama_area, Kapasitas: $kapasitas, Terisi: $terisi)");

    $_SESSION['message'] = "Area parkir '$nama_area' berhasil diupdate!";
    $_SESSION['message_type'] = 'success';

    header('Location: index.php#area');
    exit;
} else {
    header('Location: index.php#area');
    exit;
}
?>
