<?php
session_start(); // Keep only if session logic is needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Journeolink Business</title>
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
  background: url("/users-payments-management-system/public/images/BusinessRepOnboarding.jpg") no-repeat center 26% fixed;
  background-size: cover;
}



    .overlay {
  height: 100vh;
  box-sizing: border-box;
  padding: 2rem 3rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between; /* ⬅️ ensures top and bottom spacing */
  background: linear-gradient(to left, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.2) 70%, rgba(0,0,0,0) 100%);
}



    .brand {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1.5rem;
      font-weight: 700;
    }

    .brand img {
      height: 40px;
    }

    .brand span {
      color: #AEB877;
    }

    .content {
  position: relative;
  top: -3rem; /* ⬅️ moves content slightly upward */
  align-self: flex-end;
  text-align: left;
  max-width: 600px;
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

    .button-group {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <!-- Left: Logo -->
    <div class="brand">

      <img src="/users-payments-management-system/public/images/image 4.png" alt="Journeolink Logo">
      Journeolink <span>Business</span>
    </div>

    <!-- Right: Headline and buttons -->
    <div class="content">
      <h1>
        Showcase Your Business<br>with <span style="color:#FCC777;">Journeolink</span>
      </h1>

      <p class="subheadline">
        Let more people discover your business by becoming a<br>
        Journeolink Business-Partner today!
      </p>

      <div class="button-group">
        <a href="auth/business_rep_login.php" class="btn-login">Login</a>
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
  </div>
</body>
</html>
