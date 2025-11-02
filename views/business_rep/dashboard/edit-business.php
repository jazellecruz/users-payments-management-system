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

    $businessId = null;
    $publicId = null;
    $businessInfo = null;
    $businessPhotos = null;

    $conn = getDbConnection();

    if(isset($_GET['id'])) {
        $publicId = $_GET['id'];
        $businessId = getBusinessIdByPublicId($conn, $publicId);
        $businessInfo = getBusinessById($conn, $businessId);
        $businessPhotos = getBusinessPhotosByBusinessId($conn, $businessId);
    } 

    $businessTypes = getAllBusinessTypes($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Business Dashboard - Edit Business</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
            <div class="container">
                <div class="d-flex flex-column gap-3 py-4 px-md-4">
                    <div class="bg-white p-3 rounded border border-light-gray">
                        <div class="avt-img-container">
                            <img class="rounded" src="<?php echo $businessInfo['business_cover_img_url'] ? $businessInfo['business_cover_img_url'] : "https://www.ufwc.co.uk/images/no-img-placeholder.png"?>" alt=""  srcset="" style="width: 100%; height: 300px; object-fit: cover;">
                            <div class="cover-img-edit-overlay">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editBusinessCoverModal">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </button> 
                            </div> 
                        </div>
                        <div class="d-flex gap-4 align-items-center flex-column flex-md-row mt-3">
                            <div class="avt-img-container" style="margin-top: -40px; margin-left: 20px">
                                <img class="rounded rounded-circle" src="<?php echo $businessInfo['business_profile_img'] ? $businessInfo['business_profile_img'] : "https://www.ufwc.co.uk/images/no-img-placeholder.png"?>" alt=""  srcset=""                            
                                    width="160" 
                                    height="160" 
                                    style="object-fit: cover; border: 6px solid white;
                                    "
                                >
                                <div class="edit-avatar-overlay rounded-circle">
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editBusinessProfileModal">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </button>
                                </div> 
                            </div>

                             <div class="d-flex flex-column justify-content-center gap-2">
                                <div class="d-flex gap-2 align-items-center">
                                    <p class="fw-bold h4 text-brand-primary mb-0"><?php echo $businessInfo['business_name']; ?></>
                                    <div class="badge-base d-flex flex-row align-items-center gap-1 rounded-pill align-self-center <?php echo !$businessInfo['is_disabled'] ? "badge-approved" : "badge-rejected"; ?>" style="height: 24px;">
                                        <i class="bi bi-circle-fill"></i>
                                        <span class=""><?php echo ucfirst(!$businessInfo['is_disabled'] ? "active" : "disabled"); ?></span>
                                    </div>
                                </div>
                                <p class="text-muted normal mb-0 truncate"><?php echo $businessInfo['business_desc']; ?></p>
                                <p class="small text-muted mb-0"><i class="bi bi-geo-alt-fill pe-1 text-danger"></i><?php echo $businessInfo['business_unit_number'] ." " . $businessInfo['business_street'] . ", " . $businessInfo['business_city']?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded border border-light-gray">
                        <div class="p-3 border-bottom border-light-gray">
                            <p class="fw-bold fs-5 text-brand-primary mb-0 pb-0">Business Information</p>
                        </div>
                        <form action="../../../api/business/business.php" method="post" id="editBusInfoForm">
                            <input type="hidden" name="action" value="edit_business_info">    
                            <input type="hidden" name="public_business_id" value="<?php echo $businessInfo['public_business_id']; ?>">     
                            <div class="p-3 d-flex flex-column gap-3">
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label class="form-label text-muted">Business Name</label>
                                        <input name="business_name" class="form-control form-control-sm" type="text" value="<?php echo $businessInfo['business_name']; ?>">
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Business Type</label>
                                        <select name="business_type" class="form-select form-select-sm" aria-label="business type select">
                                            <?php foreach ($businessTypes as $type): ?>
                                                <option 
                                                    value="<?php echo $type['business_type_id']; ?>" 
                                                    <?php echo $type['business_type_name'] == $businessInfo['business_type_name'] ? 'selected' : ''; ?>
                                                >
                                                <?php echo $type['business_type_name']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label for="business_desc" class="form-label text-muted">Business Description</label>
                                    <textarea name="business_desc" id="business_desc" class="form-control form-control-sm" rows="3"><?php echo $businessInfo['business_desc']; ?></textarea>
                                </div>
                                <div class="w-100 pb-2">
                                    <label for="business_address" class="form-label text-muted">Business Address</label>
                                    <div class="d-flex flex-column flex-md-row gap-3">
                                        <div class="w-100">
                                            <label for="bus_unit_number_input" class="form-label text-muted">Unit Number</label>
                                            <input name="business_unit_number" type="text" class="form-control form-control-sm address-input" id="bus_unit_number_input" value="<?php echo $businessInfo['business_unit_number']; ?>">
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_street_input" class="form-label text-muted">Street</label>
                                            <input name="business_street" type="text" class="form-control form-control-sm address-input" id="bus_street_input" value="<?php echo $businessInfo['business_street']; ?>">
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_postal_code_input" class="form-label text-muted">Postal Code</label>
                                            <input name="business_postal_code" type="number" class="form-control form-control-sm address-input" id="bus_postal_code_input" value="<?php echo $businessInfo['business_postal_code']; ?>">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row gap-3 mt-3 pb-2">
                                        <div class="w-100">
                                            <label for="bus_city_input" class="form-label text-muted">City</label>
                                            <input name="business_city" value="Quezon City" type="text" class="form-control form-control-sm address-input" id="bus_city_input" data-target="bus_city" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_province_input" class="form-label text-muted">Province</label>
                                            <input name="business_province" value="Metro Manila" type="text" class="form-control form-control-sm address-input" id="bus_province_input" data-target="bus_province" readonly>
                                        </div>
                                        <div class="w-100">
                                            <label for="bus_country_input" class="form-label text-muted">Country</label>
                                            <input name="business_country" value="Philippines" type="text" class="form-control form-control-sm address-input" id="bus_country_input" data-target="bus_country" readonly>
                                        </div>
                                    </div>
                                    <div class="map-wrapper pt-1">
                                        <input type="text" hidden 
                                            id="loc_lat" 
                                            value="<?php echo $businessInfo['loc_lat']; ?>" 
                                            name="loc_lat"
                                            readonly
                                        >
                                        <input type="text" hidden 
                                            id="loc_long" 
                                            value="<?php echo $businessInfo['loc_long']; ?>" 
                                            readonly
                                            name="loc_long"
                                        >
                                        <div class=" w-100 w-md-75 rounded" id="address-review-map" style="height: 250px;"></div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row gap-3 mt-3 pb-2">
                                        <div class="w-100">
                                            <label class="form-label text-muted">Business Contact Number</label>
                                            <input name="business_contact_num" value="<?php echo $businessInfo['business_contact_num']; ?>" type="text" class="form-control form-control-sm">
                                        </div>
                                        <div class="w-100">
                                            <label class="form-label text-muted">Business Email</label>
                                            <input name="business_email" value="<?php echo $businessInfo['business_email']; ?>" type="text" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="w-100 pb-2">
                                        <div class="d-flex align-items-center justify-content-start gap-2 mb-2">
                                            <label class="form-label text-muted pb-0 mb-0">Is business operating?</label>
                                            <button type="button" class="bg-transparent border-0 p-0 m-0" data-bs-toggle="popover" data-bs-title="Operation Status" data-bs-content="<?php echo $infoOperationStatMsg; ?>" data-bs-placement="top">
                                                <i class="bi bi-info-circle text-muted small info-popover-icon fw-bold"></i>
                                            </button>
                                        </div>
                                        <div class="d-flex flex-column flex-lg-row gap-3">
                                            <div class="form-check">
                                                <input 
                                                    name="is_operating"
                                                    class="form-check-input" 
                                                    value="true"
                                                    type="radio"
                                                    id="defaultCheck1" 
                                                    <?php echo $businessInfo['is_operating'] ? 'checked' : ''; ?>
                                                    >
                                                <span  class="small">
                                                    Yes, the business is currently operating.
                                                </span>
                                            </div>
                                            <div class="form-check">
                                                <input 
                                                    name="is_operating"
                                                    class="form-check-input" 
                                                    value="false"
                                                    type="radio" 
                                                    id="defaultCheck2"
                                                    <?php echo !$businessInfo['is_operating'] ? 'checked' : ''; ?>
                                                >
                                                <span class="small">
                                                    No, the business is not currently operating.
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 border-top border-light-gray mt-2">
                                <div class=" d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-secondary text-white">Cancel</button>
                                    <button type="button" id="saveBusInfoEditBtn" class="btn btn-sm btn-brand-primary text-white bg-brand-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bg-white rounded border border-light-gray">
                        <div class="p-3 border-bottom border-light-gray d-flex justify-content-between align-items-center">
                            <p class="fw-bold fs-5 text-brand-primary mb-0 pb-0">Business Photos</p>
                            <div class=" d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#deletePhotosModal">Remove Photos</button>
                                <button type="button" class="btn btn-sm btn-brand-primary text-white bg-brand-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">Add Photo</button>
                            </div>
                        </div>
                        <div class="w-100 p-3">
                            <div>
                                <div class="row">
                                    <?php 
                                        foreach ($businessPhotos as $photo): ?>
                                            <div class="col-12  col-lg-4 mb-3">
                                                <div class="thumbnail w-100" style="height: 250px;">
                                                    <img src="<?php echo $photo["photo_url"]; ?>" alt="Business Photo" class="object-fit-cover rounded mb-2 business-img" style="width: 100%; object-fit: cover; height: 100%;">
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit business profile modal -->
    <div class="modal fade" id="editBusinessProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../../../api/business/business.php" id="editProfileForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                <input type="hidden" name="business_id" value="<?php echo $businessInfo['business_id']; ?>">
                <input type="text" name="action" value="edit_business_profile_img" hidden>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="editProfileModalLabel">Edit Business Profile Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <?php 
                            $lastSlashPos = strrchr($businessInfo['business_profile_img'], '/'); 
                            $imgFileName = substr($lastSlashPos, 1);
                        ?>
                        <img 
                            src="<?php echo $businessInfo['business_profile_img'] ? $businessInfo['business_profile_img'] : 'https://www.ufwc.co.uk/images/no-img-placeholder.png'; ?>" 
                            class="rounded-circle border avatar mx-auto mb-3" 
                            width="160" 
                            height="160" 
                            style="object-fit: cover;"
                            id="previewBusinessProfileImgEdit"
                        >
                        <label for="business_profile_image" class="form-label">Business Profile Image</label>
                        <input class="form-control form-control-sm img-input" data-target="previewBusinessProfileImgEdit" type="file" name="business_profile_image" id="business_profile_image" 
                            value=""
                            placeholder="<?php echo $imgFileName; ?>"
                        >
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveProfileEditBtn" class="btn btn-brand-primary bg-brand-primary text-white btn-sm">
                            <img src="../../../public/svg/loading.svg" class="d-none loading-spinner" id="editCoverLoadingSpinner" alt="" style="width: 20px;">
                            <span>Save</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit business cover photo modal -->
    <div class="modal fade" id="editBusinessCoverModal" tabindex="-1" aria-labelledby="editCoverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../../../api/business/business.php" id="editCoverForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                <input type="hidden" name="business_id" value="<?php echo $businessInfo['business_id']; ?>">
                <input type="text" name="action" value="edit_business_cover_img" hidden>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="editCoverModalLabel">Edit Business Cover Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <?php 
                            $lastSlashPos = strrchr($businessInfo['business_cover_img_url'], '/'); 
                            $imgFileName = substr($lastSlashPos, 1);
                        ?>
                        <img 
                            src="<?php echo $businessInfo['business_cover_img_url'] ? $businessInfo['business_cover_img_url'] : 'https://www.ufwc.co.uk/images/no-img-placeholder.png'; ?>" 
                            class="w-100 border rounded mb-3"
                            height="300"
                            style="object-fit: cover;"
                            id="previewCoverProfileImgEdit"
                        >
                        <label for="business_cover_image" class="form-label">Business Cover Image</label>
                        <input class="form-control form-control-sm img-input" data-target="previewCoverProfileImgEdit" type="file" name="business_cover_image" id="business_cover_image" 
                            value=""
                            placeholder="<?php echo $imgFileName; ?>"
                        >
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCoverEditBtn" class="btn btn-brand-primary bg-brand-primary text-white btn-sm">
                            <img src="../../../public/svg/loading.svg" class="d-none loading-spinner" id="editCoverLoadingSpinner" alt="" style="width: 20px;">
                            <span>Save</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm action modal -->
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

    <!-- User Message Modal -->
    <div class="modal fade" id="userMsgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered z-2">
            <div class="modal-content">
                <div class="modal-header" id="userMsgModalHeader">
                    <p class=" modal-title fs-6 fw-bold" id="userMsgTitle"></p>
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

    <!-- Delete photos Modal -->
    <div class="modal fade" id="deletePhotosModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header bg-body-tertiary">
                <h1 class="modal-title fs-6">Delete Photos</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Click the photos you want to be deleted below to highlight and delete them from your business profile.</p>
                <form id="deletePhotosForm" action="../../../api/business/business.php" method="post">
                    <input type="hidden" name="business_id" value="<?php echo $businessInfo['business_id']; ?>">
                    <input type="hidden" name="action" value="delete_business_imgs">
                    <div class="row">
                        <?php foreach ($businessPhotos as $photo): ?>
                            <div class="col-12 col col-lg-4 mb-3">
                                <div class="thumbnail position-relative image-container" style="height: 250px;">
                                    <input hidden name="photosToDelete[]" value="<?php echo $photo["business_photo_id"]; ?>" type="checkbox" class="position-absolute form-check-input checkbox-lg imgToDelCheckbox" style="top: 10px; left: 16px; z-index: 10;">
                                    <div class="overlay w-100 h-100 rounded position-absolute checked-img-overlay"></div>
                                    <button type="button" class="w-100 h-100 border-0 bg-transparent position-absolute z-2 toggleCheckImgBtn" data-checkbox-target="" data-overlay-target=""></button>
                                    <img src="<?php echo $photo["photo_url"]; ?>" alt="Business Photo" class="object-fit-cover rounded mb-2 cursor-pointer h-100 w-100" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="deletePhotosBtn" class="btn btn-danger">
                    <img src="../../../public/svg/loading.svg" class="d-none" id="delLoadingSpinner" alt="" style="width: 20px;">
                    <span>Delete</span>
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- Add Images Modal -->
    <div class="modal fade" id="uploadPhotosModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light-subtle">
                    <h1 class="modal-title fs-6" id="exampleModalLabel">Add Images</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Click to choose which photos to upload. Once uploaded, your photos will be displayed on the dashboard</p>
                    <form action="../../../api/business/business.php" method="post" id="uploadImgForm" enctype="multipart/form-data">
                        <input type="hidden" name="business_id" value="<?php echo $businessInfo['business_id']; ?>">
                        <input type="hidden" name="action" value="add_business_imgs">
                        <input class="form-control form-control-sm" type="file" id="uploadImgInput" name="business_images[]" multiple>
                        <div id="uploadImgsPreviewContainer" class="row pt-3">
                        </div>
                    </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn bg-brand-primary btn-brand-primary text-white" id="uploadNewImgBtn">
                            <img src="../../../public/svg/loading.svg" class="d-none" id="uploadLoadingSpinner" alt="" style="width: 20px;">
                            <span>Upload</span>
                        </button>
                    </div>
                </div>
            </div>
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../../../public/js/business-dashboard.js"></script>
    <script src="../../../public/js/business-dashboard-edit.js"></script>
</body>
</html>