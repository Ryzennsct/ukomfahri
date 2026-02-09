<?php
// Koneksi database
require_once 'koneksi.php';

// Set header JSON
header('Content-Type: application/json');

try {
    // Query untuk mengambil semua data area parkir
    $query = "SELECT * FROM tb_area_parkir ORDER BY nama_area ASC";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $item = [
            'id' => $row['id_area'] ?? $row['id'] ?? '',
            'nama_area' => $row['nama_area'] ?? $row['area'] ?? ''
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