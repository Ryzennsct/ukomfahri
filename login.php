<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Parkir</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      min-height: 100vh;
      background: url('https://images.unsplash.com/photo-1506521781263-d8422e82f27a') center/cover no-repeat;
      position: relative;
      font-family: 'Segoe UI', sans-serif;
    }

    /* Blur overlay */
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      z-index: 0;
    }

    .login-box {
      position: relative;
      z-index: 1;
      max-width: 420px;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.25);
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center">

  <div class="login-box">
    <h3 class="text-center mb-3">LOGIN PARKIR</h3>
    <p class="text-center text-muted mb-4">
      Masuk ke sistem informasi parkir
    </p>

    <form action="proses_login.php" method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        Login
      </button>
    </form>

    <div class="text-center mt-3">
      <small>© Sistem Parkir</small>
    </div>
  </div>

</body>
</html>
