<?php
session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$id_user = $_SESSION['id_user'];


/* ================= MASUK ================= */

if(isset($_POST['masuk'])){

    $plat  = strtoupper(trim($_POST['plat']));
    $jenis = $_POST['jenis'];   // dari form (manual)
    $area  = $_POST['area'];


    /* CEK / SIMPAN KENDARAAN */
    $q = mysqli_query($conn,"
        SELECT * FROM tb_kendaraan
        WHERE plat_nomor='$plat'
    ");

    if(mysqli_num_rows($q)==0){

        // kalau belum ada → insert
        mysqli_query($conn,"
            INSERT INTO tb_kendaraan
            (plat_nomor, jenis_kendaraan, warna, pemilik, id_user)
            VALUES
            ('$plat','$jenis','-','-','$id_user')
        ");

        $id_kendaraan = mysqli_insert_id($conn);

    }else{

        // kalau sudah ada → pakai
        $k = mysqli_fetch_assoc($q);
        $id_kendaraan = $k['id_kendaraan'];

    }


    /* AMBIL TARIF */
    $t = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT * FROM tb_tarif
        WHERE jenis_kendaraan='$jenis'
    "));

    $id_tarif = $t['id_tarif'];


    $now = date('Y-m-d H:i:s');

    $cek = mysqli_query($conn,"
    SELECT * FROM tb_transaksi t
    JOIN tb_kendaraan k
    ON t.id_kendaraan = k.id_kendaraan
    WHERE k.plat_nomor='$plat'
    AND t.status='masuk'
    ");
    
    if(mysqli_num_rows($cek)>0){
    
        echo "<script>
        alert('Kendaraan masih parkir!');
        location.href='index.php#sistem-parkir';
        </script>";
    
        exit;
    }
    
    /* INSERT TRANSAKSI */
    mysqli_query($conn,"
        INSERT INTO tb_transaksi
        VALUES(
        NULL,
        '$id_kendaraan',
        '$now',
        NULL,
        '$id_tarif',
        0,
        0,
        'masuk',
        '$id_user',
        '$area'
        )
    ");


    /* UPDATE AREA */
    mysqli_query($conn,"
        UPDATE tb_area_parkir
        SET terisi = terisi + 1
        WHERE id_area = '$area'
    ");


    header("location:index.php#sistem-parkir");
    exit;
}

/* ================= KELUAR ================= */

if(isset($_POST['keluar'])){

    $id = $_POST['id_transaksi'];

    // Ambil data transaksi
    $d = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT * FROM tb_transaksi
        WHERE id_parkir='$id'
    "));


    // Hitung durasi
    $masuk  = strtotime($d['waktu_masuk']);
    $keluar = time();

    $jam = ceil(($keluar - $masuk) / 3600);
    if($jam < 1) $jam = 1;


    // Ambil tarif
    $t = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT tarif_per_jam
        FROM tb_tarif
        WHERE id_tarif='$d[id_tarif]'
    "));


    $total = $jam * $t['tarif_per_jam'];

    $now = date('Y-m-d H:i:s');


    // Update transaksi
    mysqli_query($conn,"
        UPDATE tb_transaksi SET

        waktu_keluar = '$now',
        durasi_jam   = '$jam',
        biaya_total = '$total',
        status       = 'keluar'

        WHERE id_parkir = '$id'
    ");


    // Update area
    mysqli_query($conn,"
        UPDATE tb_area_parkir
        SET terisi = terisi - 1
        WHERE id_area = '$d[id_area]'
    ");


    // Redirect cetak
    header("location:cetak_struk.php?id=$id");
    exit;
}
