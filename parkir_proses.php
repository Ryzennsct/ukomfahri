<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'] ?? 0;

$aksi = $_POST['aksi'] ?? '';

/* ===============================
   AMBIL DATA KENDARAAN
================================ */
if ($aksi == 'get_kendaraan') {

    $q = mysqli_query($conn,"SELECT * FROM tb_kendaraan");

    $data = [];

    while($row = mysqli_fetch_assoc($q)){
        $data[] = $row;
    }

    echo json_encode($data);
}


/* ===============================
   AMBIL DATA AREA
================================ */
if ($aksi == 'get_area') {

    $q = mysqli_query($conn,"SELECT * FROM tb_area_parkir");

    $data = [];

    while($row = mysqli_fetch_assoc($q)){
        $data[] = $row;
    }

    echo json_encode($data);
}


/* ===============================
   AMBIL TARIF
================================ */
if ($aksi == 'get_tarif') {

    $jenis = $_POST['jenis'];

    $q = mysqli_query($conn,"
        SELECT * FROM tb_tarif 
        WHERE jenis_kendaraan='$jenis'
    ");

    $data = mysqli_fetch_assoc($q);

    echo json_encode($data);
}


/* ===============================
   PARKIR MASUK
================================ */
if ($aksi == 'masuk') {

    $id_kendaraan = $_POST['id_kendaraan'];
    $id_area      = $_POST['id_area'];
    $waktu_masuk  = $_POST['waktu_masuk'];

    // Cek apakah kendaraan masih parkir
    $cek = mysqli_query($conn,"
        SELECT * FROM tb_transaksi
        WHERE id_kendaraan='$id_kendaraan'
        AND status='masuk'
    ");

    if(mysqli_num_rows($cek) > 0){
        echo "sudah_parkir";
        exit;
    }

    mysqli_query($conn,"
        INSERT INTO tb_transaksi 
        (id_kendaraan,waktu_masuk,status,id_user,id_area)
        VALUES
        ('$id_kendaraan','$waktu_masuk','masuk','$id_user','$id_area')
    ");

    // Tambah terisi area
    mysqli_query($conn,"
        UPDATE tb_area_parkir
        SET terisi = terisi + 1
        WHERE id_area='$id_area'
    ");

    echo "masuk_ok";
}



/* ===============================
   PARKIR KELUAR
================================ */
if ($aksi == 'keluar') {

    $id_transaksi = $_POST['id_transaksi'];
    $waktu_keluar = $_POST['waktu_keluar'];

    // Ambil data transaksi
    $q = mysqli_query($conn,"
        SELECT t.*,k.jenis_kendaraan,a.id_area
        FROM tb_transaksi t
        JOIN tb_kendaraan k ON t.id_kendaraan=k.id_kendaraan
        JOIN tb_area_parkir a ON t.id_area=a.id_area
        WHERE t.id_parkir='$id_transaksi'
    ");

    $data = mysqli_fetch_assoc($q);

    $masuk = strtotime($data['waktu_masuk']);
    $keluar = strtotime($waktu_keluar);

    $durasi = ceil(($keluar - $masuk) / 3600);

    // Ambil tarif
    $qt = mysqli_query($conn,"
        SELECT * FROM tb_tarif
        WHERE jenis_kendaraan='".$data['jenis_kendaraan']."'
    ");

    $tarif = mysqli_fetch_assoc($qt);

    $total = $durasi * $tarif['tarif_per_jam'];

    // Update transaksi
    mysqli_query($conn,"
        UPDATE tb_transaksi SET
        waktu_keluar='$waktu_keluar',
        durasi_jam='$durasi',
        biaya_total='$total',
        status='keluar',
        id_tarif='".$tarif['id_tarif']."'
        WHERE id_parkir='$id_transaksi'
    ");

    // Kurangi area
    mysqli_query($conn,"
        UPDATE tb_area_parkir
        SET terisi = terisi - 1
        WHERE id_area='".$data['id_area']."'
    ");

    echo json_encode([
        'durasi'=>$durasi,
        'total'=>$total,
        'tarif'=>$tarif['tarif_per_jam']
    ]);
}



/* ===============================
   DATA PARKIR AKTIF
================================ */
if ($aksi == 'get_aktif') {

    $q = mysqli_query($conn,"
        SELECT t.*,k.*,a.nama_area
        FROM tb_transaksi t
        JOIN tb_kendaraan k ON t.id_kendaraan=k.id_kendaraan
        JOIN tb_area_parkir a ON t.id_area=a.id_area
        WHERE t.status='masuk'
        ORDER BY t.waktu_masuk DESC
    ");

    $data=[];

    while($row=mysqli_fetch_assoc($q)){
        $data[]=$row;
    }

    echo json_encode($data);
}
