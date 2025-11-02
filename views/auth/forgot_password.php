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
            <div class="form-container bg-white mh-100 px-4 py-5 rounded shadow-lg text-center bg-opacity-75">
                <div class="pb-3 d-flex flex-column gap-4 align-items-center">
                    <img src="../../public/images/forgot-password-icon.png" alt="" class="password-icon">
                    <h2 class="fw-bold text-brand-primary">Forgot Your Password?</h2>
                    <p class="text-muted">No worries! Just enter your email address below and we'll send you a password reset link.</p>
                </div>
                <form action="../../api/auth/account_password.php" method="post">
                    <input type="hidden" name="action" value="request_reset_password">
                    <div class="d-flex flex-column gap-4 text-start">
                        <div class="w-100 d-flex flex-column">
                            <label for="email" class="form-label text-muted">Your Email</label>
                            <input type="email" name="email" class="border border-2 border-secondary form-control bg-transparent" id="" placeholder="Enter your email" required>
                        </div>
                        <div class="w-100">
                            <button class="btn btn-brand-primary bg-brand-primary text-white w-100 py-2" type="button" class="w-100" id="sendResetLinkBtn">
                                <div class="spinner-border text-light loading-spinner d-none small" role="status" style="width: 20px; height: 20px;"></div>
                                Send Link
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include_once __DIR__ . '../../partials/global/user_msg_modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const userMsgModal = new bootstrap.Modal(document.getElementById('userMsgModal'));
        const sendResetLinkBtn = document.getElementById('sendResetLinkBtn');
        const form = sendResetLinkBtn.closest('form');

        const handleFormSubmit = async (event) => {
            const form = sendResetLinkBtn.closest('form');
            const formData = new FormData(form);

            const spinner = sendResetLinkBtn.querySelector('.loading-spinner');
            spinner.classList.remove('d-none');
            sendResetLinkBtn.disabled = true;

            try {
                const res = await axios.post('../../api/auth/account_password.php', formData);

                spinner.classList.add('d-none');
                sendResetLinkBtn.disabled = false;

                showUserMsgModal({
                    title: 'Email Sent!',
                    content: 'A password reset link has been sent to your email address. Please check your inbox.',
                    headerBgColor: 'bg-success',
                    titleColor: 'text-white'
                });
            } catch (error) {
                // Hide loading spinner
                spinner.classList.add('d-none');
                sendResetLinkBtn.disabled = false;

                showUserMsgModal({
                    title: 'Error',
                    content: 'An error occurred while processing your request. Please try again later.',
                    headerBgColor: 'bg-danger',
                    titleColor: 'text-white'
                });
            } finally {
                // Hide loading spinner
                spinner.classList.add('d-none');
                sendResetLinkBtn.disabled = false;
                form.reset();
            }
        };

        sendResetLinkBtn.addEventListener('click', handleFormSubmit)
    </script>
</body>
</html>