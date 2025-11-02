<?php 

require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/accounts.php';

$reqIsValid = false;
$errMessage = null;

if(!isset($_GET["session_id"])) {
    $reqIsValid = false;
}

// this check is needed to show the reset password form only if there is a valid session
if(isset($_GET["session_id"])) {
    $sessionId = $_GET["session_id"];

    $conn = getDbConnection();
    $redisClient = getPredisClient(["prefix" => getResPassPrefix()]);

    // check if session exists so that we can show the reset password form
    $hashedSessId = hashResetPassSessionId($sessionId);
    $sessionKey = getResPassPrefix() . $hashedSessId;

    $sessionData = $redisClient->hgetall($sessionKey);

    // check if there is existing session and if it is not used yet
    if (!empty($sessionData) && ($sessionData['isUsed'] ?? 0) == 0) {
        $reqIsValid = true;
    } else {
        $reqIsValid = false;
        $errMessage = "Invalid or expired password reset session.";
    }
}

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

        .password-icon{
            width: 120px;
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
            <?php if(!$reqIsValid) { ?>
                <div class="bg-white p-4 rounded text-center d-flex flex-column justify-content-center align-items-center shadow-lg bg-opacity-75">
                    <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                        <i class="bi bi-x-circle-fill h1 text-danger"></i>
                    </div>
                    <div class="pb-3">
                        <h2 class="fw-bold text-brand-primary">Invalid Session</h2>
                        <p class="text-muted">Your reset password session is either invalid or has expired. Please try again.</p>
                    </div>
                    <a href="../../views/auth/forgot_password.php" class="btn btn-brand-primary bg-brand-primary text-white">Go to Forgot Password</a>
                </div>
            <?php } else { ?>
            <div class="form-container bg-white mh-100 px-4 py-5 rounded shadow-lg text-center bg-opacity-75" style="width: 600px;">
                <div class="pb-3 d-flex flex-column gap-4 align-items-center">
                    <img src="../../public/images/forgot-password-icon.png" alt="" class="password-icon">
                    <h2 class="fw-bold text-brand-primary">Reset Your Password</h2>
                    <p class="text-muted">Time for a fresh start! Go ahead and set a new password.</p>
                </div>
                <form action="../../api/auth/account_password.php" method="post">
                    <input type="hidden" name="action" value="perform_reset_password">
                    <input type="hidden" name="email" value="<?php echo $sessionData['email']; ?>">
                    <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sessionId); ?>">
                    <div class="d-flex flex-column gap-4 text-start">
                        <div class="w-100 d-flex flex-column">
                            <label for="new_password" class="form-label text-muted">New Password:</label>
                            <div class="input-group">
                                <input class="border-secondary form-control bg-transparent" type="password" id="new_password" name="new_password" placeholder="Enter your new password" required>
                                <span class="input-group-text togglePassword bg-transparent border-secondary" data-target="new_password" id="inputGroup-sizing-sm"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                            <div id="passwordCriteriaMsg" class="form-text input-note-text fst-italic text-secondary">
                                Password must include at least one uppercase letter, one lowercase letter, and one special character.
                            </div>      
                        </div>
                        <div class="w-100 d-flex flex-column">
                            <label for="confirm_password" class="form-label text-muted">Confirm Password:</label>
                            <div class="input-group">
                                <input class="border border-secondary form-control bg-transparent" type="password" id="confirm_password" placeholder="Confirm your new password" required>
                                <span class="input-group-text inputGroup-sizing-sm togglePassword bg-transparent border-secondary" data-target="confirm_password" id="inputGroup-sizing-sm"><i class="bi bi-eye-slash-fill"></i></span>
                            </div>
                            <div id="unmatchedPasswordErr" class="form-text input-note-text fst-italic d-none text-danger">
                                Password does not match.
                            </div>
                        </div>
                        <div class="w-100">
                            <button class="btn btn-brand-primary bg-brand-primary text-white w-100" type="submit" id="resetBtn" disabled>
                                <div class="spinner-border text-light loading-spinner d-none small" role="status" style="width: 20px; height: 20px;"></div>
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php }  ?>
        </div>
    </div>
    <?php include_once __DIR__ . '../../partials/global/user_msg_modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    const password = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const unmatchedPasswordErr = document.getElementById('unmatchedPasswordErr');
    const passwordCriteriaMsg = document.getElementById('passwordCriteriaMsg');
    const resetBtn = document.getElementById('resetBtn');

    password.addEventListener('input', () => {
        updateSubmitBtnState();
        updatePasswordCriteriaMsgState();
        updateUnmatchedPasswordErrState();
    });

    confirmPassword.addEventListener('input', () => {
        updateSubmitBtnState();
        updatePasswordCriteriaMsgState();
        updateUnmatchedPasswordErrState();
    });

    document.querySelectorAll('.togglePassword').forEach(button => {
        button.addEventListener('click', function () {
            const targetInput = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            }
        }); 
    });

    const doesPasswordMatch = () => {
        return confirmPassword.value === password.value; 
    }

    const isPasswordFormatValid = pw => {
        // password should contain atleast one uppercase, lowercase letter, and a special char
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/
        return regex.test(pw);
    }

    const updateSubmitBtnState = () => {
        if(!isPasswordFormatValid(password.value)) { return resetBtn.disabled = true; } 
        if(!doesPasswordMatch()) { return resetBtn.disabled = true; }
        resetBtn.disabled = false;
    }

    const updatePasswordCriteriaMsgState = () => {
        passwordCriteriaMsg.classList.remove('text-secondary', 'text-danger', 'text-success');
        if(password.value.length === 0) return passwordCriteriaMsg.classList.add('text-secondary');
        if(!isPasswordFormatValid(password.value)) return passwordCriteriaMsg.classList.add('text-danger');
        return passwordCriteriaMsg.classList.add('text-success');
    }

    const updateUnmatchedPasswordErrState = () => {
        if(confirmPassword.value.length === 0) {
            return unmatchedPasswordErr.classList.add('d-none');
        }

        if(!doesPasswordMatch()) {
            return unmatchedPasswordErr.classList.remove('d-none');
        }

        unmatchedPasswordErr.classList.add('d-none');
    }

    const handleFormSubmit = async () => {
        const form = resetBtn.closest('form');

        const spinner = resetBtn.querySelector('.loading-spinner');
        spinner.classList.remove('d-none');
        resetBtn.disabled = true;

        form.submit();
    }

    resetBtn.addEventListener('click', handleFormSubmit)
    </script>
</body>
</html>