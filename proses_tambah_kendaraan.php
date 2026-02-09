<?php
// proses_tambah_kendaraan.php
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

// Pastikan request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ID user dari session
    $id_user = (int) $_SESSION['id_user'];

    // Ambil & sanitasi input
    $plat_nomor      = mysqli_real_escape_string($conn, strtoupper(trim($_POST['plat_nomor'])));
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $warna           = mysqli_real_escape_string($conn, trim($_POST['warna']));
    $pemilik         = mysqli_real_escape_string($conn, trim($_POST['pemilik']));

    // Validasi
    if (empty($plat_nomor) || empty($jenis_kendaraan)) {
        echo "<script>
                alert('Plat nomor dan jenis kendaraan harus diisi!');
                window.location.href='index.php#kendaraan';
              </script>";
        exit;
    }

    // Cek duplikasi plat nomor
    $cek = mysqli_query($conn, "SELECT 1 FROM tb_kendaraan WHERE plat_nomor = '$plat_nomor'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
                alert('Plat nomor sudah terdaftar!');
                window.location.href='index.php#kendaraan';
              </script>";
        exit;
    }

    // INSERT + LOG AKTIVITAS
    query_log("
        INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, id_user)
        VALUES ('$plat_nomor', '$jenis_kendaraan', '$warna', '$pemilik', $id_user)
    ", "Menambahkan kendaraan dengan plat nomor $plat_nomor");

    echo "<script>
            alert('Data kendaraan berhasil ditambahkan!');
            window.location.href='index.php#kendaraan';
          </script>";
    exit;

} else {
    header('Location: index.php#kendaraan');
    exit;
}
?>
