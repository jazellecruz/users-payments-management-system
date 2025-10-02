<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | JourneoLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('/users-payments-management-system/public/images/dreamstime_s_112555055.jpg') no-repeat center center;
      background-size: cover;
      font-family: 'Intern', sans-serif;
    }
    .card {
      backdrop-filter: blur(10px);
      background-color: rgba(255, 255, 255, 0.3);
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
      font-weight: 700;
      text-align: left;
    }
    .qc-tagline {
      color: #000;
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 300;
      text-align: left;
    }
    form button {
      margin-bottom: 19px;
    }
    form button:last-child {
      margin-bottom: 0;
    }
    h2, h3, h4, h5, h6,
    label, a, span {
      color: #3F562C;
    }
    .btn-brand {
      background-color: #3F562C;
      color: #fff;
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-brand:hover {
      background-color: #2d3f20 !important;
      color: #fff !important;
    }
  </style>
</head>
<body>
  <div style="position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.2); z-index:-1;"></div>

  <div class="container-fluid vh-100">
    <div class="row h-100 g-0">
      <!-- Left Column -->
      <div class="col-12 col-lg-6 d-flex flex-column position-relative h-100 p-5">
        <div class="position-absolute top-0 start-0 m-3 ms-3 mt-5 d-flex align-items-center gap-0">
          <img src="/users-payments-management-system/public/images/image%204.png" style="height:32px;">
          <span class="fw-bold" style="color:#3F562C; font-size:20px; font-family:Inter, sans-serif;">JourneoLink</span>
        </div>

        <div class="d-flex flex-column justify-content-center flex-grow-1">
          <h1 class="display-3 fw-bold mt-0 mb-0">Discover<br>Kyusi</h1>
          <p class="h5 fw-bold mb-1 mt-4" style="font-family: 'Intern', sans-serif;">Where Every Street Holds A Story</p>
          <p class="lead mb-1 fw-bold" style="font-family: 'Intern', sans-serif; color: #DCDCDC">
            Step Into A City Full Of Culture, Life, And<br>Endless Possibilities.
          </p>
        </div>

        <div>
          <p class="fw-bold position-absolute bottom-0 start-6" style="margin-left:-30px; color: white;">@Quezon City</p>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center">
        <div class="card p-4 shadow" style="width:450px; height:500px; flex-shrink:0; margin-top:40px;">
          <h3 class="mb-1 mt-3 text-center">Login</h3>
          <p class="qc-tagline mb-4 mt-0 text-center">Hey there! Ready to jump back in?</p>

          <form action="loading.php" method="POST" style="width: 80%; margin: 0 auto;">
            <!-- Email -->
            <div class="mb-4">
              <label><span style="font-weight:600;">Email Address</span></label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                  <img src="/users-payments-management-system/public/images/person%201.png" style="width:21px; height:21px;">
                </span>
                <input type="email" name="email" class="form-control border-start-0" placeholder="Enter Your Email Address"
                  onfocus="this.placeholder=''" onblur="this.placeholder='Enter Your Email Address'" required>
              </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
              <label><span style="font-weight:600;">Password</span></label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                  <img src="/users-payments-management-system/public/images/Rectangle.png" style="width:21px; height:21px;">
                </span>
                <input type="password" name="password" class="form-control border-start-0" placeholder="Your Password"
                  onfocus="this.placeholder=''" onblur="this.placeholder='Your Password'" required>
              </div>
              <div class="text-end mt-1" style="font-family: Inter; font-size: 15px;">
                <a href="login.php" style="color:#000; font-weight:400; text-decoration:underline;">Forgot password?</a>
              </div>
            </div>

            <!-- Buttons -->
            <button type="submit" name="login" class="btn btn-brand w-100 mb-3">Log in with Email</button>
            <button class="btn btn-outline-secondary w-100 mb-4 d-flex align-items-center justify-content-center">
              <img src="/users-payments-management-system/public/images/image%203.png" style="width:21px; height:21px; margin-right:12px;">
              <span style="font-weight:600;">Continue with Google</span>
            </button>
          </form>

          <!-- Footer -->
          <div class="text-center mt-3" style="font-family: Inter; font-size: 15px;">
            Don't Have an Account?
            <a href="signup.php" style="color:#000; font-weight:400; text-decoration:underline;">Sign Up</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
