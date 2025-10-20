<?php
    session_start();

    require_once __DIR__ . '/../../../utils/auth.php';
    require_once __DIR__ . '/../../../utils/utils.php';
    require_once __DIR__ . '/../../../db/db_conn.php';
    require_once __DIR__ . '/../../../queries/business.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bus_rep') {
        redirectUser("../auth/business_rep_login.php");
        exit();
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/global.css">
    <link rel="stylesheet" href="../../../public/css/business-dashboard.css">
    <style>
        body {
            background-color: #f3f4f5ff;
        }
    </style>
</head>

<body>
    <!-- MAIN CONTAINER -->
    <div class="d-flex flex-row vh-100">
        <?php include_once __DIR__ . '../../../partials/business_rep/sidebar.php'; ?>
        <div class="flex-grow-1 overflow-y-scroll h-100">
            <?php include_once __DIR__ . '../../../partials/business_rep/navbar.php'; ?>
            <div class="container px-3 px-lg-5 pt-4 pb-4">
                <div class="pb-3 pt-2">
                    <h3 class="fw-bold text-brand-primary pb-2">My Account</h3>
                    <p class="text-muted lh-md">Displayed below are your basic account details.</p>
                </div>
                <div class="bg-white p-4 rounded mb-4 d-flex flex-column align-items-start gap-lg-4 gap-3 border border-light-gray">
                    <div class="avt-img-container">
                        <img 
                            src="<?php echo $_SESSION['acc_img_url'] ? $_SESSION['acc_img_url']  : '../../../public/images/placeholder-avt.jpg'; ?>" 
                            class="rounded-circle border avatar" 
                            width="120" 
                            height="120" 
                            style="object-fit: cover;"
                        >
                        <div class="edit-avatar-overlay rounded-circle">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-fill"></i> Edit
                            </button>
                        </div>
                    </div>
                    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="../../../api/account/account.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="role" value="<?php echo $_SESSION['role'] ?>">
                                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                <input type="text" name="action" value="edit_user_image" hidden>
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fs-5" id="editProfileModalLabel">Edit Business Representative Image</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php 
                                            $lastSlashPos = strrchr($_SESSION['acc_img_url'], '/'); 
                                            $imgFileName = substr($lastSlashPos, 1);
                                        ?>
                                        <img 
                                            src="<?php echo $_SESSION['acc_img_url'] ? $_SESSION['acc_img_url'] : '../../../public/images/placeholder-avt.jpg'; ?>" 
                                            class="rounded-circle border avatar mx-auto mb-3" 
                                            width="120" 
                                            height="120" 
                                            style="object-fit: cover;"
                                            id="previewProfileImgEdit"
                                        >
                                        <label for="profile_image" class="form-label">Profile Image</label>
                                        <input class="form-control form-control-sm" type="file" name="profile_image" id="profile_image" 
                                            value=""
                                            placeholder="<?php echo $imgFileName; ?>"
                                        >
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-brand-primary bg-brand-primary text-white btn-sm">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-3 align-items-center">
                        <div class="w-100">
                            <label class="form-label text-muted">First Name</label>
                            <input name="first_name" class="form-control form-control-sm" type="text" value="<?php echo $_SESSION['first_name']; ?>" readonly>
                        </div>
                        <div class="w-100">
                            <label class="form-label text-muted">Last Name</label>
                            <input name="last_name" class="form-control form-control-sm" type="text" value="<?php echo $_SESSION['last_name']; ?>" readonly>
                        </div>
                        <div class="w-100 align-self-md-end">
                            <button type="button" class="btn btn-sm btn-brand-secondary text-white" data-bs-toggle="modal" data-bs-target="#editAccInfoModal">
                                <i class="bi bi-pencil-fill mr-2"></i>
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal fade" id="editAccInfoModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../../../api/account/account.php" method="POST">
                                <input type="hidden" name="action" value="edit_account_info">
                                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Account Information</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fw-bold">Account Information</h6>
                                    <p class="text-muted small">Only your <strong>first name</strong> and <strong>last name</strong> can be edited.</p>
                                    <div class="w-100 pb-2">
                                        <label class="form-label text-muted">First Name</label>
                                        <input name="first_name" class="form-control form-control-sm" type="text" value="<?php echo $_SESSION['first_name']; ?>">
                                    </div>
                                    <div class="w-100 pb-2">
                                        <label class="form-label text-muted">Last Name</label>
                                        <input name="last_name" class="form-control form-control-sm" type="text" value="<?php echo $_SESSION['last_name']; ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-brand-primary bg-brand-primary text-white btn-sm">Save</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="w-100 pb-2">
                            <?php 
                                // this is just to mask email for privacy on shared screens
                                // honestly, this is redundant since the email is shown on the fckn navbar
                                // can be removed 
                                $maskedEmail = maskEmail($_SESSION['email']);
                            ?>
                            <label class="form-label text-muted">Email</label>
                            <input name="email" class="form-control form-control-sm" type="text" value="<?php echo $maskedEmail; ?>">       
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        // Preview selected profile image before upload
        const profileImageInput = document.getElementById('profile_image');
        const previewProfileImgEdit = document.getElementById('previewProfileImgEdit');

        profileImageInput.addEventListener('change', function(e) {
            console.log(e.target.result);
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewProfileImgEdit.setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }); 
    </script>
</body>
</html>