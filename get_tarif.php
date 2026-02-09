<?php
// Koneksi database
require_once 'koneksi.php';

// Set header JSON
header('Content-Type: application/json');

try {
    // Query untuk mengambil semua data tarif
    $query = "SELECT * FROM tb_tarif ORDER BY id_tarif ASC";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Map dengan fallback untuk berbagai nama kolom
        $item = [
            'id' => $row['id_tarif'] ?? $row['id'] ?? '',
            'jenis' => $row['jenis_kendaraan'] ?? $row['jenis'] ?? $row['type'] ?? '',
            'tarif' => $row['tarif_per_jam'] ?? $row['tarif'] ?? $row['harga'] ?? 0
        ];
        $data[] = $item;
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