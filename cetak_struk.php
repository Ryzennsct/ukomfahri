<?php
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$id = $_GET['id'];

$d = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT t.*,k.plat_nomor,k.jenis_kendaraan,a.nama_area
FROM tb_transaksi t
JOIN tb_kendaraan k
ON t.id_kendaraan=k.id_kendaraan
JOIN tb_area_parkir a
ON t.id_area=a.id_area
WHERE t.id_parkir='$id'
"));
?>

<!DOCTYPE html>
<html>
<head>
<title>Struk Parkir</title>

<style>
body{
font-family: Arial;
width:280px;
margin:auto;
}
</style>

</head>

<body onload="setTimeout(()=>window.print(),800)">

<div id="qrcode" style="display:flex; justify-content:center; margin:10px 0;"></div>


<h4 align="center">STRUK PARKIR</h4>
<hr>

Plat : <?=$d['plat_nomor']?><br>
Jenis : <?=$d['jenis_kendaraan']?><br>
Area : <?=$d['nama_area']?><br>

<hr>

Masuk : <?=$d['waktu_masuk']?><br>
Keluar : <?=$d['waktu_keluar']?><br>
Jam : <?=$d['durasi_jam']?><br>

<hr>

Total : Rp <?=number_format($d['biaya_total'])?>

<hr>

<center>Terima Kasih</center>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

<script>
new QRCode(document.getElementById("qrcode"), {
    text: "<?= $d['plat_nomor'] ?>",
    width: 100,
    height: 100
});
</script>