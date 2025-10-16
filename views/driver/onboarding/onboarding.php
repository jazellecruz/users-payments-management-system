<?php 

session_start();

require_once __DIR__ . '/../../../utils/auth.php';

// this onboarding page is only accessible if the user already has an account and is in and has no driver profile yet
if(!(
    isset($_SESSION['userId']) 
    && isset($_SESSION['role']) 
    && $_SESSION['role'] === 'driver' 
    && (!isset($_SESSION['driverId']) || empty($_SESSION['driverId'])))
    ) {
    redirectUser('../auth/sign-up.php');
    exit;
} 

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
    <link rel="stylesheet" type="text/css" href="../../../public/css/driver-onboarding.css">
    <link rel="stylesheet" type="text/css" href="../../../public/css/global.css">
    <title>Driver - Onboarding</title>
</head>
<body>
    <!-- START OF TITLE NAV BAR -->
    <div>
        <div class="nav-title-bar d-flex justify-content-start align-items-center">
            <div class="d-flex flex-row align-items-center">
                <img src="/users-payments-management-system/public/images/image 4.png"  height="30">
                <span class=" fs-5 fw-bold text-brand-primary">Journeolink <span class="text-brand-secondary">Drivers</span></span>
            </div>
        </div>
    </div>
    <!-- END OF TITLE NAV BAR -->

    <!-- START OF MAIN CONTAINER -->
    <div class="d-flex flex-row">
        <div class="flex-grow-1 container p-0 main-content-wrapper">
                <div class=" border-box d-flex flex-column justify-content-center gap-3 form-content-container">
                    <form method="POST" action="../../../api/driver/onboarding.php" class="swiper form-swiper" id="onboardingForm" enctype="multipart/form-data">
                        <input name="user_acc_id" type="text" value="<?php echo $_SESSION['userId'] ?>" hidden>
                        <input name="action" type="text" value="driver_onboarding" hidden>
                        <div class="swiper-wrapper">
                            <div class="swiper-slide d-flex flex-column gap-3">
                                <div class="pt-4">
                                    <p class="fs-2 fw-bold text-brand-primary">Personal Information</p>
                                    <p class="fs-6 text-secondary">Fill in your accurate and valid details to ensure a smooth onboarding experience. </p>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label  for="first_name" class="form-label fw-bold">First Name</label>
                                        <input name="first_name" type="text" class="form-control form-control-sm custom-input data-input required" id="first_name" data-target="first_name_review"  placeholder="Enter your first name">
                                    </div>
                                    <div class="w-100">
                                        <label for="last_name" class="form-label fw-bold">Last Name</label>
                                        <input name="last_name" type="text" class="form-control form-control-sm custom-input data-input required" id="last_name" data-target="last_name_review" placeholder="Enter your last name">
                                    </div>
                                    <div class="w-100">
                                        <label for="middle_name" class="form-label fw-bold">Middle Name</label>
                                        <input name="middle_name" type="text" class="form-control form-control-sm custom-input data-input required" id="middle_name" data-target="middle_name_review" placeholder="Enter your middle name">
                                    </div>
                                    <div class="w-100">
                                        <label for="ext_name" class="form-label fw-bold">Ext</label>
                                        <input name="ext_name" type="text" class="form-control form-control-sm custom-input data-input " id="ext_name" data-target="ext_name_review" placeholder="Ex. Jr., Sr. III">
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="birthdate" class="form-label fw-bold">Birthdate</label>
                                        <input name="birthdate" type="date" class="form-control form-control-sm custom-input data-input required" id="birthdate" data-target="birthdate_review" placeholder="Enter your birthdate">
                                    </div>
                                    <div class="w-100">
                                        <label for="gender" class="form-label fw-bold">Gender</label>
                                        <select name="gender" class="form-select form-select-sm custom-input data-input required" id="gender" data-target="gender_review">
                                            <option selected disabled>Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label for="address" class="form-label fw-bold">Address</label>
                                    <input name="address" type="text" class="form-control form-control-sm custom-input data-input required" id="address" data-target="address_review" placeholder="Enter your address">
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="contact_number" class="form-label mb-1 fw-bold">Contact Number</label>
                                        <span class="text-muted d-block mb-2">Your contact number must be active.</span>
                                        <input name="contact_number" type="text" class="form-control form-control-sm custom-input data-input required" id="contact_number" data-target="contact_number_review" placeholder="Enter your contact number">
                                    </div>
                                    <div class="w-100">
                                        <label for="email" class="form-label mb-1 fw-bold">Alternative Email</label>
                                        <span class="text-muted d-block mb-2">Your alternative email must be active.</span>
                                        <input name="alt_email" type="text" class="form-control form-control-sm custom-input data-input required" id="alt_email" data-target="alt_email_review" placeholder="Enter your alternative email">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start mt-4 pb-4">
                                    <button type="button" class="btn btn-sm bg-brand-primary btn-brand-primary text-white px-4 py-2" onclick="swiper.slideNext()">
                                        Next
                                        <i class="bi bi-arrow-right"></i>  
                                    </button>
                                </div>
                            </div>
                            <div class="swiper-slide d-flex flex-column gap-3">
                                <div class="pt-4">
                                    <p class="fs-2 fw-bold text-brand-primary">Almost Done – Upload Your Files</p>
                                    <p class="fs-6 text-secondary">Submit your driver’s license and supporting documents for verification.</p>
                                </div>
                                <div>
                                    <label class="form-label fw-bold d-block">License</label>
                                    <div class="d-flex flex-column flex-md-row gap-3">
                                        <div class="w-100">
                                            <label for="license_num" class="form-label">Driver's License Number</label>
                                            <input name="license_num" type="text" class="form-control form-control-sm custom-input data-input required" id="license_num" data-target="license_number_review" placeholder="Enter your license number">
                                        </div>
                                        <div class="w-100">
                                            <label for="license_expiry_date" class="form-label">Driver's License Expiry Date</label>
                                            <input name="license_expiry_date" type="date" class="form-control form-control-sm custom-input data-input required" id="license_expiry_date" data-target="license_expiry_date_review" placeholder="Enter your license expiry date">
                                        </div>
                                    </div>
                                    <div class="w-100 mt-3">
                                        <label for="license_photo" class="form-label">Driver's License Photo</label>
                                        <input value="nbi_clearance" name="license_photo" type="file" class="form-control form-control-sm custom-input data-input required" id="license_photo" data-target="license_photo_review" placeholder="Enter your address">
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label for="nbi_clearance" class="form-label fw-bold">NBI Clearance Photo</label>
                                    <input name="nbi_clearance" type="file" class="form-control form-control-sm custom-input data-input required" id="nbi_clearance_proof" data-target="nbi_clearance_photo_review" placeholder="Enter your address">
                                </div>
                                <div class="w-100">
                                    <label for="address_proof_photo" class="form-label fw-bold">Proof of Address Photo</label>
                                    <span class="text-muted d-block mb-2">Must match with the current address you provided.</span>
                                    <input name="address_proof_photo" type="file" class="form-control form-control-sm custom-input data-input required" id="address_proof_photo" data-target="address_proof_photo_review" placeholder="Enter your address">
                                </div>
                                <div class="w-100">
                                    <label for="id_picture" class="form-label fw-bold">2x2 ID Picture</label>
                                    <span class="text-muted d-block mb-2">Make sure your face is clearly visible, with no filters or accessories, a plain white background</span>
                                    <input name="id_picture" type="file" class="form-control form-control-sm custom-input data-input required" id="id_picture" data-target="id_picture_review" placeholder="Enter your address">
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="terms_conds_checkbox" class="form-check-input data-input required" type="checkbox" id="terms_conds_checkbox" data-target="terms_conds_checkbox_review">
                                    <label class="form-check-label" for="terms_conds_checkbox">I accept the 
                                        <a class="text-underline text-brand-secondary pe-auto" data-bs-toggle="modal" data-bs-target="#terms-conditions-modal">Terms and Conditions</a> 
                                        and consent to the use of my data for verification purposes.</label>
                                </div>
                                <div class="d-flex justify-content-start gap-3 pt-3 pb-4">
                                    <button type="button" class="btn btn-sm btn-secondary text-white px-4 py-2" onclick="swiper.slidePrev()">
                                        Prev
                                        <i class="bi bi-arrow-left"></i>  
                                    </button>
                                    <button type="button" class="btn btn-sm bg-brand-primary btn-brand-primary text-white px-4 py-2" onclick="swiper.slideNext()">
                                        Next
                                        <i class="bi bi-arrow-right"></i>  
                                    </button>
                                </div>
                            </div>      
                            <div class="swiper-slide d-flex flex-column gap-3">
                                <div class="pt-4">
                                    <p class="fs-2 fw-bold text-brand-primary">One Last Step – Review Your Application</p>
                                    <p class="fs-6 text-secondary">Make sure your information is correct. You won’t be able to edit after submission.</p>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="first_name_review" class="form-label fw-bold">First Name</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="first_name_review" readonly >
                                    </div>
                                    <div class="w-100">
                                        <label for="last_name_review" class="form-label fw-bold">Last Name</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="last_name_review" data-target="last_name" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="middle_name_review" class="form-label fw-bold">Middle Name</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="middle_name_review" data-target="middle_name" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="ext_name_review" class="form-label fw-bold">Ext</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="ext_name_review" data-target="ext_name" readonly>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="birthdate_review" class="form-label fw-bold">Birthdate</label>
                                        <input type="date" class="form-control form-control-sm custom-input input-review" id="birthdate_review" data-target="birthdate" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="gender_review" class="form-label fw-bold">Gender</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="gender_review" data-target="gender" readonly>
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label for="address_proof_review" class="form-label fw-bold">Address</label>
                                    <input type="text" class="form-control form-control-sm custom-input input-review" id="address_review" data-target="address" readonly>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="contact_number_review" class="form-label mb-1 fw-bold">Contact Number</label>
                                        <span class="text-muted d-block mb-2">Your contact number must be active.</span>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="contact_number_review" data-target="contact_number" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="alt_email_review" class="form-label mb-1 fw-bold">Alternative Email</label>
                                        <span class="text-muted d-block mb-2">Your alternative email must be active.</span>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="alt_email_review" data-target="email" readonly>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label fw-bold d-block">License</label>
                                    <div class="d-flex flex-column flex-md-row gap-3">
                                        <div class="w-100">
                                            <label for="license_number_review" class="form-label">License Number</label>
                                            <input type="text" class="form-control form-control-sm custom-input input-review" id="license_number_review" data-target="license_number" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="license_expiry_date_review" class="form-label">License Expiry Date</label>
                                            <input type="date" class="form-control form-control-sm custom-input input-review" id="license_expiry_date_review" data-target="license_expiry_date" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="license_photo_review" class="form-label">License Photo</label>
                                            <input type="text" class="form-control form-control-sm custom-input input-review" id="license_photo_review" data-target="license_photo" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="nbi_clearance_photo_review" class="form-label fw-bold">NBI Clearance Photo</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="nbi_clearance_photo_review" data-target="nbi_clearance" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="address_proof_photo_review" class="form-label fw-bold">Proof of Address Photo</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="address_proof_photo_review" data-target="address_photo" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="id_picture_review" class="form-label fw-bold">2x2 ID Picture</label>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="id_picture_review" data-target="id_picture" readonly>
                                    </div>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="terms_conds_checkbox_review" data-target="terms_conds_checkbox" disabled>
                                    <span class="" for="terms_conds_checkbox_review">I accept the 
                                        <a class="text-underline text-brand-secondary pe-auto" data-bs-toggle="modal" data-bs-target="#terms-conditions-modal">Terms and Conditions</a> 
                                        and consent to the use of my data for verification purposes.</span>
                                </div>
                                <div class="d-flex justify-content-start gap-3 pt-3 pb-4">
                                    <button type="button" class="btn btn-sm btn-secondary text-white px-4 py-2" onclick="swiper.slidePrev()">
                                        Prev
                                        <i class="bi bi-arrow-left"></i>  
                                    </button>
                                    <button type="button" class="btn btn-brand-primary bg-brand-primary px-4 py-2 text-white" id="submitBtn" disabled>Submit</button>
                                </div>
                            </div>      
                        </div>
                    </form>
                </div>
            </div>
        <div class="d-none d-md-block side-photo-container " style=""></div>
    </div>
    <!-- END OF MAIN CONTAINER -->

    <!-- START OF TERMS AND CONDITIONS MODAL -->
    <div class="modal fade" id="terms-conditions-modal" tabindex="-1" aria-labelledby="terms-conditions-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Terms and Conditions</h1>
                    <button type="button" class="btn btn-sm-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>By submitting this form, you agree to the collection, storage, and processing of the personal information and documents you provide, including but not limited to your name, contact details, government-issued IDs, and proof of address. This information will be used <strong>solely for the purpose of verifying your identity and eligibility</strong> to join the Journeolink Drivers platform.</p>
                    <h6 class="mt-3">How we handle your information</h6>
                    <ul>
                        <li><strong>Secure storage:</strong> Your data is stored securely and access is limited to authorized personnel involved in verification.</li>
                        <li><strong>Purpose limitation:</strong> Collected data is used only for identity verification and onboarding.</li>
                        <li><strong>Retention:</strong> We retain data only as long as necessary to complete verification or as required by law.</li>
                        <li><strong>No unauthorized sharing:</strong> We do not share your verification documents with third parties except where required by law or authorized by you.</li>
                    </ul>
                    <h6 class="mt-3">Your rights</h6>
                    <p>You may withdraw your consent or request deletion of your data after submission by contacting our support team at <a href="mailto:support@journeolink.com">support@journeolink.com</a>. Please note that withdrawing consent may prevent completion of verification and onboarding.</p>
                    <hr>
                    <p class="small text-muted mb-0">By accepting, you confirm that the information and documents you provide are accurate and that you consent to the processing described above.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END OF TERMS AND CONDITIONS MODAL -->

    <!-- START OF ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <p class="modal-title fs-6 text-white" id="errorModalTitle"></p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorModalMessage" class="fs-6"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END OF ERROR MODAL -->
     
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="../../../public/js/driver-onboarding.js"></script>
</body>
</html>

