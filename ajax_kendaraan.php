<?php
include 'koneksi.php';

$plat = $_GET['plat'];

$q = mysqli_query($conn,"
SELECT * FROM tb_kendaraan
WHERE plat_nomor='$plat'
");

echo json_encode(mysqli_fetch_assoc($q));
