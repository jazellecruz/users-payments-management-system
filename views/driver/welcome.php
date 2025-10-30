<?php
// Remove this if you don't use session logic here
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Journeolink Drive</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../public/css/global.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Outfit', sans-serif;
      color: #fff;
    }

    body {
      background: url("/users-payments-management-system/public/images/DriverGetStarted.jpg") no-repeat center center fixed;
      background-size: cover;
    }

    .overlay {
      height: 100vh;
      box-sizing: border-box;
      padding: 2rem 3rem;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.2) 70%, rgba(0,0,0,0) 100%);
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 5rem;
    }

    .brand img {
      height: 40px;
    }

    .brand span {
      color: #AEB877;
    }

    h1 {
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .subheadline {
      font-size: 1.1rem;
      color: #cbd5e1;
      margin-bottom: 1rem;
      max-width: 600px;
    }

    .btn-login {
      background-color: #000;
      color: #fff;
      border: 2px solid #fff;
      border-radius: 0.5rem;
      padding: 0.6rem 1.5rem;
      font-weight: 600;
      text-decoration: none;
    }

    .btn-login:hover {
      background-color: #111;
    }

    .btn-getstarted {
      background-color: #22c55e;
      color: #fff;
      border-radius: 0.5rem;
      padding: 0.6rem 1.5rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-getstarted:hover {
      background-color: #16a34a;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <!-- Logo + brand text -->
    <div class="brand">
      <img src="/users-payments-management-system/public/images/image 4.png" alt="Journeolink Logo">
      Journeolink <span>Drive</span>
    </div>

    <!-- Headline -->
    <h1>
      Earn, Drive, and Thrive <br>with <span style="color:#FCC777;">Journeolink</span>.
    </h1>

    <!-- Subheadline -->
    <p class="subheadline">
      Become one of our driver-partners at Journeolink<br>
      and start earning today. Apply now!
    </p>

    <!-- Buttons -->
    <div style="display:flex; gap:1rem; margin-top:1rem;">
      <a href="auth/driver_login.php" class="btn-login">Login</a>
      <a href="auth/sign-up.php" class="btn-getstarted">
        Get Started
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
             viewBox="0 0 16 16">
          <path fill-rule="evenodd"
                d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 
                   0 0 1 .708-.708l4 4a.5.5 0 0 1 
                   0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 
                   8.5H1.5A.5.5 0 0 1 1 8z"/>
        </svg>
      </a>
    </div>
  </div>
</body>
</html>
