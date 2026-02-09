<?php
session_start();
require_once 'koneksi.php';

// Rekap transaksi berdasarkan filter waktu
if (isset($_GET['action']) && $_GET['action'] == 'get') {
    
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
    $tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : null;
    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : null;
    
    // Base query
    $query = "SELECT 
                t.id_parkir,
                t.waktu_masuk,
                t.waktu_keluar,
                t.durasi_jam,
                t.biaya_total,
                t.status,
                k.plat_nomor,
                k.jenis,
                k.warna,
                k.pemilik,
                a.nama_area,
                a.tarif_perjam,
                u.nama as petugas
              FROM tb_transaksi t
              JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
              LEFT JOIN tb_area a ON t.id_area = a.id_area
              LEFT JOIN tb_user u ON t.id_user = u.id_user";
    
    $where_conditions = [];
    $params = [];
    $types = "";
    
    // Filter berdasarkan pilihan
    switch ($filter) {
        case 'hari_ini':
            $where_conditions[] = "DATE(t.waktu_masuk) = CURDATE()";
            break;
            
        case 'kemarin':
            $where_conditions[] = "DATE(t.waktu_masuk) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
            
        case 'minggu_ini':
            $where_conditions[] = "YEARWEEK(t.waktu_masuk, 1) = YEARWEEK(CURDATE(), 1)";
            break;
            
        case 'bulan_ini':
            $where_conditions[] = "MONTH(t.waktu_masuk) = MONTH(CURDATE()) AND YEAR(t.waktu_masuk) = YEAR(CURDATE())";
            break;
            
        case 'custom':
            if ($tanggal_mulai && $tanggal_akhir) {
                $where_conditions[] = "DATE(t.waktu_masuk) BETWEEN ? AND ?";
                $params[] = $tanggal_mulai;
                $params[] = $tanggal_akhir;
                $types .= "ss";
            }
            break;
    }
    
    // Tambahkan WHERE clause jika ada
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    $query .= " ORDER BY t.waktu_masuk DESC LIMIT 1000";
    
    // Prepare dan execute
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }
    
    $data = [];
    $total_pendapatan = 0;
    $total_transaksi = 0;
    $total_kendaraan_parkir = 0;
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $total_transaksi++;
        
        if ($row['status'] == 'keluar' && $row['biaya_total']) {
            $total_pendapatan += $row['biaya_total'];
        }
        
        if ($row['status'] == 'masuk') {
            $total_kendaraan_parkir++;
        }
    }
    
    // Hitung statistik tambahan
    $statistik = [
        'total_transaksi' => $total_transaksi,
        'total_pendapatan' => $total_pendapatan,
        'total_kendaraan_parkir' => $total_kendaraan_parkir,
        'filter' => $filter,
        'tanggal_mulai' => $tanggal_mulai,
        'tanggal_akhir' => $tanggal_akhir
    ];
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'statistik' => $statistik
    ]);
    
    if (isset($stmt)) $stmt->close();
    $conn->close();
    exit();
}

// Default response
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request']);
$conn->close();
?>