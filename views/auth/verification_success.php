<?php 

session_start();

require_once __DIR__ . '/../../config/config.php';

$role = $_SESSION['role'] ?? null;
$isForOnboarding = isset($_SESSION['isForOnboarding']) ? $_SESSION['isForOnboarding'] : false;
$nextRedirection = $_SESSION['next_redirection'];
$altRedirection = isset($_SESSION['alt_redirection']) ? $_SESSION['alt_redirection'] : BASE_URL . '/views/auth/login.php';

unset($_SESSION['alt_redirection']);
unset($_SESSION['next_redirection']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../public/css/global.css">
    <title>Verification - Success</title>
</head>
<body>
    <div class="container">
        <div class="nav-title-bar d-flex justify-content-start align-items-center pt-3">
            <div class="d-flex flex-row align-items-center">
                <img src="/users-payments-management-system/public/images/image 4.png"  height="30">
                <span class=" fs-5 fw-bold text-brand-primary">Journeolink</span>
            </div>
        </div>
    </div>
    <div class="container px-lg-5" style="padding-top: 16%;">
        <h1 class="text-brand-primary fw-bold">Account Verified Successfully! 🎉</h1>
        <p class="text-muted">Your account has been successfully verified. </p>
        <p class="text-muted">Redirecting you automatically <?php if($role == 'bus_rep' || ($role == 'driver')) echo " to our onboarding process " ?> in <span id="countdown" class="fs-italic fw-bold text-black">5</span> seconds...</p>
        <p class="text-muted">Click <a href="<?php echo $altRedirection; ?>">here</a> to proceed to home. </p>
    </div>

</body>

<script>
    const countdownElement = document.getElementById('countdown');
    let redirectSeconds = 4;
    let interval = 1000; 
    let countdown = redirectSeconds;
    countdownElement.textContent = redirectSeconds;

    setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        if (countdown <= 0) {
            clearInterval();
            window.location.href = "<?php echo $nextRedirection; ?>";
        }
    }, interval);
</script>
</html>