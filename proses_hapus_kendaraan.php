<?php
// kendaraan_hapus.php
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

// Cek parameter ID
if (isset($_GET['id']) && !empty($_GET['id'])) {

    $id_kendaraan = (int) $_GET['id'];

    // Validasi ID
    if ($id_kendaraan <= 0) {
        echo "<script>
                alert('ID kendaraan tidak valid!');
                window.location.href='index.php#kendaraan';
              </script>";
        exit;
    }

    // Cek apakah data ada
    $cek = mysqli_query($conn, "
        SELECT plat_nomor 
        FROM tb_kendaraan 
        WHERE id_kendaraan = $id_kendaraan
    ");

    if (mysqli_num_rows($cek) == 0) {
        echo "<script>
                alert('Data kendaraan tidak ditemukan!');
                window.location.href='index.php#kendaraan';
              </script>";
        exit;
    }

    // Ambil plat nomor untuk log
    $data = mysqli_fetch_assoc($cek);
    $plat_nomor = $data['plat_nomor'];

    // DELETE + LOG AKTIVITAS
    query_log("
        DELETE FROM tb_kendaraan 
        WHERE id_kendaraan = $id_kendaraan
    ", "Menghapus data kendaraan ID $id_kendaraan (Plat: $plat_nomor)");

    echo "<script>
            alert('Data kendaraan berhasil dihapus!');
            window.location.href='index.php#kendaraan';
          </script>";
    exit;

} else {
    echo "<script>
            alert('ID kendaraan tidak ditemukan!');
            window.location.href='index.php#kendaraan';
          </script>";
    exit;
}
?>
