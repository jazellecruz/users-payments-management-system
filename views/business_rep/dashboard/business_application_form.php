<?php
    session_start();
    
    require_once __DIR__ . '/../../../db/db_conn.php';
    require_once __DIR__ . '/../../../utils/utils.php';
    require_once __DIR__ . '/../../../utils/auth.php';
    require_once __DIR__ . '/../../../queries/business.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bus_rep') {
        redirectUser('../auth/business_rep_login.php');
        exit();
    }

    $conn = getDbConnection();
    $businessRepId = null;
    $businessRep = null;

    if(isset($_SESSION['business_rep_id'])) {
        $businessRepId = $_SESSION['business_rep_id'];
    } else {
        $businessRep = getBusinessRepByUserId($conn, $_SESSION['user_id']);

        if(!$businessRep) {
            $businessRepId = null;
        } else {            
            $businessRepId = $businessRep['business_rep_id'];
        }
    }

    $businessTypes = getAllBusinessTypes($conn);
    $businessRoles = getAllBusinessRoles($conn);
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../../../public/css/global.css">
    <style>
        body {
            background-color: #f3f4f5ff;
        }

        .form-label{
            font-size: 13px;
        }

        input[readonly] {
            background-color: #f1f5f9ff;

        }

        /* #map-msg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: opacity 0.2s ease-in-out;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        #map-msg-overlay {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            color: #555;
        } */

        .preview-upload-item {
            position: relative;
            height: 250px;
            border: 2px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .preview-upload-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-upload-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            cursor: pointer;
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
                <?php if(empty($businessRepId)) { ?>
                    <div class="">
                        <div class="d-flex flex-column justify-content-center align-items-center py-5 px-md-4">
                            <i class="bi bi-person-fill-slash text-muted h1"></i>
                            <h5 class="fw-bold  text-brand-primary mt-3">No Businesses Representative Profile Found</h5>
                            <p class="small text-muted text-center">A business representative profile is required to apply your business.</p>
                            <a class="btn btn-brand-primary bg-brand-primary text-white" href="representative_form.php">Apply Now</a>
                        </div>
                    </div> 
                <?php return; } ?>
                <div class="">
                    <h3 class="text-brand-primary fw-bold pb-2">Business Application Form</h3>
                    <p class="text-muted lh-md subtitle small">Complete the following fields to register your business to the Journeolink Platform. Once approved, your business will be visible to potential clients and partners.</p>
                </div>
                <div class="bg-white p-4 rounded-3 border border-light-gray">
                    <form action="" method="POST" class="needs-validation" id="business-app-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_application">
                        <input type="hidden" name="business_rep_id" value="<?php echo $businessRepId; ?>">
                        <div class="d-flex flex-column gap-4">
                            <div class="">
                                <p class="fw-bold text-brand-primary mb-2">Business Representative Information</p>
                                <p class="text-secondary small mb-0">Your business representative profile will be automatically linked to this application.</p>
                            </div>
                            <div class="w-100 d-flex flex-column flex-lg-row gap-3">
                                <div class="w-100">
                                    <label class="form-label fw-bold text-muted">Representative Name</label>
                                    <input type="text" class="form-control form-control-sm custom-input data-input" value="<?php echo $businessRep['first_name'] . ' ' . $businessRep['last_name']; ?>" id="" readonly>
                                </div>
                                <div class="w-100">
                                    <label class="form-label fw-bold text-muted">Business Representative ID</label>
                                    <input type="text" class="form-control form-control-sm custom-input data-input" value="<?php echo $businessRep['public_business_rep_id']?>" id="" readonly>
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="bus_rep_role" class="form-label fw-bold text-muted">What's your role in the business? Please choose one below:</label>
                                <select name="bus_rep_role" class="form-select form-select-sm custom-input data-input required" id="bus_rep_role" data-target="bus_rep_role_review" required>
                                    <option selected disabled>Select your role</option>
                                    <?php foreach ($businessRoles as $role): ?>
                                        <option class="role_opt" value="<?php echo $role['business_rep_position_id'] ?>" data-role-code="<?php echo $role['business_rep_code'] ?>"><?php echo $role['business_position_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select your role in the business.
                                </div>
                            </div>
                            <div class="w-100 d-none" id="auth_letter_input_grp">
                                <label for="auth_letter" class="form-label fw-bold text-muted">If you are submitting behalf of the owner, please provide an Authorization Letter.</label>
                                <input name="auth_letter" type="file" class="form-control form-control-sm custom-input data-input" id="auth_letter" data-target="auth_letter_review" placeholder="Upload your authorization letter">
                                <div class="invalid-feedback">
                                    Please upload an authorization letter.
                                </div>
                            </div>
                            <div class="pt-4">
                                <p class="fw-bold text-brand-primary mb-2 ">Business Information</p>
                                <p class="text-secondary small mb-0">Fill out the information below to register your business.</p>
                            </div>
                            <div class="w-100">
                                <label for="bus_name" class="form-label fw-bold text-muted">Business Name</label>
                                <input name="bus_name" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_name" data-target="bus_name_review" placeholder="Enter your business name" required>
                                <div class="invalid-feedback">
                                    Please enter your business name.
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="bus_type" class="form-label fw-bold text-muted">Business Type</label>
                                <select name="bus_type" class="form-select form-select-sm custom-input data-input required" id="bus_type" data-target="bus_type_review" required>
                                    <option class="bus_type_opt" selected disabled>Select business type</option>
                                    <?php foreach ($businessTypes as $type): ?>
                                        <option class="bus_type_opt" value="<?php echo $type['business_type_id'] ?>"><?php echo $type['business_type_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="w-100">
                                <label for="bus_desc" class="form-label fw-bold text-muted">Business Description <span class="text-muted fw-normal">(What does your business do?)</span></label>
                                <textarea name="bus_desc" style="resize: none;" class="form-control form-control-sm custom-input data-input required" id="bus_desc" data-target="bus_desc_review" placeholder="What does your business do?" rows="6" required></textarea>
                                <div class="invalid-feedback">
                                    Please enter a brief description of your business.
                                </div>
                            </div>
                            <div class="w-100">
                                <input type="text" name="bus_longitude" hidden id="bus_longitude">
                                <input type="text" name="bus_latitude" hidden id="bus_latitude">
                                <label for="business_address" class="form-label mb-1 fw-bold text-muted">Business Address</label>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label  for="bus_unit_number" class="form-label text-muted text-muted">Unit Number</label>
                                        <input name="bus_unit_number" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_unit_number_input" data-target="bus_unit_number_review"  placeholder="Enter your unit number" required>
                                        <div class="invalid-feedback">
                                            Please enter your unit number.
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <label  for="bus_street" class="form-label text-muted">Street</label>
                                        <input name="bus_street" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_street_input" data-target="bus_street_review"  placeholder="Enter your street" required>
                                         <div class="invalid-feedback">
                                            Please enter your street.
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <label  for="bus_postal_code" class="form-label text-muted">Postal Code</label>
                                        <input name="bus_postal_code" type="number" class="form-control form-control-sm custom-input data-input required" id="bus_postal_code_input" data-target="bus_postal_code_review"  placeholder="Enter your postal code" required>
                                        <div class="invalid-feedback">
                                            Please enter your postal code.
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3 mt-3">
                                    <div class="w-100">
                                        <label  for="bus_city" class="form-label text-muted">City</label>
                                        <input value="Quezon City" name="bus_city" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_city_input" data-target="bus_city_review" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label  for="bus_province" class="form-label text-muted">Province</label>
                                        <input value="Metro Manila" name="bus_province" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_province_input" data-target="bus_province_review" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label  for="bus_country" class="form-label text-muted">Country</label>
                                        <input value="Philippines" name="bus_country" type="text" class="form-control form-control-sm custom-input data-input required" id="bus_country_input" data-target="bus_country_review" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100">
                                <label class="form-label text-muted">Check the map below to see if the address you entered is correct.</label>
                                <div class="position-relative rounded">
                                    <div id="address-map" class="rounded" style="height: 300px;">
                                    </div>
                                    <!-- <div class="map-msg-overlay rounded" id="map-msg-overlay">
                                        <div>
                                            <div class="spinner-border text-secondary d-inline-block" role="status"></div>
                                            <p class="m-auto">Loading map...</p>
                                        </div>
                                    </div> -->
                                </div>

                            </div>
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="w-100">
                                    <label for="business_contact_num" class="form-label mb-1 fw-bold text-muted">Business Contact Number</label>
                                    <span class="text-muted d-block mb-2 form-label">Business contact number must be active.</span>
                                    <input name="business_contact_num" type="number" class="form-control form-control-sm custom-input data-input required" id="business_contact_num" data-target="business_contact_num_review" placeholder="Enter your business contact number" required>
                                </div>
                                <div class="w-100">
                                    <label for="business_email" class="form-label mb-1 fw-bold text-muted">Business Email</label>
                                    <span class="text-muted d-block mb-2 form-label">Business email must be active.</span>
                                    <input name="business_email" type="email" class="form-control form-control-sm custom-input data-input required" id="business_email" data-target="business_email_review" placeholder="Enter your business email" required>
                                    <div class="invalid-feedback">
                                        Please enter your business email.
                                    </div>
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="business_permit" class="form-label mb-1 fw-bold text-muted">Business Permit</label>
                                <span class="text-muted d-block mb-2 form-label">Business permit must be active.</span>
                                <input name="business_permit" type="file" class="form-control form-control-sm custom-input data-input required" id="business_permit" data-target="business_permit_review" placeholder="Upload your business permit" required>
                                 <div class="invalid-feedback">
                                    Please upload your business permit.
                                </div>
                            </div>
                            <div>
                                <label for="business_permit" class="form-label mb-1 fw-bold text-muted">Is your business currently operating?</label>
                                <div class="d-flex flex-column flex-md-row gap-2 mt-2 form-check pl-0">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input data-input required" type="radio" name="operating_cb" id="operating_cb" data-target="operating_cb_review" data-opposite-target="not_operating_cb_review" value="true" required>
                                        <label class="form-check-label form-label" for="operating_cb">Yes, the business is currently operating.</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input data-input required" type="radio" name="operating_cb" id="not_operating_cb" data-target="not_operating_cb_review" data-opposite-target="operating_cb_review" value="false" required>
                                        <label class="form-check-label form-label" for="not_operating_cb">No, the business is not operating.</label>
                                    </div>
                                </div>    
                                <div class="invalid-feedback">
                                    Please enter your unit number.
                                </div>                  
                            </div>
                            <div class="w-100">
                                <label for="business_photos" class="form-label fw-bold text-muted">Business Photos</label>
                                <input multiple accept="image/*" name="business_photos[]" type="file" class="form-control form-control-sm custom-input data-input required" id="business_photos" data-target="business_photos_review" required>
                                <div class="invalid-feedback">
                                    Please upload your business photos.
                                </div>
                                <div class="invalid-feedback small"></div>
                            </div>
                            <div id="uploadImgsPreviewContainer" class="row">
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input data-input" type="checkbox" name="terms_conds_checkbox" id="terms_conds_checkbox" data-target="terms_conds_checkbox_review" required>
                                <span class="form-label" for="terms_conds_checkbox">I accept the 
                                    <a class="text-underline text-brand-secondary pe-auto" data-bs-toggle="modal" data-bs-target="#terms-conditions-modal">Terms and Conditions</a> 
                                    and consent to the use of my data for verification purposes and to represent as a Business Representative in the Journeolink Business platform.</span>
                            </div>
                            <div class="d-flex justify-content-start">
                                <button type="button" id="submit-btn" class="btn btn-sm px-4 py-2 bg-brand-primary text-white btn-brand-primary d-flex gap-2 align-items-center" disabled>
                                    <div class="spinner-border text-light loading-spinner d-none small" role="status" style="width: 20px; height: 20px;">
                                    </div>
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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

    <!-- User Message Modal -->
    <div class="modal fade" id="userMsgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered z-2">
            <div class="modal-content">
                <div class="modal-header" id="userMsgModalHeader">
                    <p class="modal-title fs-6" id="userMsgTitle"></p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" id="userMsgContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END OF TERMS AND CONDITIONS MODAL -->

    <!-- START OF CONFIRM ACTION MODAL -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-body-tertiary">
                    <p class=" modal-title fs-6 fw-bold">CONFIRM ACTION</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="" id="confirmActionMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmActionModalBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <!-- START OF CONFIRM ACTION MODAL -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../../../public/js/business-dashboard.js"></script>

<script>

    // TO DO: move into a separate js file later
    const repRoleInput = document.getElementById("bus_rep_role");
    const authLetterInputGrp =  document.getElementById("auth_letter_input_grp");
    const busPhotoInput = document.getElementById("business_photos");
    const form = document.getElementById("business-app-form");
    const termsCondsCheckbox = document.getElementById("terms_conds_checkbox");
    const submitBtn = document.getElementById("submit-btn");
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    const addressInputs = [
        document.getElementById("bus_unit_number_input"),
        document.getElementById("bus_street_input"),
        document.getElementById("bus_postal_code_input"),
    ];
    let businessPhotos = [];

    const tempLong = 121.0437; 
    const tempLat = 14.6760;
    const tempZoomLvl = 6;

    let debounceTimer;

    const baseLayer = {
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        options: {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }
    }

    // to do: create a map class to handle map related functions
    // and make it reusable/exportable
    const addressMap = L.map('address-map').setView([tempLat, tempLong], tempZoomLvl);   
    L.tileLayer(baseLayer.url, baseLayer.options).addTo(addressMap);
    let addressMarker;

    function debounce(func, delay) {
    return (...args) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func(...args), delay);
    };
    }

    const getGeoCoordinates = async (address) => {
        const res = await axios({
            method: 'get',
            url: 'http://localhost/users-payments-management-system/api/maps/geolocation.php',
            params: {
                action: 'get-geolocation',
                unitNumber: address.unitNumber,
                street: address.street,
                city: address.city,
                province: address.province,
                postalCode: address.postalCode,
                country: address.country
            }
        });

        return res.data;    
    }

    async function handleAddressChange(map, marker) {
        const lonInput = document.getElementById("bus_longitude");
        const latInput = document.getElementById("bus_latitude");

        const address = {
            unitNumber: document.getElementById("bus_unit_number_input").value,
            street: document.getElementById("bus_street_input").value,
            city: document.getElementById("bus_city_input").value,
            province: document.getElementById("bus_province_input").value,
            postalCode: document.getElementById("bus_postal_code_input").value,
            country: document.getElementById("bus_country_input").value
        }

        const data = await getGeoCoordinates(address);

        if(data.length === 0) {
            console.error("No location found");
            return;
        }

        const lat = parseFloat(data[0].lat);
        const lon = parseFloat(data[0].lon);

        renderAddressOnMap(map, marker, {lat: lat, lon: lon});
        
        lonInput.value = lon;
        latInput.value = lat;
    }

    const renderAddressOnMap = (map, marker, coordinates) => {
        if (marker) map.removeLayer(marker);

        map.setView([coordinates.lat, coordinates.lon], 16);

        if(marker) marker = null;

        marker = L.marker([coordinates.lat, coordinates.lon]).addTo(map);
    }

    for(input of addressInputs) {
        input.addEventListener("input", debounce(() => handleAddressChange(addressMap, addressMarker), 3000));
    }

    repRoleInput.addEventListener("change", (e) => {
        const ownerCode = "OWNER";
        const selectedOptCode = e.target.selectedOptions[0].dataset.roleCode;
        const authLetterInput = document.getElementById("auth_letter");

        authLetterInput.classList.remove("required");
        if(selectedOptCode === ownerCode) {
            authLetterInputGrp.classList.remove("d-none"); // remove any previous d-none class
            authLetterInputGrp.classList.add("d-none");
            authLetterInput.classList.remove("required");
        } else {
            authLetterInputGrp.classList.remove("d-none");
            authLetterInput.classList.add("required");
        }
    });

    const showPreview = () => {
        const uploadImgPreviewContainer = document.getElementById('uploadImgsPreviewContainer');

        uploadImgPreviewContainer.innerHTML = '';

        businessPhotos.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewItemContainer = document.createElement('div');
                previewItemContainer.classList.add('col', 'col-12', 'col-lg-4', 'mb-3');

                const previewItem = document.createElement('div');
                previewItem.classList.add('preview-upload-item');

                const img = document.createElement('img');
                img.src = e.target.result;

                const removeBtn = document.createElement('button');
                removeBtn.classList.add('remove-upload-btn');
                removeBtn.innerHTML = '&times;';
                removeBtn.addEventListener('click', () => removeImage(index));

                // previewItem.classList.add('flex-fill');
                previewItem.appendChild(img);
                previewItem.appendChild(removeBtn);
                previewItemContainer.appendChild(previewItem);
                uploadImgPreviewContainer.appendChild(previewItemContainer);
            };
            reader.readAsDataURL(file);
        });
    }

    const removeImage = (index) => {
        businessPhotos.splice(index, 1);
        updateBusPhotoInput();
        showPreview();
    }

    const updateBusPhotoInput = () => {
        const dataTransfer = new DataTransfer();
        businessPhotos.forEach(file => dataTransfer.items.add(file));
        busPhotoInput.files = dataTransfer.files;
    }

    busPhotoInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        let errDiv = e.target.nextElementSibling;

        while(errDiv && !errDiv.classList.contains('invalid-feedback')) {
            errDiv = errDiv.nextElementSibling;
        }

        errDiv.style.display = 'none';

        if(businessPhotos.length  + files.length > 5) {
            console.log("More than 5 photos");
            errDiv.innerText = "You can upload a maximum of 5 photos only.";
            e.target.value = '';
            errDiv.style.display = 'block';
            return;
        }

        businessPhotos = [...businessPhotos, ...files];
        updateBusPhotoInput();
        showPreview();
    });

    termsCondsCheckbox.addEventListener("change", function () {
        submitBtn.disabled = !this.checked;
    });

    
    const isFormComplete = () => {
        const requiredInputs = form.querySelectorAll(".required");
        let isComplete = true;

        requiredInputs.forEach(input => {
            if(input.type === "radio") {
                const radioGroup = form.querySelectorAll(`input[name="${input.name}"]`);
                const isChecked = Array.from(radioGroup).some(radio => radio.checked);
                if(!isChecked) {
                    isComplete = false;
                }
            } else if(!input.value || (input.type === "file" && input.files.length === 0)) {
                isComplete = false;
            }
        });

        return isComplete;    
    }

    // to do: move to a separate js file later
    const showUserMsgModal = ({ title, content, headerBgColor = 'bg-secondary', titleColor = 'text-dark' }) => {
        const userMsgModal = new bootstrap.Modal(document.getElementById('userMsgModal'));
        const userMsgModalHeader = document.getElementById('userMsgModalHeader');
        const userMsgContent = document.getElementById('userMsgContent');
        const userMsgTitle = document.getElementById('userMsgTitle');
        
        userMsgTitle.innerText = title;
        userMsgContent.innerText = content;
        userMsgModalHeader.classList.add(headerBgColor);
        userMsgTitle.classList.add(titleColor)
        userMsgModal.show();
    }

    const showConfirmModal = (msg) => {
        const confirmModalMsgEl = document.getElementById('confirmActionMessage');
        const confirmActionModalBtn = document.getElementById('confirmActionModalBtn');
        return new Promise((resolve) => {
            confirmModalMsgEl.textContent = msg;
            confirmModal.show();

            const handleConfirm = () => {
                // this is necessary!! clean up first to remove previous listeners
                confirmActionModalBtn.removeEventListener('click', handleConfirm);
                resolve(true);
            };

            confirmActionModalBtn.addEventListener('click', handleConfirm);
        });
    };

    const hideConfirmModal = () => {
        confirmModal.hide();
    };


    submitBtn.addEventListener("click", async function (e) {
        if(!isFormComplete()) {
            return showUserMsgModal({
                title: 'Incomplete Form',
                content: 'Please fill out all fields before submitting the form.',
                headerBgColor: 'bg-danger',
                titleColor: 'text-white'
            });  
        }

        const confirmMsg = "You’re about to submit your business application form. Confirm that all provided details are accurate. Proceed?";
        const confirmed = await showConfirmModal(confirmMsg);

        if (!confirmed) return;
        hideConfirmModal();

        e.target.disabled = true;
        e.target.querySelector('.loading-spinner').classList.remove('d-none');

        const formData = new FormData(form);

        try {
            let res = await axios.post('../../../api/business/application.php', formData);

            if(res.status == 200) {
                showUserMsgModal({
                    title: 'Form Submitted',
                    content: 'Your business application form has been successfully submitted!',
                    headerBgColor: 'bg-success',
                    titleColor: 'text-white'
                });
            }
        } catch(err) {
            showUserMsgModal({
                title: 'Error Submitting Form',
                content: 'Failed to submit the business application form. Please try again later.',
                headerBgColor: 'bg-danger',
                titleColor: 'text-white'
            });  
        } finally {
            e.target.querySelector('.loading-spinner').classList.add('d-none');
            form.reset();
            e.target.disabled = true;
            businessPhotos = [];
            showPreview();
        }
    });

</script>
</body>
</html>