<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Query sesuai tabel & kolom database
$query = mysqli_query(
    $conn,
    "SELECT * FROM tb_user 
     WHERE username='$username' 
     AND password='$password' 
     AND status_aktif='1'"
);

$data = mysqli_fetch_assoc($query);

if ($data) {

    // Set session
    $_SESSION['login'] = true;
    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    // Redirect berdasarkan role
    if ($data['role'] == 'admin') {
        header("Location: index.php");
    } elseif ($data['role'] == 'petugas') {
        header("Location: index.php");
    } elseif ($data['role'] == 'owner') {
        header("Location: index.php");
    } else {
        // Default redirect jika role tidak dikenali
        header("Location: index.php");
    }
    exit;

} else {
    echo "<script>
        alert('Username atau Password salah atau akun tidak aktif!');
        window.location='login.php';
    </script>";
}
?>