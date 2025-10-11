<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../../public/css/global.css">
    <link rel="stylesheet" type="text/css" href="../../../public/css/business-signup.css">

    <title>Business - Sign Up</title>
</head>
<body>
    <!-- START OF MAIN CONTAINER -->
    <div class="d-flex flex-row">
        <div class="d-none d-md-block side-photo-container " style="">
        </div>  
        <div class="flex-grow-1">
            <div class="position-fixed w-100">
                <div class="nav-title-bar d-flex justify-content-start align-items-center">
                    <div class="d-flex flex-row align-items-center">
                        <img src="/users-payments-management-system/public/images/image 4.png"  height="30">
                        <span class=" fs-5 fw-bold text-brand-primary">Journeolink <span class="text-brand-secondary">Business</span></span>
                    </div>
                </div>
            </div>
            <div style="margin-top: var(--onboarding-nav-bar-height);">
                <div class="p-4 mx-auto  d-flex flex-column justify-content-center gap-3 form-content-container" style="">
                    <div>
                        <p class="fs-2 fw-bold text-brand-primary">Create a Business Account </p>
                        <p class="fs-6 text-secondary">Set up your user account to get started with Journeolink Business.</p>
                    </div>
                    <form action="../../../api/business/auth.php" method="post" class="d-flex flex-column gap-3">
                        <input type="text" name="action" id="" value="business_rep_signup" hidden>
                        <input type="text" name="role" value="driver" hidden>
                        <div class="d-flex flex-column flex-md-row gap-3">
                            <div class="w-100">
                                <label for="first_name" class="form-label fw-bold">First Name</label>
                                <input type="text" name="first_name" class="form-control custom-input" id="first_name" placeholder="Enter your first name" required>
                            </div>
                            <div class="w-100">
                                <label for="last_name" class="form-label fw-bold">Last Name</label>
                                <input type="text" name="last_name" class="form-control custom-input" id="last_name" placeholder="Enter your last name" required>
                            </div>
                        </div>
                        <div>
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control custom-input" id="email" placeholder="Enter your email" required>
                        </div>
                        <div>
                            <label for="password" class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control custom-input" id="password" placeholder="Enter your password" required>
                                <span class="input-group-text password-toggle-btn" id="togglePassword"><i class="bi bi-eye-fill"></i></span>
                            </div>
                        </div>
                        <div>
                            <label for="confirmPassword" class="form-label fw-bold">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control custom-input" id="confirmPassword" placeholder="Confirm your password">
                                <span class="input-group-text password-toggle-btn" id="toggleConfirmPassword"><i class="bi bi-eye-fill"></i></span>
                            </div>
                        </div>
                        <button type="submit" class="sign-up-btn btn text-light primary-color mt-3">Sign Up</button>
                    </form>
                    <div class="text-center">
                        <p class="text-secondary">Already have an account? <a href="./business_rep_login.php" class="text-brand-secondary">Log in</a></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- END OF MAIN CONTAINER -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../../../public/js/driver-signup.js"></script>
</body>
</html>