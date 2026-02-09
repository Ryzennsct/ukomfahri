<?php
session_start();
include 'koneksi.php';
require_once 'log_helper.php';

$jenis = $_POST['jenis_kendaraan'];
$tarif = $_POST['tarif_per_jam'];

/* 1. Cek apakah jenis kendaraan sudah ada */
$cek = mysqli_query($conn, "
    SELECT * FROM tb_tarif
    WHERE jenis_kendaraan = '$jenis'
");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>
        alert('Jenis kendaraan sudah memiliki tarif!');
        window.location='index.php#tarif';
    </script>";
    exit;
}

/* 2. Jika belum ada → insert + log */
query_log("
    INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam)
    VALUES ('$jenis', '$tarif')
", "Menambahkan tarif parkir untuk jenis kendaraan $jenis dengan tarif Rp $tarif / jam");

header("Location: index.php#tarif");
exit;
?>
