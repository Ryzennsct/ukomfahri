<?php
session_start();
include 'koneksi.php';  
include 'log_helper.php';  
// CEK WAJIB LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>EasyParking</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/logoweb.jpg" rel="icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

</head>
<style>
/* Optional: Style untuk dropdown yang lebih baik */
.form-select {
    padding: 0.5rem 2.25rem 0.5rem 0.75rem;
}

.form-select option {
    padding: 8px;
}
#header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
}

/* Nav menu tetap di tempat */
#navmenu {
  position: relative;
}

/* Spasi body supaya konten tidak ketutup header */
body {
  padding-top: 90px; /* sesuaikan tinggi header */
}#header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
}

/* Nav menu tetap di tempat */
#navmenu {
  position: relative;
}

/* Spasi body supaya konten tidak ketutup header */
body {
  padding-top: 90px; /* sesuaikan tinggi header */
}
</style>

    <body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top"> <div class="container position-relative d-flex align-items-center justify-content-between">

    <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
    <img src="assets/img/logo.png" alt="Logo" class="logo-img me-2">
    <h1 class="sitename mb-0">EasyPark</h1>
</a>

    <nav id="navmenu" class="navmenu">
        <ul style="text-decoration: none;">
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="#hero" class="active">Home</a></li>
            <li><a href="#user">User List</a></li>
            <li><a href="#tarif">Tarif Parkir</a></li>
            <li><a href="#area">Area Parkir</a></li>
            <li><a href="#kendaraan">Kendaraan</a></li>
            <li><a href="#log">Log Aktivitas</a></li>
        <?php elseif ($_SESSION['role'] == 'petugas'): ?>
            <li><a href="#hero" class="active">Home</a></li>
            <li><a href="#transaksi">Transaksi</a></li>
        <?php elseif ($_SESSION['role'] == 'owner'): ?>
            <li><a href="#hero" class="active">Home</a></li>
            <li><a href="#rekap">Rekap Transaksi</a></li>
        <?php endif; ?>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav><!-- .nav-menu -->
      <?php
            // Pastikan session sudah dimulai di bagian paling atas file (sebelum HTML)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            ?>

        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
            <a href="logout.php" class="btn btn-cyan btn-logout">
        Logout
        </a>
        <?php else: ?>
            <a class="btn-getstarted flex-md-shrink-0" href="login.php" style="display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;">Login</a>
        <?php endif; ?>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section - DITAMPILKAN UNTUK SEMUA ROLE -->
    <section id="hero" class="hero section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">

            <?php
            // Query untuk mendapatkan total transaksi hari ini
            $today = date('Y-m-d');
            $query_transaksi = "SELECT COUNT(*) as total_transaksi FROM tb_transaksi WHERE DATE(waktu_masuk) = '$today'";
            $result_transaksi = mysqli_query($conn, $query_transaksi);
            $data_transaksi = mysqli_fetch_assoc($result_transaksi);
            $total_transaksi_hari_ini = $data_transaksi['total_transaksi'];

            // Query untuk mendapatkan total pendapatan keseluruhan (dari awal sampai sekarang)
            $query_pendapatan = "SELECT SUM(biaya_total) as total_pendapatan FROM tb_transaksi WHERE status = 'keluar'";
            $result_pendapatan = mysqli_query($conn, $query_pendapatan);
            $data_pendapatan = mysqli_fetch_assoc($result_pendapatan);
            $total_pendapatan_keseluruhan = $data_pendapatan['total_pendapatan'] ?? 0;

            // Format rupiah
            function formatRupiah($angka) {
                return 'Rp ' . number_format($angka, 0, ',', '.');
            }
            ?>

            <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-right" data-aos-delay="200">
            <div class="hero-content">
                <h1 class="hero-title">Sistem Informasi Parkir Kendaraan Berbasis Web</h1>
                <p class="hero-description">Aplikasi ini digunakan untuk membantu pengelolaan parkir kendaraan secara terkomputerisasi.</p>
                <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $total_transaksi_hari_ini; ?></span>
                    <span class="stat-label">Total Transaksi Hari Ini</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo formatRupiah($total_pendapatan_keseluruhan); ?></span>
                    <span class="stat-label">Total Pendapatan</span>
                </div>
                </div>
            </div>
            </div>

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left" data-aos-delay="300">
            <div class="hero-visual">
              <div class="hero-image-wrapper">
                <img src="asset-img/main-icon.jpg" class="img-fluid hero-image" alt="Hero Image">
                <div class="floating-elements">
                  <div class="floating-card card-1">
                    <i class="bi bi-lightbulb"></i>
                    <span>Easy to Use</span>
                  </div>
                  <div class="floating-card card-2">
                    <i class="bi bi-award"></i>
                    <span>Safe & Secure </span>
                  </div>
                  <div class="floating-card card-3">
                    <i class="bi bi-people"></i>
                    <span>Fast Entry</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

    </section><!-- /Hero Section -->
    
    <?php if ($_SESSION['role'] == 'admin'): ?>
    <!-- Features Section -->
    <section id="user" class="features section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span class="description-title">Data User</span>
        <h2>Data User</h2>
        <p>Daftar User yang Terdaftar</p>
      </div><!-- End Section Title -->
      <div class="container" data-aos="fade-up" data-aos-delay="100">

<div class="card-table"><!-- MULAI CARD -->

  <div class="tabs-wrapper">
    <div class="tab-content" data-aos="fade-up" data-aos-delay="200">

      <button class="btn btn-cyan mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        + Tambah User
      </button>

      <table class="table table-bordered table-striped table-hover">

                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            $query = mysqli_query($conn, "SELECT * FROM tb_user") or die("Query error: " . mysqli_error($conn));
                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= $row['nama_lengkap']; ?></td>
                        <td><?= $row['username']; ?></td>
                        <td class="text-center">
                        <span class="badge bg-primary"><?= $row['role']; ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($row['status_aktif'] == 1) { ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                        <button type="button"
                                class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditUser<?= $row['id_user']; ?>">
                            Edit
                        </button>

                            <a href="hapus_user.php?id=<?= $row['id_user']; ?>"
                            onclick="return confirm('Yakin hapus?')"
                            class="btn btn-danger btn-sm">
                            Hapus
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                        } else {
                        ?>
                        <tr>
                            <td colspan="7" class="text-center">Data user belum tersedia</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>

      </div>
    <!-- MODAL USER -->
    <?php
          mysqli_data_seek($query, 0);
          while ($row = mysqli_fetch_assoc($query)) {
        ?>
        <div class="modal fade"
            id="modalEditUser<?= $row['id_user']; ?>"
            tabindex="-1"
            aria-hidden="true">

          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

              <form action="user_update.php" method="POST">

                <div class="modal-header">
                  <h5 class="modal-title">Edit Data User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="id_user"
                        value="<?= $row['id_user']; ?>">

                  <div class="row">

                    <div class="col-md-6 mb-3">
                      <label class="form-label">Nama Lengkap</label>
                      <input type="text"
                            name="nama_lengkap"
                            class="form-control"
                            value="<?= $row['nama_lengkap']; ?>"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label">Username</label>
                      <input type="text"
                            name="username"
                            class="form-control"
                            value="<?= $row['username']; ?>"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label">Password</label>
                      <input type="text"
                            name="password"
                            class="form-control"
                            value="<?= $row['password']; ?>"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label">Role</label>
                      <select name="role" class="form-select" required>
                        <option value="admin" <?= $row['role']=='admin'?'selected':''; ?>>Admin</option>
                        <option value="petugas" <?= $row['role']=='petugas'?'selected':''; ?>>Petugas</option>
                        <option value="owner" <?= $row['role']=='owner'?'selected':''; ?>>Owner</option>
                      </select>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label">Status Aktif</label>
                      <select name="status_aktif" class="form-select" required>
                        <option value="1" <?= $row['status_aktif']==1?'selected':''; ?>>Aktif</option>
                        <option value="0" <?= $row['status_aktif']==0?'selected':''; ?>>Nonaktif</option>
                      </select>
                    </div>

                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button"
                          class="btn btn-secondary"
                          data-bs-dismiss="modal">
                      Batal
                  </button>
                  <button type="submit" class="btn btn-cyan">
                      Update
                  </button>
                </div>

              </form>

            </div>
          </div>
        </div>
<?php } ?>

    </div>

        </div>
      </div>
      
    </div>

  </div>
</div>
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form action="user_tambah.php" method="POST">

        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahUserLabel">Tambah Data User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row">

            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <input type="text" name="password" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Role</label>
              <select name="role" class="form-select" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="owner">Owner</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Status Aktif</label>
              <select name="status_aktif" class="form-select" required>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-cyan">Simpan</button>
        </div>

      </form>

    </div>
  </div>
</div>
</section><!-- /Features Section -->


    <!-- Features Section -->
    <section id="tarif" class="features section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <span class="description-title">Data Tarif</span>
    <h2>Data Tarif</h2>
    <p>Tarif Parkir Kendaraan</p>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="tabs-wrapper">
        <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
            
            <!-- Tombol Tambah -->
            <button class="btn btn-cyan mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahTarif">
                <i class="bi bi-plus-circle"></i> Tambah Tarif
            </button>

            <!-- Tabel Tarif -->
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Jenis Kendaraan</th>
                        <th>Tarif / Jam</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($conn, "SELECT * FROM tb_tarif ORDER BY id_tarif ASC");

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            // Badge warna sesuai jenis kendaraan
                            if ($row['jenis_kendaraan'] == 'mobil') {
                                $badge = '<span class="badge bg-info">Mobil</span>';
                            } elseif ($row['jenis_kendaraan'] == 'lainnya') {
                                $badge = '<span class="badge bg-secondary">Lainnya</span>';
                            } else {
                                $badge = '<span class="badge bg-primary">Motor</span>';
                            }
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center">
                                <?= $badge; ?>
                            </td>
                            <td class="text-center">
                                <strong>Rp <?= number_format($row['tarif_per_jam'], 0, ',', '.'); ?></strong>
                            </td>
                            <td class="text-center">
                                <!-- Tombol Edit - TRIGGER MODAL -->
                                <button type="button"
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditTarif<?= $row['id_tarif']; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>

                                <!-- Tombol Hapus -->
                                <button onclick="hapusTarif(<?= $row['id_tarif']; ?>, '<?= htmlspecialchars($row['jenis_kendaraan']); ?>')"
                                        class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus   
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDIT TARIF - HARUS DI DALAM LOOP -->
                        <div class="modal fade" 
                             id="modalEditTarif<?= $row['id_tarif']; ?>" 
                             tabindex="-1" 
                             aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="tarif_edit.php" method="POST">
                                        <input type="hidden" name="id_tarif" value="<?= $row['id_tarif']; ?>">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">
                                                <i class="bi bi-pencil"></i> Edit Tarif Parkir
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <!-- Info Jenis Kendaraan (Read Only) -->
                                            <div class="mb-3">
                                                <label class="form-label">Jenis Kendaraan</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="<?= ucfirst($row['jenis_kendaraan']); ?>" 
                                                       readonly>
                                                <input type="hidden" 
                                                       name="jenis_kendaraan" 
                                                       value="<?= $row['jenis_kendaraan']; ?>">
                                                <small class="text-muted">Jenis kendaraan tidak dapat diubah</small>
                                            </div>

                                            <!-- Input Tarif -->
                                            <div class="mb-3">
                                                <label class="form-label">Tarif Per Jam <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" 
                                                           name="tarif_per_jam" 
                                                           class="form-control" 
                                                           value="<?= $row['tarif_per_jam']; ?>" 
                                                           min="0"
                                                           step="500"
                                                           required>
                                                </div>
                                                <small class="text-muted">Masukkan tarif dalam Rupiah (contoh: 2000, 5000)</small>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Batal
                                            </button>
                                            <button type="submit" class="btn btn-cyan">
                                                <i class="bi bi-save"></i> Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END MODAL EDIT -->

                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle"></i> Data tarif belum tersedia
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>

  </div>

</section>

<!-- MODAL TAMBAH TARIF -->
<div class="modal fade" id="modalTambahTarif" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="tarif_tambah.php" method="POST">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i> Tambah Tarif Parkir
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Jenis Kendaraan -->
                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                        <select name="jenis_kendaraan" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Tarif Per Jam -->
                    <div class="mb-3">
                        <label class="form-label">Tarif Per Jam <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="tarif_per_jam" 
                                   class="form-control" 
                                   placeholder="Contoh: 2000" 
                                   min="0"
                                   step="500"
                                   required>
                        </div>
                        <small class="text-muted">Masukkan tarif dalam Rupiah</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-cyan">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
// Fungsi hapus tarif
function hapusTarif(id, jenis) {
    if (confirm(`Yakin ingin menghapus tarif ${jenis}?\n\nData yang terhapus tidak dapat dikembalikan!`)) {
        window.location.href = `tarif_hapus.php?id=${id}`;
    }
}
</script>
</section><!-- /Features Section -->

<!-- Features Section -->
<section id="area" class="features section">

<!-- Section Title -->
<div class="container section-title" data-aos="fade-up">
<span class="description-title">Data Area</span>
<h2>Data Area</h2>
<p>Data Area Parkir</p>
</div><!-- End Section Title -->

<div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="tabs-wrapper">
        <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
                <button class="btn btn-cyan btn-sm mb-4" data-bs-toggle="modal" data-bs-target="#modalTambahArea">
                    <i class="bi bi-plus-circle"></i> Tambah Area
                </button>
        <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Area</th>
                            <th>Kapasitas</th>
                            <th>Terisi</th>
                            <th>Tersedia</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($conn, "SELECT * FROM tb_area_parkir ORDER BY id_area ASC");

                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                $tersedia = $row['kapasitas'] - $row['terisi'];
                                
                                if ($tersedia == 0) {
                                    $status = '<span class="badge bg-danger">Penuh</span>';
                                } elseif ($tersedia <= 5) {
                                    $status = '<span class="badge bg-warning text-dark">Hampir Penuh</span>';
                                } else {
                                    $status = '<span class="badge bg-success">Tersedia</span>';
                                }
                                
                                $persentase = ($row['kapasitas'] > 0) ? round(($row['terisi'] / $row['kapasitas']) * 100) : 0;
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_area']); ?></strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $row['kapasitas']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark"><?= $row['terisi']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= $tersedia; ?></span>
                                </td>
                                <td class="text-center">
                                    <?= $status; ?>
                                    <div class="progress mt-2" style="height: 8px;">
                                        <div class="progress-bar <?= $persentase >= 80 ? 'bg-danger' : ($persentase >= 60 ? 'bg-warning' : 'bg-success'); ?>" 
                                             style="width: <?= $persentase; ?>%;">
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= $persentase; ?>%</small>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditArea<?= $row['id_area']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm"
                                            onclick="hapusArea(<?= $row['id_area']; ?>, '<?= htmlspecialchars($row['nama_area']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Edit Area -->
                            <div class="modal fade" id="modalEditArea<?= $row['id_area']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="area_edit.php" method="post">
                                        <input type="hidden" name="id_area" value="<?= $row['id_area']; ?>">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-pencil"></i> Edit Area Parkir
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Area</label>
                                                    <input type="text" name="nama_area" class="form-control" 
                                                           value="<?= htmlspecialchars($row['nama_area']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Kapasitas</label>
                                                    <input type="number" name="kapasitas" class="form-control" 
                                                           value="<?= $row['kapasitas']; ?>" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Terisi</label>
                                                    <input type="number" name="terisi" class="form-control" 
                                                           value="<?= $row['terisi']; ?>" min="0" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-cyan">
                                                    <i class="bi bi-save"></i> Update
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> Data area parkir belum tersedia
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Area -->
    <div class="modal fade" id="modalTambahArea" tabindex="-1">
        <div class="modal-dialog">
            <form action="area_tambah.php" method="post">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle"></i> Tambah Area Parkir
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Area</label>
                            <input type="text" name="nama_area" class="form-control" 
                                   placeholder="Contoh: Area A, Lantai 1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kapasitas</label>
                            <input type="number" name="kapasitas" class="form-control" 
                                   placeholder="Jumlah slot parkir" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Terisi Awal</label>
                            <input type="number" name="terisi" class="form-control" 
                                   value="0" min="0" required>
                            <small class="text-muted">Biasanya diisi 0 untuk area baru</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-cyan">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form> 
        </div>
    </div>
    <!-- Modal Hapus Area -->
<div class="modal fade" id="modalHapusArea" tabindex="-1" aria-labelledby="modalHapusAreaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalHapusAreaLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Area
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Apakah Anda yakin ingin menghapus area:</p>
                <h6 class="text-danger fw-bold" id="namaAreaHapus"></h6>
                <p class="text-muted small mb-0 mt-2">
                    <i class="bi bi-info-circle me-1"></i>Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-cyan   " id="btnKonfirmasiHapus">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>

</div>

</section><!-- /Features Section -->


    

    
    <!-- /features Section -->
    <section id="kendaraan" class="features section">

<!-- Section Title -->
<div class="container section-title mt-4" data-aos="fade-up">
    <span class="description-title">Data Kendaraan</span>
    <h2>Data Kendaraan</h2>
    <p>Manajemen Data Kendaraan</p>
</div>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <button class="btn btn-cyan btn-sm m-3" data-bs-toggle="modal" data-bs-target="#modalTambahKendaraan">
                <i class="bi bi-plus-circle"></i> Tambah Kendaraan
            </button>
        </div>

        <div class="card-body">
            <!-- Filter & Search -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <select name="jenis" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenis</option>
                            <option value="motor" <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'motor') ? 'selected' : ''; ?>>Motor</option>
                            <option value="mobil" <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'mobil') ? 'selected' : ''; ?>>Mobil</option>
                            <option value="lainnya" <?= (isset($_GET['jenis']) && $_GET['jenis'] == 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                        <?php if (isset($_GET['jenis']) && $_GET['jenis'] != ''): ?>
                            <a href="kendaraan.php" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari plat nomor..." 
                               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn btn-cyan">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Plat Nomor</th>
                            <th width="15%">Jenis</th>
                            <th width="15%">Warna</th>
                            <th width="20%">Pemilik</th>
                            <th width="15%">Ditambahkan Oleh</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        
                        // Query dengan filter dan JOIN ke tabel user
                        $where = "1=1";
                        if (isset($_GET['jenis']) && $_GET['jenis'] != '') {
                            $jenis_filter = mysqli_real_escape_string($conn, $_GET['jenis']);
                            $where .= " AND k.jenis_kendaraan = '$jenis_filter'";
                        }
                        if (isset($_GET['search']) && $_GET['search'] != '') {
                            $search = mysqli_real_escape_string($conn, $_GET['search']);
                            $where .= " AND k.plat_nomor LIKE '%$search%'";
                        }
                        
                        $query = mysqli_query($conn, "
                            SELECT k.*, u.username, u.nama_lengkap 
                            FROM tb_kendaraan k
                            LEFT JOIN tb_user u ON k.id_user = u.id_user
                            WHERE $where 
                            ORDER BY k.id_kendaraan DESC
                        ");

                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                // Badge warna berdasarkan jenis
                                if ($row['jenis_kendaraan'] == 'motor') {
                                    $badge_jenis = '<span class="badge bg-primary">Motor</span>';
                                } elseif ($row['jenis_kendaraan'] == 'mobil') {
                                    $badge_jenis = '<span class="badge bg-success">Mobil</span>';
                                } else {
                                    $badge_jenis = '<span class="badge bg-secondary">Lainnya</span>';
                                }
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center">
                                    <strong class="text-primary"><?= strtoupper(htmlspecialchars($row['plat_nomor'])); ?></strong>
                                </td>
                                <td class="text-center"><?= $badge_jenis; ?></td>
                                <td class="text-center">
                                    <span class="badge" style="background-color: <?= htmlspecialchars($row['warna'] ?? '#6c757d'); ?>; color: #000000;">
                                        <?= $row['warna'] ? ucfirst(htmlspecialchars($row['warna'])) : '-'; ?>
                                    </span>
                                </td>
                                <td><?= $row['pemilik'] ? htmlspecialchars($row['pemilik']) : '<span class="text-muted">-</span>'; ?></td>
                                <td class="text-center">
                                    <?php if ($row['nama_lengkap']): ?>
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-person-check"></i> <?= htmlspecialchars($row['nama_lengkap']); ?>
                                        </span>
                                        <br>
                                        <small class="text-muted">(<?= htmlspecialchars($row['username']); ?>)</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditKendaraan<?= $row['id_kendaraan']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm"
                                            onclick="hapusKendaraan(<?= $row['id_kendaraan']; ?>, '<?= htmlspecialchars($row['plat_nomor']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Edit Kendaraan (Individual per row) -->
                            <div class="modal fade" id="modalEditKendaraan<?= $row['id_kendaraan']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <form action="kendaraan_edit.php" method="POST">
                                        <input type="hidden" name="id_kendaraan" value="<?= $row['id_kendaraan']; ?>">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-pencil"></i> Edit Kendaraan
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                                                        <input type="text" name="plat_nomor" class="form-control text-uppercase" 
                                                               value="<?= htmlspecialchars($row['plat_nomor']); ?>" 
                                                               placeholder="Contoh: B 1234 XYZ" maxlength="15" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                                                        <select name="jenis_kendaraan" class="form-select" required>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="motor" <?= $row['jenis_kendaraan'] == 'motor' ? 'selected' : ''; ?>>Motor</option>
                                                            <option value="mobil" <?= $row['jenis_kendaraan'] == 'mobil' ? 'selected' : ''; ?>>Mobil</option>
                                                            <option value="lainnya" <?= $row['jenis_kendaraan'] == 'lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Warna</label>
                                                        <input type="text" name="warna" class="form-control" 
                                                               value="<?= htmlspecialchars($row['warna']); ?>" 
                                                               placeholder="Contoh: Hitam, Merah" maxlength="20">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Nama Pemilik</label>
                                                        <input type="text" name="pemilik" class="form-control" 
                                                               value="<?= htmlspecialchars($row['pemilik']); ?>" 
                                                               placeholder="Nama lengkap pemilik" maxlength="100">
                                                    </div>
                                                </div>
                                                
                                                <!-- Info user yang menambahkan (read-only) -->
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i> 
                                                    <strong>Ditambahkan oleh:</strong> 
                                                    <?= $row['nama_lengkap'] ? htmlspecialchars($row['nama_lengkap']) . ' (' . htmlspecialchars($row['username']) . ')' : 'Tidak diketahui'; ?>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle me-1"></i>Batal
                                                </button>
                                                <button type="submit" class="btn btn-cyan">
                                                    <i class="bi bi-save me-1"></i> Update
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> 
                                        <?php if (isset($_GET['search']) || isset($_GET['jenis'])): ?>
                                            Data tidak ditemukan
                                        <?php else: ?>
                                            Data kendaraan belum tersedia
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kendaraan (Outside loop, only once) -->
<div class="modal fade" id="modalTambahKendaraan" tabindex="-1" aria-labelledby="modalTambahKendaraanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahKendaraanLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Kendaraan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_tambah_kendaraan.php" method="POST" id="formTambahKendaraan">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="platNomor" class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="platNomor" name="plat_nomor" 
                            placeholder="Contoh: B 1234 XYZ" maxlength="15" required>
                        <small class="text-muted">Maksimal 15 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label for="jenisKendaraan" class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                        <select class="form-select" id="jenisKendaraan" name="jenis_kendaraan" required>
                            <option value="">-- Pilih Jenis Kendaraan --</option>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="warnaKendaraan" class="form-label">Warna</label>
                        <input type="text" class="form-control" id="warnaKendaraan" name="warna" 
                            placeholder="Contoh: Hitam, Putih, Merah" maxlength="20">
                        <small class="text-muted">Maksimal 20 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label for="pemilik" class="form-label">Pemilik</label>
                        <textarea class="form-control" id="pemilik" name="pemilik" rows="2" 
                                placeholder="Nama pemilik atau keterangan (opsional)" maxlength="100"></textarea>
                        <small class="text-muted">Maksimal 100 karakter</small>
                    </div>
                    
                    <!-- Info user yang sedang login -->
                    <div class="alert alert-success">
                        <i class="bi bi-person-check-fill"></i> 
                        <strong>Ditambahkan oleh:</strong> 
                        <?= htmlspecialchars($_SESSION['nama_lengkap']) . ' (' . htmlspecialchars($_SESSION['username']) . ')'; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Kendaraan -->
<div class="modal fade" id="modalHapusKendaraan" tabindex="-1" aria-labelledby="modalHapusKendaraanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalHapusKendaraanLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Kendaraan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Apakah Anda yakin ingin menghapus kendaraan dengan plat nomor:</p>
                <h6 class="text-danger fw-bold" id="platNomorHapus"></h6>
                <p class="text-muted small mb-0 mt-2">
                    <i class="bi bi-info-circle me-1"></i>Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-cyan" id="btnKonfirmasiHapusKendaraan">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

</section>

<?php
include 'koneksi.php';

// ================================
// SIMPAN LOG AKTIVITAS
// ================================

if (!function_exists('log_aktivitas')) {
    function log_aktivitas($aktivitas): void
    {
        global $conn;

        if (!isset($_SESSION['id_user'])) {
            return;
        }

        $id_user = $_SESSION['id_user'];
        $aktivitas = mysqli_real_escape_string($conn, $aktivitas);

        mysqli_query($conn, "
            INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas)
            VALUES ('$id_user', '$aktivitas', NOW())
        ");
    }
}

// ===============================
// AMBIL LOG AKTIVITAS
// ===============================
function get_log_aktivitas(): array
{
    global $conn;

    $query = "
        SELECT l.*, u.username
        FROM tb_log_aktivitas l
        LEFT JOIN tb_user u ON l.id_user = u.id_user
        ORDER BY l.waktu_aktivitas DESC
        LIMIT 20
    ";

    $result = mysqli_query($conn, $query);

    $logs = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
    }

    return $logs;
}

// 🔥 PANGGIL SEKALI AJA
$logs = get_log_aktivitas();
?>



    <section id="log" class="features section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
        <span class="description-title">Log Aktivitas</span>
        <h2>Log Aktivitas</h2>
        <p>Daftar Aktivitas</p>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="tabs-wrapper">
                <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
                <table class="table align-middle mb-0">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Nama User</th>
              <th>Aktivitas</th>
              <th>Waktu</th>
            </tr>
          </thead>

          <tbody>
<?php if (!empty($logs)) : ?>
    <?php $no = 1; ?>
    <?php foreach ($logs as $log) : ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td>
                <?= $log['username'] ?? '<span class="text-muted">System</span>' ?>
            </td>
            <td><?= $log['aktivitas'] ?></td>
            <td class="text-center">
                <?= date('d-m-Y H:i', strtotime($log['waktu_aktivitas'])) ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="4" class="text-center text-muted">
            Belum ada log aktivitas
        </td>
    </tr>
<?php endif; ?>
</tbody>

        </table>
                </div>
            </div>

        </div>

    </section><!-- /Features Section -->
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 'petugas'): ?>

                                  <!-- ================= TRANSAKSI ================= -->
<section id="transaksi" class="features section">

<!-- Section Title -->
<div class="container section-title" data-aos="fade-up">
  <span class="description-title">TRANSAKSI</span>
  <h2>TRANSAKSI</h2>
  <p>Transaksi Parkir</p>
</div>

<div class="container" data-aos="fade-up" data-aos-delay="100">

  <!-- ===== TAB BUTTON ===== -->
  <ul class="nav nav-tabs mb-4 justify-content-center gap-2" id="parkirTab" role="tablist">

    <li class="nav-item" role="presentation">
      <button class="nav-link active"
              data-bs-toggle="tab"
              data-bs-target="#masuk"
              type="button"
              role="tab">
        Parkir Masuk
      </button>
    </li>

    <li class="nav-item" role="presentation">
      <button class="nav-link"
              data-bs-toggle="tab"
              data-bs-target="#keluar"
              type="button"
              role="tab">
        Parkir Keluar
      </button>
    </li>

  </ul>

  <!-- ===== TAB CONTENT ===== -->
  <div class="tab-content" id="parkirTabContent">

    <!-- ================= PARKIR MASUK ================= -->
    <div class="tab-pane fade show active" id="masuk" role="tabpanel">

      <div class="card shadow-sm card-cyan">
        <div class="card-body">

          <h5 class="card-title-cyan">Form Parkir Masuk</h5>

          <form action="transaksi.php" method="POST">

            <div class="mb-3">
              <label class="form-label">Plat Nomor</label>
              <input type="text" name="plat" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Jenis Kendaraan</label>
              <select name="jenis" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Area Parkir</label>
              <select name="area" class="form-select" required>
                <?php
                $q = mysqli_query($conn,"
                  SELECT * FROM tb_area_parkir
                  WHERE terisi < kapasitas
                ");
                while($a = mysqli_fetch_assoc($q)){
                  echo "<option value='{$a['id_area']}'>
                          {$a['nama_area']}
                        </option>";
                }
                ?>
              </select>
            </div>

            <button name="masuk" class="btn btn-cyan w-100">
              Simpan Parkir Masuk
            </button>

          </form>

        </div>
      </div>

    </div>

    <!-- ================= PARKIR KELUAR ================= -->
    <div class="tab-pane fade" id="keluar" role="tabpanel">

      <div class="card shadow-sm card-cyan">
        <div class="card-body">

          <h5 class="card-title-cyan">Form Parkir Keluar</h5>

          <form action="transaksi.php" method="POST">

            <div class="mb-3">
              <label class="form-label">Pilih Kendaraan</label>
              <select name="id_transaksi" class="form-select" required>
                <?php
                $q = mysqli_query($conn,"
                  SELECT t.id_parkir, k.plat_nomor
                  FROM tb_transaksi t
                  JOIN tb_kendaraan k
                    ON t.id_kendaraan = k.id_kendaraan
                  WHERE t.status = 'masuk'
                ");
                while($d = mysqli_fetch_assoc($q)){
                  echo "<option value='{$d['id_parkir']}'>
                          {$d['plat_nomor']}
                        </option>";
                }
                ?>
              </select>
            </div>

            <button name="keluar" class="btn btn-cyan w-100">
              Parkir Keluar & Cetak
            </button>

          </form>

        </div>
      </div>

    </div>

  </div>
</div>
</section>



<script>
document.getElementById('plat').addEventListener('keyup',function(){

fetch('ajax_kendaraan.php?plat='+this.value)
.then(r=>r.json())
.then(d=>{

if(d){
document.getElementById('jenis').value=d.jenis_kendaraan;
}

});

});
</script>

			
                </div>
            </div>

        </div>

    </section><!-- /Features Section -->
    <?php endif; ?>


<!-- Features Section -->
 
 <!-- /end features Section-->



<!-- JavaScript untuk Sistem Parkir -->
<script>

</script>


 <!-- /end features Section-->


 <?php if ($_SESSION['role'] == 'owner'): ?>
<!-- Features Section -->
<section id="rekap" class="features section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <span class="description-title">Rekap Transaksi</span>
        <h2>Rekap Transaksi</h2>
        <p>Data Transaksi Parkir</p>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="tabs-wrapper">
            <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
                
                <!-- Data Transaksi Section -->
                <div class="data-section mt-5">
                    <h2 class="text-center mb-1" style="font-weight: 700; font-size: 2.5rem;">TRANSAKSI</h2>
                    <p class="text-center text-muted mb-4">Data Transaksi Parkir</p>
                    
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <!-- Filter Tanggal -->
                            <form method="GET" action="index.php">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggalMulai" 
                                               value="<?php echo isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="tanggal_akhir" id="tanggalAkhir"
                                               value="<?php echo isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select class="form-select" name="status" id="filterStatus">
                                            <option value="">Semua Status</option>
                                            <option value="masuk" <?php echo (isset($_GET['status']) && $_GET['status'] == 'masuk') ? 'selected' : ''; ?>>Masuk</option>
                                            <option value="keluar" <?php echo (isset($_GET['status']) && $_GET['status'] == 'keluar') ? 'selected' : ''; ?>>Keluar</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="submit" class="btn btn-cyan">
                                        <i class="bi bi-search me-2"></i>Cari Transaksi
                                    </button>
                                    <a href="cetak_pdf.php?tanggal_mulai=<?= $_GET['tanggal_mulai'] ?? '' ?>&tanggal_akhir=<?= $_GET['tanggal_akhir'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>" 
                                    class="btn btn-danger">
                                    Cetak PDF
                                    </a>
                                </div>
                            </form>

                            <!-- Table Transaksi -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead style="background-color: #2c3e50; color: white;">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">ID Parkir</th>
                                            <th class="text-center">Plat Nomor</th>
                                            <th class="text-center">Waktu Masuk</th>
                                            <th class="text-center">Waktu Keluar</th>
                                            <th class="text-center">Durasi (Jam)</th>
                                            <th class="text-center">Biaya Total</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Area</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transaksiTableBody">
                                        <?php
                                        // Ambil parameter filter dari URL
                                        $tanggalMulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
                                        $tanggalAkhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
                                        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
                                        
                                        // Query dasar
                                        $queryTransaksi = "SELECT 
                                                t.id_parkir,
                                                t.id_kendaraan,
                                                k.plat_nomor,
                                                t.waktu_masuk,
                                                t.waktu_keluar,
                                                t.durasi_jam,
                                                t.biaya_total,
                                                t.status,
                                                t.id_area
                                            FROM tb_transaksi t
                                            LEFT JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                                            WHERE 1=1";
                                        
                                        // Tambahkan filter tanggal mulai
                                        if (!empty($tanggalMulai)) {
                                            $queryTransaksi .= " AND DATE(t.waktu_masuk) >= '$tanggalMulai'";
                                        }
                                        
                                        // Tambahkan filter tanggal akhir
                                        if (!empty($tanggalAkhir)) {
                                            $queryTransaksi .= " AND DATE(t.waktu_masuk) <= '$tanggalAkhir'";
                                        }
                                        
                                        // Tambahkan filter status
                                        if (!empty($statusFilter)) {
                                            $queryTransaksi .= " AND t.status = '$statusFilter'";
                                        }
                                        
                                        $queryTransaksi .= " ORDER BY t.waktu_masuk DESC LIMIT 100";
                                        
                                        $resultTransaksi = mysqli_query($conn, $queryTransaksi);
                                        
                                        if($resultTransaksi && mysqli_num_rows($resultTransaksi) > 0) {
                                            $no = 1;
                                            while($row = mysqli_fetch_assoc($resultTransaksi)) {
                                                // Format waktu
                                                $waktuMasuk = date('d/m/Y H:i', strtotime($row['waktu_masuk']));
                                                $waktuKeluar = $row['waktu_keluar'] ? date('d/m/Y H:i', strtotime($row['waktu_keluar'])) : '-';
                                                
                                                // Badge status
                                                $statusBadge = ($row['status'] == 'masuk') ? 
                                                    '<span class="badge bg-success">Masuk</span>' : 
                                                    '<span class="badge bg-secondary">Keluar</span>';
                                                
                                                // Format biaya
                                                $biaya = $row['biaya_total'] ? 'Rp ' . number_format($row['biaya_total'], 0, ',', '.') : 'Rp 0';
                                                
                                                // Durasi
                                                $durasi = $row['durasi_jam'] ?? '0';
                                                
                                                echo "<tr>";
                                                echo "<td class='text-center'>{$no}</td>";
                                                echo "<td class='text-center'><strong>{$row['id_parkir']}</strong></td>";
                                                echo "<td class='text-center'><strong class='text-primary'>{$row['plat_nomor']}</strong></td>";
                                                echo "<td class='text-center'>{$waktuMasuk}</td>";
                                                echo "<td class='text-center'>{$waktuKeluar}</td>";
                                                echo "<td class='text-center'>{$durasi}</td>";
                                                echo "<td class='text-center fw-bold text-success'>{$biaya}</td>";
                                                echo "<td class='text-center'>{$statusBadge}</td>";
                                                echo "<td class='text-center'>Area {$row['id_area']}</td>";
                                                echo "</tr>";
                                                $no++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='9' class='text-center py-4'>Tidak ada data transaksi</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php
                            // Tampilkan info filter yang sedang aktif
                            if (!empty($tanggalMulai) || !empty($tanggalAkhir) || !empty($statusFilter)) {
                                echo "<div class='alert alert-info mt-3'>";
                                echo "<strong>Filter Aktif:</strong> ";
                                if (!empty($tanggalMulai)) echo "Tanggal Mulai: " . date('d/m/Y', strtotime($tanggalMulai)) . " | ";
                                if (!empty($tanggalAkhir)) echo "Tanggal Akhir: " . date('d/m/Y', strtotime($tanggalAkhir)) . " | ";
                                if (!empty($statusFilter)) echo "Status: " . ucfirst($statusFilter);
                                echo " <a href='index.php' class='btn btn-sm btn-outline-secondary ms-3'>Reset Filter</a>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
        
            </div>
        </div>
    </div>

    <style>

    body {
        background: linear-gradient(135deg, #e3f2fd, #ffffff);
        min-height: 100vh;
    }


    .data-section h2 {
        color: #2c3e50;
        position: relative;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead th {
        font-weight: 600;
        font-size: 0.95rem;
        padding: 15px 10px;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #ced4da;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .gap-2 {
        gap: 0.5rem;
    }
    </style>

</section><!-- /Features Section -->
<?php endif; ?>

<script>
// JavaScript untuk fitur pencarian tanggal
document.getElementById('btnCari')?.addEventListener('click', function() {
    const tanggalMulai = document.getElementById('tanggalMulai').value;
    const tanggalAkhir = document.getElementById('tanggalAkhir').value;
    const status = document.getElementById('filterStatus').value;
    
    // Redirect dengan parameter pencarian
    let url = 'index.php?';
    if(tanggalMulai) url += 'tanggal_mulai=' + tanggalMulai + '&';
    if(tanggalAkhir) url += 'tanggal_akhir=' + tanggalAkhir + '&';
    if(status) url += 'status=' + status;
    
    window.location.href = url;
});
</script>

  </main>

  <footer id="footer" class="footer position-relative dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">EasyPark</span>
          </a>
        </div>



      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">EasyPark</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by Fahri</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>


  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="backup.js"></script>
  <script src="script.js"></script>

  

</body>

</html>
<!-- JavaScript untuk Hapus Kendaraan -->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript untuk Hapus Kendaraan -->
<script>
let idKendaraanHapus = null;

function hapusKendaraan(id, platNomor) {
    idKendaraanHapus = id;
    document.getElementById('platNomorHapus').textContent = platNomor.toUpperCase();
    
    // Tampilkan modal
    const modalHapus = new bootstrap.Modal(document.getElementById('modalHapusKendaraan'));
    modalHapus.show();
}

// Event listener untuk tombol konfirmasi hapus
document.getElementById('btnKonfirmasiHapusKendaraan').addEventListener('click', function() {
    if (idKendaraanHapus) {
        // Redirect ke halaman proses hapus
        window.location.href = 'kendaraan_hapus.php?id=' + idKendaraanHapus;
    }
});

// Validasi form tambah kendaraan
document.getElementById('formTambahKendaraan')?.addEventListener('submit', function(e) {
    const platNomor = document.getElementById('platNomor').value.trim();
    const jenisKendaraan = document.getElementById('jenisKendaraan').value;
    
    if (!platNomor) {
        e.preventDefault();
        alert('Plat nomor harus diisi!');
        return false;
    }
    
    if (!jenisKendaraan) {
        e.preventDefault();
        alert('Jenis kendaraan harus dipilih!');
        return false;
    }
});

// Auto uppercase untuk plat nomor
document.getElementById('platNomor')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>