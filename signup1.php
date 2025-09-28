<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up | JourneoLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('images/bridge-bg.jpg.png') no-repeat center center;

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
      <h3 class="mb-0">Sign Join Us!</h3>
      <p class="qc-tagline mb-8 mt-0">Your QC Adventure Starts Here!</p>
      <button class="btn btn-google w-100 mb-3 d-flex align-items-center justify-content-center" style="gap:8px;">
        <img src="images/image%203.png" style="width:21px; height:21px; flex-shrink:0; aspect-ratio:1/1;">
      <span style="font-weight:600;">
    Continue with Google</span></button>
      <form>
        <div class="mb-3">
          <div class="mb-3">
  <label><span style="font-weight:600;">
    Full Name</span></label>
  <div class="input-group">
    <span class="input-group-text bg-white border-end-0">
      <img src="images/person%201.png" style="width:21px; height:21px; flex-shrink:0; aspect-ratio:1/1;">
    </span>
    <input type="text" class="form-control border-start-0" placeholder="Name">
  </div>
</div>
        <div class="mb-3">
  <label><span style="font-weight:600;">
  Email Address</span></label>
  <div class="input-group">
    <span class="input-group-text bg-white border-end-0">
      <img src="images/email%201.png" style="width:21px; height:21px; flex-shrink:0; aspect-ratio:1/1;">
    </span>
    <input type="email" class="form-control border-start-0" placeholder="Username or Email">
  </div>
</div>
        <div class="mb-3">
  <label><span style="font-weight:600;">Password</span></label>
  <div class="input-group">
    <span class="input-group-text bg-white border-end-0">
      <img src="images/Rectangle.png" style="width:21px; height:21px; flex-shrink:0; aspect-ratio:1/1;">
    </span>
    <input type="password" class="form-control border-start-0" placeholder="••••••••">
  </div>
</div>
        <button type="submit" class="btn w-100" style="background-color:#000; color:#fff;">Sign Up</button>
      </form>
      <div class="text-center mt-3" style="font-family: Inter; font-size: 15px;">
  Already part of the adventure?
  <a href="login.php" style="color:#000; font-weight:400; text-decoration:underline;">
    Login in here
  </a>.
</div>
    </div>
  </div>
</body>
</html>
