<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once 'koneksi.php';

// ================= LOAD DOMPDF MANUAL =================
require_once __DIR__ . '/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

// ================= FILTER =================
$tanggal_mulai  = $_GET['tanggal_mulai'] ?? '';
$tanggal_akhir  = $_GET['tanggal_akhir'] ?? '';
$status         = $_GET['status'] ?? '';

$where = "WHERE 1=1";

if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
    $where .= " AND DATE(waktu_masuk) BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
}

if (!empty($status)) {
    $where .= " AND tb_transaksi.status = '$status'";
}

// ================= QUERY =================
$query = mysqli_query($conn, "
    SELECT tb_transaksi.*, tb_kendaraan.plat_nomor
    FROM tb_transaksi
    LEFT JOIN tb_kendaraan 
    ON tb_transaksi.id_kendaraan = tb_kendaraan.id_kendaraan
    $where
    ORDER BY waktu_masuk DESC
");

// ================= DETEKSI USER =================
$role = strtoupper($_SESSION['role'] ?? 'UNKNOWN');
$username = $_SESSION['username'] ?? 'UNKNOWN';

// ================= HTML =================
function tanggalIndonesia($datetime) {
    $bulan = [
        1 => 'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    $timestamp = strtotime($datetime);
    $tgl = date('d', $timestamp);
    $bln = $bulan[(int)date('m', $timestamp)];
    $thn = date('Y', $timestamp);
    $jam = date('H:i:s', $timestamp);

    return "$tgl $bln $thn | $jam WIB";
}

$html = '
<style>
    body {
        font-family: Arial, sans-serif;
        color: #333;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .logo {
        font-size: 26px;
        font-weight: bold;
        color: #00bcd4;
        letter-spacing: 1px;
    }

    .sub {
        font-size: 14px;
        color: #555;
    }

    .info {
        margin-top: 10px;
        font-size: 13px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th {
        background-color: #00bcd4;
        color: white;
        padding: 8px;
        font-size: 12px;
        border: 1px solid #0097a7;
    }

    td {
        padding: 7px;
        font-size: 11px;
        text-align: center;
        border: 1px solid #ddd;
    }

    tr:nth-child(even) {
        background-color: #f5fcfd;
    }

    .total-box {
        margin-top: 25px;
        padding: 12px;
        background-color: #e0f7fa;
        border: 2px solid #00bcd4;
        font-weight: bold;
        font-size: 14px;
        text-align: right;
        color: #006064;
    }

    .footer {
        margin-top: 60px;
        text-align: right;
        font-size: 13px;
        color: #444;
    }
</style>


<div class="header">
    <div class="logo">EASY PARK</div>
    <div class="sub">Rekap Transaksi Parkir</div>
</div>

<p class="info">
<b>Dicetak oleh:</b> '.$role.' ('.$username.')<br>
<b>Tanggal Cetak:</b> '.tanggalIndonesia(date("Y-m-d H:i:s")).'
</p>


<table>
<tr>
    <th>No</th>
    <th>ID</th>
    <th>Plat</th>
    <th>Masuk</th>
    <th>Keluar</th>
    <th>Durasi</th>
    <th>Biaya</th>
    <th>Status</th>
</tr>';


$no = 1;
$total = 0;

while ($data = mysqli_fetch_assoc($query)) {

    $biaya = $data['biaya_total'];
    $total += $biaya;

    $html .= '
    <tr>
        <td>'.$no++.'</td>
        <td>'.$data['id_parkir'].'</td>
        <td>'.$data['plat_nomor'].'</td>
            <td>'.$data['waktu_masuk'].'</td>
            <td>'.$data['waktu_keluar'].'</td>
            <td>'.$data['durasi_jam'].' Jam</td>
            <td>Rp '.number_format($biaya,0,",",".").'</td>
            <td>'.$data['status'].'</td>
        </tr>';
}

$html .= '
</table>

<div class="total-box">
Total Pendapatan: Rp '.number_format($total,0,",",".").'
</div>

<div class="footer">
Mengetahui,<br><br><br>
'.$role.'
</div>
';


// ================= GENERATE PDF =================
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("rekap_transaksi.pdf", ["Attachment" => true]);
