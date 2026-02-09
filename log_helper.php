<?php
// ==================================================
// HELPER GLOBAL (WAJIB DI PALING ATAS)
// ==================================================

if (!function_exists('log_aktivitas')) {
    function log_aktivitas($aktivitas)
    {
        global $conn;

        if (!isset($_SESSION['id_user'])) {
            return;
        }
        // Ambil id_user dari session
        $id_user   = $_SESSION['id_user'];
        $aktivitas = mysqli_real_escape_string($conn, $aktivitas);

        //Masukkan ke tabel log aktivitas 
        mysqli_query($conn, "
            INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas)
            VALUES ('$id_user', '$aktivitas', NOW())
        ");
    }
}

if (!function_exists('query_log')) {
    function query_log($sql, $aktivitas)
    {
        global $conn;

        mysqli_query($conn, $sql);
        log_aktivitas($aktivitas);
    }
}