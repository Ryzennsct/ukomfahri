<?php
// Koneksi database
require_once 'koneksi.php';

// Set header JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $kendaraan_id = $_POST['kendaraan_id'];
        $status = $_POST['status'];
        $waktu_masuk = $_POST['waktu_masuk'];
        $area_id = $_POST['area_parkir_id'];
        
        // Ambil id_user dari session jika ada
        session_start();
        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : NULL;
        
        if ($status == 'Masuk') {
            // Cek apakah kendaraan sudah parkir
            $check = "SELECT id_parkir FROM tb_transaksi WHERE id_kendaraan = ? AND status = 'Masuk'";
            $stmt_check = mysqli_prepare($conn, $check);
            mysqli_stmt_bind_param($stmt_check, "i", $kendaraan_id);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            
            if (mysqli_num_rows($result_check) > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Kendaraan ini sudah parkir! Harap keluarkan dulu.'
                ]);
                exit;
            }
            
            // Ambil jenis kendaraan
            $get_jenis = "SELECT * FROM tb_kendaraan WHERE id_kendaraan = ?";
            $stmt_jenis = mysqli_prepare($conn, $get_jenis);
            mysqli_stmt_bind_param($stmt_jenis, "i", $kendaraan_id);
            mysqli_stmt_execute($stmt_jenis);
            $result_jenis = mysqli_stmt_get_result($stmt_jenis);
            $data_jenis = mysqli_fetch_assoc($result_jenis);
            
            // Ambil jenis dengan fallback (cek semua kemungkinan nama kolom)
            $jenis = '';
            if (isset($data_jenis['jenis_kendaraan'])) {
                $jenis = $data_jenis['jenis_kendaraan'];
            } elseif (isset($data_jenis['jenis'])) {
                $jenis = $data_jenis['jenis'];
            } elseif (isset($data_jenis['type'])) {
                $jenis = $data_jenis['type'];
            }
            
            // Cek kolom apa yang ada di tb_tarif
            $check_columns = "SHOW COLUMNS FROM tb_tarif";
            $result_columns = mysqli_query($conn, $check_columns);
            $tarif_columns = [];
            while ($col = mysqli_fetch_assoc($result_columns)) {
                $tarif_columns[] = $col['Field'];
            }
            
            // Build query berdasarkan kolom yang ada
            $id_tarif = NULL;
            if (in_array('jenis_kendaraan', $tarif_columns)) {
                $get_tarif = "SELECT * FROM tb_tarif WHERE jenis_kendaraan = ?";
            } elseif (in_array('jenis', $tarif_columns)) {
                $get_tarif = "SELECT * FROM tb_tarif WHERE jenis = ?";
            } elseif (in_array('type', $tarif_columns)) {
                $get_tarif = "SELECT * FROM tb_tarif WHERE type = ?";
            } else {
                // Fallback: ambil tarif pertama
                $get_tarif = "SELECT * FROM tb_tarif LIMIT 1";
            }
            
            $stmt_tarif = mysqli_prepare($conn, $get_tarif);
            if (strpos($get_tarif, '?') !== false) {
                mysqli_stmt_bind_param($stmt_tarif, "s", $jenis);
            }
            mysqli_stmt_execute($stmt_tarif);
            $result_tarif = mysqli_stmt_get_result($stmt_tarif);
            
            if (mysqli_num_rows($result_tarif) > 0) {
                $data_tarif = mysqli_fetch_assoc($result_tarif);
                $id_tarif = $data_tarif['id_tarif'] ?? $data_tarif['id'] ?? NULL;
            }
            
            // Insert transaksi masuk
            $query = "INSERT INTO tb_transaksi (id_kendaraan, id_area, waktu_masuk, status, id_tarif, id_user) 
                      VALUES (?, ?, ?, 'Masuk', ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "iisii", $kendaraan_id, $area_id, $waktu_masuk, $id_tarif, $id_user);
            
            if (mysqli_stmt_execute($stmt)) {
                // Update terisi di area parkir
                $update_area = "UPDATE tb_area_parkir SET terisi = terisi + 1 WHERE id_area = ?";
                $stmt_area = mysqli_prepare($conn, $update_area);
                mysqli_stmt_bind_param($stmt_area, "i", $area_id);
                mysqli_stmt_execute($stmt_area);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Kendaraan berhasil masuk parkir!'
                ]);
            } else {
                throw new Exception(mysqli_error($conn));
            }
            
        } else if ($status == 'Keluar') {
            // Update transaksi keluar
            $waktu_keluar = $_POST['waktu_keluar'];
            $durasi = $_POST['durasi'];
            $total_biaya = str_replace(['Rp', '.', ' '], '', $_POST['total_biaya']);
            
            // Ambil data transaksi masuk
            $get_transaksi = "SELECT id_parkir, id_tarif, id_area FROM tb_transaksi 
                             WHERE id_kendaraan = ? AND status = 'Masuk' 
                             ORDER BY waktu_masuk DESC LIMIT 1";
            $stmt_get = mysqli_prepare($conn, $get_transaksi);
            mysqli_stmt_bind_param($stmt_get, "i", $kendaraan_id);
            mysqli_stmt_execute($stmt_get);
            $result_get = mysqli_stmt_get_result($stmt_get);
            
            if (mysqli_num_rows($result_get) == 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tidak ada data kendaraan masuk untuk kendaraan ini'
                ]);
                exit;
            }
            
            $transaksi = mysqli_fetch_assoc($result_get);
            $transaksi_id = $transaksi['id_parkir'];
            $id_tarif = $transaksi['id_tarif'];
            $area_id = $transaksi['id_area'];
            
            // Update transaksi
            $query = "UPDATE tb_transaksi 
                      SET waktu_keluar = ?, 
                          durasi_jam = ?, 
                          biaya_total = ?, 
                          status = 'Keluar'
                      WHERE id_parkir = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "siii", $waktu_keluar, $durasi, $total_biaya, $transaksi_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Update terisi di area parkir (kurangi)
                $update_area = "UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area = ?";
                $stmt_area = mysqli_prepare($conn, $update_area);
                mysqli_stmt_bind_param($stmt_area, "i", $area_id);
                mysqli_stmt_execute($stmt_area);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Kendaraan berhasil keluar. Total biaya: Rp ' . number_format($total_biaya, 0, ',', '.'),
                    'transaksi_id' => $transaksi_id,
                    'total_biaya' => $total_biaya
                ]);
            } else {
                throw new Exception(mysqli_error($conn));
            }
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}

mysqli_close($conn);
?>