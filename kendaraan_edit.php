<?php
// kendaraan_edit.php
session_start();

include 'koneksi.php';
require_once 'log_helper.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    echo "<script>
            alert('Anda harus login terlebih dahulu!');
            window.location.href='login.php';
          </script>";
    exit;
}

// Pastikan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil & sanitasi data
    $id_kendaraan    = (int) $_POST['id_kendaraan'];
    $plat_nomor      = mysqli_real_escape_string($conn, strtoupper(trim($_POST['plat_nomor'])));
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $warna           = mysqli_real_escape_string($conn, trim($_POST['warna']));
    $pemilik         = mysqli_real_escape_string($conn, trim($_POST['pemilik']));

    // Validasi
    if (empty($id_kendaraan) || empty($plat_nomor) || empty($jenis_kendaraan)) {
        echo "<script>
                alert('Data tidak lengkap!');
                window.location.href='index.php#kendaraan';
              </script>";
        exit;
    }

    // Cek duplikasi plat nomor (kecuali data sendiri)
    $cek = mysqli_query($conn, "
        SELECT 1 
        FROM tb_kendaraan 
        WHERE plat_nomor = '$plat_nomor'
        AND id_kendaraan != $id_kendaraan
    ");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
                alert('Plat nomor sudah digunakan kendaraan lain!');
                window.location.href='index.php#kendaraan';
              </script>";
        exit;
    }

    // UPDATE + LOG AKTIVITAS
    query_log("
        UPDATE tb_kendaraan SET
            plat_nomor = '$plat_nomor',
            jenis_kendaraan = '$jenis_kendaraan',
            warna = '$warna',
            pemilik = '$pemilik'
        WHERE id_kendaraan = $id_kendaraan
    ", "Mengubah data kendaraan ID $id_kendaraan (Plat: $plat_nomor)");

    echo "<script>
            alert('Data kendaraan berhasil diupdate!');
            window.location.href='index.php#kendaraan';
          </script>";
    exit;

} else {
    header('Location: index.php#kendaraan');
    exit;
}
?>
