<?php
    session_start();

    require_once __DIR__ . '/../../../utils/auth.php';
    require_once __DIR__ . '/../../../db/db_conn.php';
    require_once __DIR__ . '/../../../queries/business.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bus_rep') {
        redirectUser("../auth/business_rep_login.php");
        exit();
    }

    $conn = getDBConnection();

    // for now use user id to get business rep details
    $userId = $_SESSION['user_id'];
    $userEmail = $_SESSION['email'];

    $businessRep = getBusinessRepByUserId($conn, $userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Business Dashboard - Representative Profile</title>
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
                <?php if(empty($businessRep)) { ?>
                    <div class="">
                        <div class="d-flex flex-column justify-content-center align-items-center py-5 px-md-4">
                            <i class="bi bi-person-fill-slash text-muted h1"></i>
                            <h5 class="fw-bold  text-brand-primary mt-3">No Businesses Representative Profile Found</h5>
                            <p class="small text-muted text-center">Apply as a Business Representative to manage your businesses and applications.</p>
                            <a class="btn btn-brand-primary bg-brand-primary text-white" href="representative_form.php">Apply Now</a>
                        </div>
                    </div>   
                <?php } else {  ?>
                    <div class="pb-3 pt-2">
                        <h3 class="fw-bold text-brand-primary pb-2">My Profile</h3>
                        <p class="text-muted lh-md">Your Business Representative information is displayed here and will be used for future applications.</p>
                    </div>
                    <!-- Hero section for profile -->
                    <div class="bg-white p-4 rounded mb-4 d-flex align-items-center gap-lg-4 gap-3 border border-light-gray">
                        <div class="avt-img-container">
                            <img 
                                src="<?php echo $businessRep['profile_img_url'] ? $businessRep['profile_img_url'] : '../../../public/images/placeholder-avt.jpg'; ?>" 
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
                        <div class="d-flex flex-column justify-content-center ">
                            <h4 class="fw-bold text-brand-primary"><?php echo $businessRep['first_name']; ?> <?php echo $businessRep['last_name']; ?></h4>
                            <p class="text-muted p-0 m-0"><?php echo $userEmail; ?></p>
                        </div>
                    </div>  
                    <!-- Edit avatar modal -->
                    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="../../../api/business/profile.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                                <input type="hidden" name="business_rep_id" value="<?php echo $businessRep['business_rep_id']; ?>">
                                <input type="text" name="action" value="edit_profile_image" hidden>
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fs-5" id="editProfileModalLabel">Edit Business Representative Image</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php 
                                            $lastSlashPos = strrchr($businessRep['profile_img_url'], '/'); 
                                            $imgFileName = substr($lastSlashPos, 1);
                                        ?>
                                        <img 
                                            src="<?php echo $businessRep['profile_img_url'] ? $businessRep['profile_img_url'] : '../../../public/images/placeholder-avt.jpg'; ?>" 
                                            class="rounded-circle border avatar mx-auto mb-3" 
                                            width="120" 
                                            height="120" 
                                            style="object-fit: cover;"
                                            id="previewProfileImgEdit"
                                        >
                                        <label for="profile_image" class="form-label">Profile Image</label>
                                        <input class="form-control form-control-sm img-input" type="file" name="profile_image" data-target="previewProfileImgEdit" id="profile_image"
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

                    <div class="bg-white rounded mb-4 border border-light-gray">
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 border-bottom border-light-gray">
                            <p class="fw-bold fs-5 text-brand-primary mb-0 pb-0">Business Representative Information</p>
                        </div>
                        <div class="px-3 pb-3">
                            <div class="d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-2 flex-md-row pb-3">
                                    <div class="w-100">
                                        <label class="form-label text-muted">Business Representative Identification</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['public_business_rep_id']; ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Joined At</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo date('M j, Y, D h:i A', strtotime($businessRep['created_at'])); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded mb-4 border border-light-gray">
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 border-bottom border-light-gray">
                            <p class="fw-bold fs-5 text-brand-primary mb-0 pb-0">Personal Information</p>
                            <button type="button" class="btn btn-sm btn-warning px-4 text-white" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                        </div>
                        <div class="px-3 pb-3">
                            <div class="d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-2 flex-md-row pb-3">
                                    <div class="w-100">
                                        <label class="form-label text-muted">First Name</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['first_name']; ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Last Name</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['last_name']; ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Middle Name</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['middle_name']; ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Name Extension</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['ext_name']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-4 pb-3">
                                <div class="d-flex flex-column gap-2 flex-md-row">
                                    <div class="w-100">
                                        <label class="form-label text-muted">Birthdate</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo date('F j, Y', strtotime($businessRep['birth_date'])); ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Gender</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo ucfirst($businessRep['gender']); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="pb-3">
                                <div class="w-100">
                                    <label class="form-label text-muted">Address</label>
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['user_address']; ?>" readonly>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-4 pb-3">
                                <div class="d-flex flex-column gap-2 flex-md-row">
                                    <div class="w-100">
                                        <label class="form-label text-muted">Contact Number</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['active_phone_number']; ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Alternative Email</label>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $businessRep['alternative_email']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="pb-3">
                                <div class="w-100">
                                    <label class="form-label text-muted">Valid Government ID</label>
                                    <?php 
                                        $lastSlashPos = strrchr($businessRep['valid_id_url'], '/'); 
                                        $fileName = substr($lastSlashPos, 1);
                                    ?>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="text" class="form-control" value="<?php echo $fileName; ?>" readonly>
                                        <span class="input-group-text">
                                            <a class="text-secondary" target="_blank" href="<?php echo $businessRep['valid_id_url']; ?>">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="../../../api/business/profile.php" method="POST">
                                    <input type="hidden" name="action" value="edit_personal_info">
                                    <input type="hidden" name="business_rep_id" value="<?php echo $businessRep['business_rep_id']; ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Personal Information</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="w-100 pb-2">
                                            <label class="form-label text-muted">First Name</label>
                                            <input name="first_name" class="form-control form-control-sm" type="text" value="<?php echo $businessRep['first_name']; ?>">
                                        </div>
                                        <div class="w-100 pb-2">
                                            <label class="form-label text-muted">Last Name</label>
                                            <input name="last_name" class="form-control form-control-sm" type="text" value="<?php echo $businessRep['last_name']; ?>">
                                        </div>
                                        <div class="w-100 pb-2">
                                            <label class="form-label text-muted">Middle Name</label>
                                            <input name="middle_name" class="form-control form-control-sm" type="text" value="<?php echo $businessRep['middle_name']; ?>">
                                        </div>
                                        <div class="w-100 pb-2">
                                            <label class="form-label text-muted">Name Extension</label>
                                            <input name="ext_name" class="form-control form-control-sm" type="text" value="<?php echo $businessRep['ext_name']; ?>">
                                        </div>
                                        <div class="w-100 pb-2">
                                            <label class="form-label text-muted">Birthdate</label>
                                            <input name="birth_date" class="form-control form-control-sm" type="date" value="<?php echo date('Y-m-d', strtotime($businessRep['birth_date'])); ?>">
                                        </div>
                                        <div class="w-100 pb-2">
                                            <label class="form-label text-muted">Gender</label>
                                            <select name="gender" class="form-select form-select-sm" id="gender">
                                                <option value="male" <?php echo ($businessRep['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="female" <?php echo ($businessRep['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                            </select>
                                        </div>
                                        <div class="w-100 pb-2">
                                            <label class="form-label text-muted">Address</label>
                                            <input name="address" class="form-control form-control-sm" type="text" value="<?php echo $businessRep['user_address']; ?>">
                                        </div>
                                        <div class="d-flex flex-column gap-2 flex-md-row">
                                            <div class="w-100 pb-2">
                                                <label class="form-label text-muted">Contact Number</label>
                                                <input name="contact_num" class="form-control form-control-sm" type="text" value="<?php echo $businessRep['active_phone_number']; ?>">
                                            </div>
                                            <div class="w-100 pb-2">
                                                <label class="form-label text-muted">Alternative Email</label>
                                                <input name="alt_email" class="form-control form-control-sm" type="text" value="<?php echo $businessRep['alternative_email']; ?>">
                                            </div>
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
                    </div>
                <?php }  ?> 
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script> 
    <script src="../../../public/js/business-dashboard.js"></script>
</body>
</html>