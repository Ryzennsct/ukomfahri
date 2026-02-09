<?php
$conn = mysqli_connect("localhost", "root", "", "db_parkir");

if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
