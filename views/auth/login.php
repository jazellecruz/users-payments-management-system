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
    .card {
      backdrop-filter: blur(10px);
      background-color: rgba(255, 255, 255, 0.85);
      border-radius: 24px;
    }
    .btn-google {
      background-color: #fff;
      border: 1px solid #ccc;
      color: #444;
    }
    h3 {
  color: #000;
  font-family: 'Inter', sans-serif;
  font-size: 24px;
  font-style: normal;
  font-weight: 700;
  line-height: normal;
  text-align: left; 
}
.qc-tagline {
  color: #000;
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-style: normal;
  font-weight: 300;
  line-height: normal;
  text-align: left;
}



  </style>
</head>
<body>
    <div style="position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.2); z-index:-1;"></div>
  <div class="container d-flex align-items-start vh-100">
    <div class="card p-4 shadow" style="width:520px; height:500px; flex-shrink:0; margin-top:40px;">
      <h3 class="mb-0">Welcome Back!</h3>
      <p class="qc-tagline mb-8 mt-0">Hey there! Ready to jump back in?</p>
       <button 
  class="btn btn-outline-secondary w-100 mb-4 d-flex align-items-center justify-content-center" 
  style="gap:8px;">
        <img src="public/images/image%203.png" style="width:21px; height:21px; flex-shrink:0; aspect-ratio:1/1;">
      <span style="font-weight:600;">
    Continue with Google</span></button>
      <form action="loading.php" method="POST">

        <div class="mb-3">
</div>
        <div class="mb-4">
  <label><span style="font-weight:600;">
  Email Address</span></label>
  <div class="input-group">
    <span class="input-group-text bg-white border-end-0">
      <img src="public/images/email%201.png" style="width:21px; height:21px; flex-shrink:0; aspect-ratio:1/1;">
    </span>
    <input type="email" name="email" class="form-control border-start-0" placeholder="you@example.com" onfocus="this.placeholder=''" 
       onblur="this.placeholder='you@example.com'"required>
  </div>
</div>
        <div class="mb-4">
  <label><span style="font-weight:600;">Password</span></label>
  <div class="input-group">
    <span class="input-group-text bg-white border-end-0">
      <img src="public/images/Rectangle.png" style="width:21px; height:21px; flex-shrink:0; aspect-ratio:1/1;">
    </span>
    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" onfocus="this.placeholder=''" 
       onblur="this.placeholder='••••••••'" required>
  </div>
</div>
        <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
      </form>
      <div class="text-center mt-3" style="font-family: Inter; font-size: 15px;">
  <a href="login.php" style="color:#000; font-weight:400; text-decoration:underline;">
    Forgot your password?</a>
  Let's get you back in.
  </div>
</div>
    </div>
  </div>
</body>
</html>
