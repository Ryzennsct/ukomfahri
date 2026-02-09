<?php
// Koneksi database
require_once 'koneksi.php';

// Set header JSON
header('Content-Type: application/json');

try {
    // Query untuk mengambil transaksi yang sedang parkir (status Masuk)
    $query = "SELECT 
        t.id_parkir as id,
        t.waktu_masuk,
        t.waktu_keluar,
        t.durasi_jam,
        t.status,
        k.plat_nomor,
        COALESCE(k.jenis_kendaraan, k.jenis, k.type) as jenis,
        COALESCE(k.pemilik, k.nama_pemilik) as pemilik,
        COALESCE(a.nama_area, '-') as area
    FROM tb_transaksi t
    LEFT JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    LEFT JOIN tb_area_parkir a ON t.id_area = a.id_area
    WHERE t.status = 'Masuk'
    ORDER BY t.waktu_masuk DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    echo json_encode($data);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>