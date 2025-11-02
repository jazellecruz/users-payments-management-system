<?php 
session_start();

// if no reset password status is set, user accessed this page directly without resetting password
// redirect them to forgot password page
if(!isset($_SESSION['reset_pass_status'])) {
   header("Location: ../../views/auth/forgot_password.php");
   exit();
} 

$postResetData = $_SESSION['reset_pass_status'];

$status = $postResetData['status'];
$message = $postResetData['userMsg'];

if($status === 'success') {
    $redirectLink = $postResetData['redirectLink'];
    $title = "Password Reset Successful";
}

if($status === 'error') {
    $redirectLink = '../../views/auth/forgot_password.php';
    $title = "Password Reset Failed";
}

unset($_SESSION['reset_pass_status']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../public/css/global.css">
    <style>
        .main-container {
            width:100vw;
            height:100vh;
            background: url('../../public/images/dreamstime_s_112555055.jpg') no-repeat center center;
            background-size: cover;
        }

        .icon-large {
            font-size: 120px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="position-absolute top-0 start-0 m-3 ms-4 mt-4 d-flex align-items-center gap-0">
          <img src="/users-payments-management-system/public/images/image%204.png" style="height:36px;">
          <span class="fw-bold text-brand-primary h4 mb-0" >JourneoLink</span>
        </div>
        <div class="container d-flex justify-content-center align-items-center h-100">
            <div class="bg-white mh-100 px-4 py-5 rounded shadow-lg text-center bg-opacity-75" style="width: 600px;">
                <div class="pb-3 d-flex flex-column gap-4 align-items-center">
                    <?php if($status === 'success') { ?>
                    <i class="bi bi-check-circle-fill icon-large text-brand-secondary"></i>
                    <?php } ?>
                    <?php if($status === 'error') { ?>
                    <i class="bi bi-x-circle-fill icon-large text-danger"></i>
                    <?php } ?>
                    <h2 class="fw-bold text-brand-primary"><?php echo $title; ?></h2>
                    <p class="text-muted"><?php echo $message; ?></p>
                    <a href="<?php echo $redirectLink; ?>" class="btn btn-brand-primary bg-brand-primary text-white">
                        <?php 
                            if($status === 'success') {
                                echo "Go to Login";
                            } else {
                                echo "Try Again";
                            }
                        ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>