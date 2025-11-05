<?php
    session_start();
    $redirectUrl = $_SESSION['verification_return_url'] ?? '../../views/auth/sign-up.php';
    unset($_SESSION['verification_return_url']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/global.css">
    <title>Verification - Notice</title>
</head>
<body>
    <!-- START OF MAIN CONTAINER -->
    <div class="d-flex flex-row">
        <div class="flex-grow-1 container">
            <div>
                <div class="nav-title-bar d-flex justify-content-start align-items-center pt-3">
                    <div class="d-flex flex-row align-items-center">
                        <img src="/users-payments-management-system/public/images/image 4.png"  height="30">
                        <span class=" fs-5 fw-bold text-brand-primary">Journeolink</span>
                    </div>
                </div>
            </div>
            <div class="container" style="padding-top: 16%;">
                <p class="text-brand-primary fw-bold fs-1">📬 Check Your Inbox</p>
                <p class="fs-6 text-secondary lh-lg w-75">
                    We've sent a confirmation email to verify your account. Please check your inbox to complete your registration on Journeolink.
                </p>
                <div class="d-flex flex-row gap-2">
                    <a href="<?php echo $redirectUrl; ?>" role="button" class="btn btn-secondary btn-brand-primary text-white bg-brand-primary">
                        Go back
                    </a>
                </div>
            </div>
        </div>
        <div class="d-none d-md-block side-photo-container" style=""></div>
    </div>
    <!-- END OF MAIN CONTAINER -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

