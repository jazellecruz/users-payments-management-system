<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | JourneoLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: url('public/images/bridge-bg.jpg.png') no-repeat center center;
      background-size: cover;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="max-width: 420px; width: 100%; border-radius: 16px;">
      <h3 class="mb-3 text-center">Welcome Back!</h3>
      <p class="text-muted text-center">Hey there! Ready to jump back in?</p>

      <!-- Google Button -->
      <button class="btn btn-outline-secondary w-100 mb-4 d-flex align-items-center justify-content-center">
        <img src="public/images/image%203.png" class="me-2" style="width:21px; height:21px;">
        Continue with Google
      </button>

      <!-- Login Form -->
      <form action="loading.php" method="POST">
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text">
              <img src="public/images/email%201.png" style="width:21px; height:21px;">
            </span>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text">
              <img src="public/images/Rectangle.png" style="width:21px; height:21px;">
            </span>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
          </div>
        </div>

        <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
      </form>

      <!-- Forgot Password -->
      <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-underline">Forgot your password?</a>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
