<?php
// Koneksi database
require_once 'koneksi.php';

// Set header JSON
header('Content-Type: application/json');

// Cek dulu struktur tabel yang sebenarnya
$queryColumns = "SHOW COLUMNS FROM tb_kendaraan";
$resultColumns = mysqli_query($conn, $queryColumns);

$columns = [];
while ($row = mysqli_fetch_assoc($resultColumns)) {
    $columns[] = $row['Field'];
}

// Query berdasarkan kolom yang ada
$query = "SELECT * FROM tb_kendaraan ORDER BY plat_nomor ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode([
        'error' => true,
        'message' => mysqli_error($conn),
        'available_columns' => $columns
    ]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Map ke format yang dibutuhkan frontend dengan fallback
    $item = [
        'id' => $row['id_kendaraan'] ?? $row['id'] ?? '',
        'plat_nomor' => $row['plat_nomor'] ?? '',
        'jenis' => $row['jenis_kendaraan'] ?? $row['jenis'] ?? $row['type'] ?? '',
        'warna' => $row['warna'] ?? $row['color'] ?? '-',
        'pemilik' => $row['pemilik'] ?? $row['nama_pemilik'] ?? $row['owner'] ?? ''
    ];
    $data[] = $item;
}

echo json_encode($data);

mysqli_close($conn);
?>