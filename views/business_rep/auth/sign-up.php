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
                        <p class="fs-3 fw-bold text-brand-primary mb-1">Create a Business Account </p>
                        <p class="fs-6 text-secondary mb-3">Set up your user account to get started with Journeolink Business.</p>
                    </div>
                    <form action="../../../api/auth/signup_account.php" method="post" class="d-flex flex-column gap-3" id="businessSignUpForm">
                        <input type="text" name="action" id="" value="account_signup" hidden>
                        <input type="text" name="role" value="bus_rep" hidden>
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
                                <span class="input-group-text togglePassword" data-target="password"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                            <div id="passwordCriteriaMsg" class="form-text input-note-text fst-italic text-secondary">
                                Password must include at least one uppercase letter, one lowercase letter, and one special character.
                            </div>   
                        </div>
                        <div>
                            <label for="confirmPassword" class="form-label fw-bold">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control custom-input" id="confirmPassword" placeholder="Confirm your password">
                                <span class="input-group-text togglePassword" data-target="confirmPassword"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                            <div id="unmatchedPasswordErr" class="form-text input-note-text fst-italic d-none text-danger">
                                Password does not match.
                            </div>
                        </div>
                        <button type="submit" class="sign-up-btn btn text-light primary-color mt-3 btn-brand-primary btn-sm" id="signupBtn" disabled>
                            <div class="spinner-border text-light loading-spinner d-none small" role="status" style="width: 20px; height: 20px;"></div>
                            Sign Up
                        </button>
                    </form>
                    <div class="text-center">
                        <p class="text-secondary mb-0">Already have an account? <a href="./business_rep_login.php" class="text-brand-secondary">Log in</a></p>
                    </div>
                    <div class="w-100">
                        <form   action="../../../api/oauth/auth.php" method="get">
                            <input type="hidden" name="role" value="bus_rep">
                            <input type="hidden" name="for-onboarding" value="true">
                            <input type="text" name="action" id="" value="business_rep_signup" hidden>
                            <button type="submit" class="btn btn-light btn-sm w-100 border border-light-gray rounded d-flex flex-row justify-content-center align-items-center gap-2 mt-2">
                                <img src="../../../public/images/image 3.png" alt="" srcset="" height="16">
                                Sign Up with Google
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- END OF MAIN CONTAINER -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../../../public/js/business-signup.js"></script>
</body>
</html>