<?php 

session_start();

require_once('../../../db/db_conn.php');
require_once('../../../queries/business.php');
require_once('../../../utils/auth.php');

// require a business rep user to access this page
if(!isset($_SESSION['active_applicant']) && !($_SESSION['active_applicant']['role'] === 'bus_rep')) {
    redirectUser('../../business_rep/auth/sign-up.php');
} 

$conn = getDbConnection();

$businessRoles = getAllBusinessRoles($conn);
$businessTypes = getAllBusinessTypes($conn);

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" type="text/css" href="../../../public/css/global.css">
    <link rel="stylesheet" type="text/css" href="../../../public/css/business-onboarding.css">
    <title>Business - Onboarding</title>
</head>
<body>
    <!-- START OF MAIN CONTAINER -->
    <div class="d-flex flex-row vh-100">
        <div class="d-none d-md-block side-photo-container " style=""></div>
        <!-- START OF TITLE NAV BAR -->
        <div>
            <div class="nav-title-bar d-flex justify-content-start align-items-center">
                <div class="d-flex flex-row align-items-center">
                    <img src="/users-payments-management-system/public/images/image 4.png"  height="30">
                    <span class=" fs-5 fw-bold text-brand-primary">Journeolink <span class="text-brand-secondary">Business</span></span>
                </div>
            </div>
        </div>
        <!-- END OF TITLE NAV BAR -->
        <div class="d-flex flex-column container">
            <div class="form-wrapper px-md-5 " style="">
                <form method="POST" action="../../../api/business/onboarding.php" id="onboardingForm" enctype="multipart/form-data">
                    <input name="user_acc_id" type="text" value="<?php echo $_SESSION['active_applicant']['user_id'] ?>" hidden>
                    <input name="action" type="text" value="business_onboarding" hidden>
                    <div class="swiper vh-100 form-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide px-2 d-flex flex-column gap-3">
                                <div class="pt-4">
                                    <p class="fs-2 fw-bold text-brand-primary">Business Representative Information</p>
                                    <p class="fs-6 text-secondary">We require the representative’s details to verify and validate this business application.</p>
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
                                        <label for="alt_email" class="form-label mb-1 fw-bold">Alternative Email</label>
                                        <span class="text-muted d-block mb-2">Your alternative email must be active.</span>
                                        <input name="alt_email" type="text" class="form-control form-control-sm custom-input data-input required" id="alt_email" data-target="alt_email_review" placeholder="Enter your alternative email">
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label for="bus_rep_role" class="form-label fw-bold">What's your role in the business?</label>
                                    <select name="bus_rep_role" class="form-select form-select-sm custom-input data-input required" id="bus_rep_role" data-target="bus_rep_role_review">
                                        <option selected disabled>Select your role</option>
                                        <?php foreach ($businessRoles as $role): ?>
                                            <option class="role_opt" value="<?= $role['business_rep_position_id'] ?>"><?= $role['business_position_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="w-100 d-none" id="auth_letter_input_grp">
                                    <label for="auth_letter" class="form-label fw-bold">If you are submitting behalf of the owner, please provide an Authorization Letter.</label>
                                    <input name="auth_letter" type="file" class="form-control form-control-sm custom-input data-input" id="auth_letter" data-target="auth_letter_review" placeholder="Upload your authorization letter">
                                </div>
                                <div class="w-100">
                                    <label for="valid_id" class="form-label fw-bold">Any valid government ID</label>
                                    <input name="valid_id" type="file" class="form-control form-control-sm custom-input data-input required" id="valid_id" data-target="valid_id_review" placeholder="Upload your valid government ID">
                                </div>
                                <div class="d-flex justify-content-start mt-4 pb-4">
                                    <button type="button" class="btn btn-sm bg-brand-primary btn-brand-primary text-white px-4 py-2" onclick="swiper.slideNext()">
                                        Next
                                        <i class="bi bi-arrow-right"></i>  
                                    </button>
                                </div>
                            </div>
                            <div class="swiper-slide px-2 d-flex flex-column gap-3">
                                <div class="pt-4">
                                    <p class="fs-2 fw-bold text-brand-primary">Business Information</p>
                                    <p class="fs-6 text-secondary">Please provide the official details of your business to complete the registration process.</p>
                                </div>
                                <div class="w-100">
                                    <label for="bus_name" class="form-label fw-bold">Business Name</label>
                                    <input name="bus_name" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_name" data-target="bus_name_review" placeholder="Enter your business name">
                                </div>
                                <div class="w-100">
                                    <label for="bus_type" class="form-label fw-bold">Business Type</label>
                                    <select name="bus_type" class="form-select form-select-sm custom-input data-input required" id="bus_type" data-target="bus_type_review">
                                        <option class="bus_type_opt" selected disabled>Select business type</option>
                                        <?php foreach ($businessTypes as $type): ?>
                                            <option class="bus_type_opt" value="<?= $type['business_type_id'] ?>"><?= $type['business_type_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="w-100">
                                    <label for="bus_desc" class="form-label fw-bold">Business Description <span class="text-muted fw-normal">(What does your business do?)</span></label>
                                    <textarea name="bus_desc" style="resize: none;" class="form-control form-control-sm custom-input data-input required" id="bus_desc" data-target="bus_desc_review" placeholder="What does your business do?" rows="6"></textarea>
                                </div>
                                <div class="w-100">
                                    <input type="text" name="bus_longitude" hidden id="bus_longitude">
                                    <input type="text" name="bus_latitude" hidden id="bus_latitude">
                                    <label for="business_address" class="form-label mb-1 fw-bold">Business Address</label>
                                    <div class="d-flex flex-column flex-md-row gap-3">
                                        <div class="w-100">
                                            <label  for="bus_unit_number" class="form-label text-muted">Unit Number</label>
                                            <input name="bus_unit_number" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_unit_number" data-target="bus_unit_number_review"  placeholder="Enter your unit number">
                                        </div>
                                        <div class="w-100">
                                            <label  for="bus_street" class="form-label text-muted">Street</label>
                                            <input name="bus_street" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_street" data-target="bus_street_review"  placeholder="Enter your street">
                                        </div>
                                        <div class="w-100">
                                            <label  for="bus_postal_code" class="form-label text-muted">Postal Code</label>
                                            <input name="bus_postal_code" type="number" class="form-control form-control-sm custom-input data-input required" id="bus_postal_code" data-target="bus_postal_code_review"  placeholder="Enter your postal code">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row gap-3 mt-3">
                                        <div class="w-100">
                                            <label  for="bus_city" class="form-label text-muted">City</label>
                                            <input value="Quezon City" name="bus_city" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_city" data-target="bus_city_review" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label  for="bus_province" class="form-label text-muted">Province</label>
                                            <input value="Metro Manila" name="bus_province" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_province" data-target="bus_province_review" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label  for="bus_country" class="form-label text-muted">Country</label>
                                            <input value="Philippines" name="bus_country" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_country" data-target="bus_country_review" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label class="form-label text-muted">Check the map below to see if the address you entered is correct.</label>
                                    <div id="map"></div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="business_contact_num" class="form-label mb-1 fw-bold">Business Contact Number</label>
                                        <span class="text-muted d-block mb-2">Business contact number must be active.</span>
                                        <input name="business_contact_num" type="text" class="form-control form-control-sm custom-input data-input required" id="business_contact_num" data-target="business_contact_num_review" placeholder="Enter your business contact number">
                                    </div>
                                    <div class="w-100">
                                        <label for="business_email" class="form-label mb-1 fw-bold">Business Email</label>
                                        <span class="text-muted d-block mb-2">Business email must be active.</span>
                                        <input name="business_email" type="text" class="form-control form-control-sm custom-input data-input required" id="business_email" data-target="business_email_review" placeholder="Enter your business email">
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label for="business_permit" class="form-label mb-1 fw-bold">Business Permit</label>
                                    <span class="text-muted d-block mb-2">Business permit must be active.</span>
                                    <input name="business_permit" type="file" class="form-control form-control-sm custom-input data-input required" id="business_permit" data-target="business_permit_review" placeholder="Upload your business permit">
                                </div>
                                <div>
                                    <label for="business_permit" class="form-label mb-1 fw-bold">Is your business currently operating?</label>
                                    <div class="d-flex flex-column flex-md-row gap-2 mt-2 form-check pl-0">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input data-input required" type="radio" name="operating_cb" id="operating_cb" data-target="operating_cb_review" data-opposite-target="not_operating_cb_review" value="true">
                                            <label class="form-check-label" for="operating_cb">Yes, the business is currently operating.</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input data-input required" type="radio" name="operating_cb" id="not_operating_cb" data-target="not_operating_cb_review" data-opposite-target="operating_cb_review" value="false">
                                            <label class="form-check-label" for="not_operating_cb">No, the business is not operating.</label>
                                        </div>
                                    </div>                      
                                </div>
                                <div>
                                    <div class="w-100">
                                        <label for="business_photos" class="form-label fw-bold">Business Photos</label>
                                        <input multiple accept="image/*" name="business_photos[]" type="file" class="form-control form-control-sm custom-input data-input required" id="business_photos" data-target="business_photos_review" placeholder="Upload your valid government ID">
                                    </div>
                                </div>
                                <div id="preview" class="preview-container"></div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input data-input" type="checkbox" name="terms_conds_checkbox" id="terms_conds_checkbox" data-target="terms_conds_checkbox_review">
                                    <span class="" for="terms_conds_checkbox">I accept the 
                                        <a class="text-underline text-brand-secondary pe-auto" data-bs-toggle="modal" data-bs-target="#terms-conditions-modal">Terms and Conditions</a> 
                                        and consent to the use of my data for verification purposes.</span>
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
                            <div class="swiper-slide px-2 d-flex flex-column gap-3">
                                <div class="pt-4">
                                    <p class="fs-2 fw-bold text-brand-primary">One Last Step – Review Your Application</p>
                                    <p class="fs-6 text-secondary">Make sure your information is correct. You won’t be able to edit after submission.</p>
                                </div>
                                <p class="fs-5 fw-bold">Business Representative Information</p>
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
                                    <label for="address_review" class="form-label fw-bold">Address</label>
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
                                <div class="w-100">
                                    <label for="bus_rep_role_review" class="form-label fw-bold">What's your role in the business?</label>
                                    <input class="form-control form-control-sm custom-input input-review" id="bus_rep_role_review" data-target="bus_rep_role" readonly>
                                </div>
                                <div class="w-100 d-none" id="auth_letter_review_grp">
                                    <label class="form-label fw-bold">Authorization Letter</label>
                                    <input type="text" class="form-control form-control-sm custom-input input-review" id="auth_letter_review" data-target="auth_letter" readonly>
                                </div>
                                <div class="w-100">
                                    <label for="valid_id_review" class="form-label fw-bold">Any valid government ID</label>
                                    <input type="text" class="form-control form-control-sm custom-input input-reiew" id="valid_id_review" data-target="valid_id" readonly>
                                </div>
                                <p class="fs-5 fw-bold mt-4">Business Information</p>
                                <div class="w-100">
                                    <label for="bus_name_review" class="form-label fw-bold">Business Name</label>
                                    <input type="text" class="form-control form-control-sm custom-input input-review" id="bus_name_review" data-target="bus_name" readonly>
                                </div>
                                <div class="w-100">
                                    <label for="bus_type_review" class="form-label fw-bold">Business Type</label>
                                    <input class="form-control form-control-sm custom-input input-review" id="bus_type_review" data-target="bus_type" readonly>
                                </div>
                                <div class="w-100">
                                    <label for="bus_desc_review" class="form-label fw-bold">Business Description <span class="text-muted fw-normal">(What does your business do?)</span></label>
                                    <textarea style="resize: none;" class="form-control form-control-sm custom-input input-review" id="bus_desc_review" data-target="bus_desc" rows="6" readonly></textarea>
                                </div>
                                <div class="w-100">
                                    <label for="business_address" class="form-label mb-1 fw-bold">Business Address</label>
                                    <div class="d-flex flex-column flex-md-row gap-3">
                                        <div class="w-100">
                                            <label  for="bus_unit_number_review" class="form-label text-muted">Unit Number</label>
                                            <input type="text" class="form-control form-control-sm custom-input input-review" id="bus_unit_number_review" data-target="bus_unit_number" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_street_review" class="form-label text-muted">Street</label>
                                            <input type="text" class="form-control form-control-sm custom-input input-review" id="bus_street_review" data-target="bus_street" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_postal_code_review" class="form-label text-muted">Postal Code</label>
                                            <input type="number" class="form-control form-control-sm custom-input input-review" id="bus_postal_code_review" data-target="bus_postal_code" readonly>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row gap-3 mt-3">
                                        <div class="w-100">
                                            <label for="bus_city_review" class="form-label text-muted">City</label>
                                            <input value="Quezon City" type="text" class="form-control form-control-sm custom-input input-review" id="bus_city_review" data-target="bus_city" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_province_review" class="form-label text-muted">Province</label>
                                            <input value="Metro Manila" type="text" class="form-control form-control-sm custom-input input-review" id="bus_province_review" data-target="bus_province" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_country_review" class="form-label text-muted">Country</label>
                                            <input value="Philippines" type="text" class="form-control form-control-sm custom-input input-review" id="bus_country_review" data-target="bus_country" readonly>
                                        </div>
                                    </div>
                                    <div class="w-100">
                                         <div class="mt-3" id="map-review"></div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label for="business_contact_num_review" class="form-label mb-1 fw-bold">Business Contact Number</label>
                                        <span class="text-muted d-block mb-2">Business contact number must be active.</span>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="business_contact_num_review" data-target="business_contact_num" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="business_email_review" class="form-label mb-1 fw-bold">Business Email</label>
                                        <span class="text-muted d-block mb-2">Business email must be active.</span>
                                        <input type="text" class="form-control form-control-sm custom-input input-review" id="business_email_review" data-target="business_email" readonly>
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label for="business_permit_review" class="form-label mb-1 fw-bold">Business Permit</label>
                                    <input type="text" class="form-control form-control-sm custom-input input-review" id="business_permit_review" data-target="business_permit" readonly>
                                </div>
                                <div class="w-100 mb-0">
                                    <label for="business_photos_review" class="form-label fw-bold">Business Photos</label>
                                    <div id="bus-photo-review-container" class="bus-photo-review-container mb-2"></div>
                                </div>
                                <div>
                                    <label for="business_permit" class="form-label mb-1 fw-bold">Is your business currently operating?</label>
                                    <div class="d-flex flex-column flex-md-row gap-2 mt-2 pl-0">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input input-review" type="radio" id="operating_cb_review" data-target="operating_cb" readonly disabled>
                                            <span class="" for="operating_cb_review">Yes, the business is currently operating.</span>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input input-review" type="radio" id="not_operating_cb_review" readonly disabled>
                                            <span class="" for="not_operating_cb_review">No, the business is not operating.</span>
                                        </div>
                                    </div>                      
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input input-review" type="checkbox" id="terms_conds_checkbox_review" data-target="terms_conds_checkbox" disabled>
                                    <span class="" for="terms_conds_checkbox_review">I accept the 
                                        <a class="text-underline text-brand-secondary pe-auto" data-bs-toggle="modal" data-bs-target="#terms-conditions-modal">Terms and Conditions</a> 
                                        and consent to the use of my data for verification purposes.</span>
                                </div>
                                <div class="d-flex justify-content-start gap-3 pt-3 pb-4">
                                    <button type="button" class="btn btn-sm btn-secondary text-white px-4 py-2" onclick="swiper.slidePrev()">
                                        Prev
                                        <i class="bi bi-arrow-left"></i>  
                                    </button>
                                    <button type="button" class="btn btn-brand-primary bg-brand-primary px-4 py-2 text-white d-flex flex-row gap-1 align-items-center" id="submitBtn" disabled>
                                        <img src="../../../public/svg/loading.svg" class="d-none" id="loadingSpinner" alt="" style="width: 20px;">
                                        <span>Submit</span>
                                    </button>
                                </div>
                            </div>      
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                    <p>By submitting this form, you agree to the collection, storage, and processing of the personal information and documents you provide, including but not limited to your name, contact details, government-issued IDs and documents. This information will be used <strong>solely for the purpose of verifying your identity and eligibility</strong> to join the Journeolink Business platform.</p>
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="../../../public/js/business-onboarding.js"></script>
</body>
</html>

