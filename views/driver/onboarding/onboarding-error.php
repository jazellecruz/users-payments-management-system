<?php

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
    <title>Driver - Onboarding</title>
    <style>
        :root {
            --primary-color: #3F562C; /* Global variable */
            --secondary-color: #988E42; /* Global variable */
            --nav-bar-height: 50px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
        }

        .bg-brand-primary{
            background-color: var(--primary-color);
        }

        .btn-brand-primary:hover {
            background-color: #2c3b1fff;
        }

        .side-photo-container {
            height: 100vh;
            width: 23%;
            object-fit: cover;
            background-repeat: repeat; 
            background-size: 500px  !important;
            background: url('/users-payments-management-system/public/images/driver-side-photo.jpg');
            position: relative;
            right: 0;
        }

        .text-brand-secondary {
            color:  var(--secondary-color);
        }

        .text-brand-primary {
            color: var(--primary-color);
        }
        .nav-title-bar{
            padding: 20px 10px;
            height: var(--nav-bar-height);
            background-color: white;
            position: relative;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- START OF MAIN CONTAINER -->
    <div class="d-flex flex-row">
        <div class="flex-grow-1 container">
            <div>
                <div class="nav-title-bar d-flex justify-content-start align-items-center">
                    <div class="d-flex flex-row align-items-center">
                        <img src="/users-payments-management-system/public/images/image 4.png"  height="30">
                        <span class=" fs-5 fw-bold text-brand-primary">Journeolink <span class="text-brand-secondary">Drivers</span></span>
                    </div>
                </div>
            </div>
            <div class="container" style="padding-top: 20%;">
                <p class="text-brand-primary fw-bold fs-2">Oops! Something went wrong...</p>
                <p class="text-brand-primary fw-bold fs-4"><span class="text-danger">HTTP 500 ERROR </span> INTERNAL SERVER ERROR</p>
                <p class="fs-6 text-secondary lh-lg" style="width: 80%">We encountered an unexpected problem. Your application wasn’t processed. Please try again later or reach out to our support team.</p>
                <div class="d-flex flex-row gap-3">
                    <a href="/users-payments-management-system/driver/auth/sign-in" role="button" class="btn btn-sm btn-secondary">
                        Go Home
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

