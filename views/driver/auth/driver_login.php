<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login | JourneoLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            font-family: 'Outfit', sans-serif;
        }
       .left-side {
            flex: 1;
            background: url('/users-payments-management-system/public/images/driver_login.jpg')
            no-repeat center center;
            background-size: cover;
            filter: brightness(0.9) contrast(.8);
            image-rendering: crisp-edges;
        }
        .right-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
        }
        .login-container {
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .logo {
            display: block;
            margin: 0 auto 1px;
        }
        .title {
            color: #3F562C;
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }
        .admin-title {
            color: #988E42;
            margin: 10px 0 5px;
        }
        .subtitle {
            color: #5A5D58;
            margin: 0 0 20px;
            white-space: nowrap;
        }
        form {
            text-align: left;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        input {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #3F562C;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .forgot-password {
            text-decoration: underline;
            color: #5A5D58;
            font-size: 14px;
            margin-top: 10px;
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="left-side"></div>
    <div class="right-side">
        <div class="login-container">
            <img src="/users-payments-management-system/public/images/jlinklogo.png" alt="JourneoLink Logo" class="logo">
            <h1 class="title">JourneoLink</h1>
            <h2 class="admin-title">JOURNEOLINK DRIVER</h2>
            <p class="subtitle">Sign in with your credentials to access your dashboard.</p>
            <form method="POST" action="../../auth/loading.php">
                <input type="hidden" name="login" value="1">
                <input type="hidden" name="role" value="driver">
                <label>Email</label>
                <input type="email" name="email" placeholder="Email" required>
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">LOG IN</button>
                <div class="d-flex justify-content-between mt-1">
                    <a href="#" class="forgot-password">Forgot Password?</a>
                    <a href="sign-up.php" class="forgot-password">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>