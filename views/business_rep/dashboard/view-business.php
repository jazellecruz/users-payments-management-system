<?php
    session_start();
    
    require_once __DIR__ . '/../../../db/db_conn.php';
    require_once __DIR__ . '/../../../utils/utils.php';
    require_once __DIR__ . '/../../../utils/auth.php';
    require_once __DIR__ . '/../../../queries/business.php';

    $infoAccStatusMsg = "An active business account makes the business visible to users on Journeolink platform. A disabled business account restricts access and visibility of the business on the platform.";
    $infoOperationStatMsg = "Non-operating businesses will be marked as 'Temporarily Closed' on the platform, informing users of their current status.";
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bus_rep') {
        redirectUser('../auth/business_rep_login.php');
        exit();
    }

    $businessId = null;
    $publicId = null;
    $businessInfo = null;

    $conn = getDbConnection();

    if(isset($_GET['id'])) {
        $publicId = $_GET['id'];
        $businessId = getBusinessIdByPublicId($conn, $publicId);
        $businessInfo = getBusinessById($conn, $businessId);
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../../../public/css/global.css">
    <link rel="stylesheet" href="../../../public/css/business-dashboard.css">
    <style>
        body {
            background-color: #f3f4f5ff;
        }

        input[readonly], textarea[readonly] {
            background-color: #F6F7FB;
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
                        <img class="rounded object-fit" src="<?php echo $businessInfo['business_cover_img_url'] ? $businessInfo['business_cover_img_url'] : 'https://www.ufwc.co.uk/images/no-img-placeholder.png'; ?>" alt=""  srcset="" style="width: 100%; height: 300px; object-fit: cover;">
                        <div class="d-flex gap-4 align-items-center flex-column flex-md-row mt-3">
                            <img class=" rounded rounded-circle" src="<?php echo $businessInfo['business_profile_img'] ? $businessInfo['business_profile_img'] : "https://www.ufwc.co.uk/images/no-img-placeholder.png"?>" alt=""  srcset=""                            
                                width="160" 
                                height="160" 
                                style="object-fit: cover; border: 6px solid white; margin-top: -40px; margin-left: 20px;
                                "
                            >
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
                        <div class="p-3 d-flex flex-column gap-3">
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="w-100">
                                    <label class="form-label text-muted">Business Name</label>
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $businessInfo['business_name']; ?>" readonly>
                                </div>
                                <div class="w-100">
                                    <label class="form-label text-muted">Business Type</label>
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $businessInfo['business_type_name']; ?>" readonly>
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="business_desc" class="form-label text-muted">Business Description</label>
                                <textarea id="business_desc" class="form-control form-control-sm" readonly rows="3"><?php echo $businessInfo['business_desc']; ?></textarea>
                            </div>
                            <div class="w-100 pb-2">
                                <label for="business_address" class="form-label text-muted">Business Address</label>
                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <div class="w-100">
                                        <label  for="bus_unit_number_input" class="form-label text-muted">Unit Number</label>
                                        <input type="text" class="form-control form-control-sm" id="bus_unit_number_input" value="<?php echo $businessInfo['business_unit_number']; ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="bus_street_input" class="form-label text-muted">Street</label>
                                        <input type="text" class="form-control form-control-sm" id="bus_street_input" value="<?php echo $businessInfo['business_street']; ?>" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="bus_postal_code_input" class="form-label text-muted">Postal Code</label>
                                        <input type="number" class="form-control form-control-sm" id="bus_postal_code_input" value="<?php echo $businessInfo['business_postal_code']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3 mt-3 pb-2">
                                    <div class="w-100">
                                        <label for="bus_city_input" class="form-label text-muted">City</label>
                                        <input value="Quezon City" type="text" class="form-control form-control-sm" id="bus_city_input" data-target="bus_city" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="bus_province_input" class="form-label text-muted">Province</label>
                                        <input value="Metro Manila" type="text" class="form-control form-control-sm" id="bus_province_input" data-target="bus_province" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label for="bus_country_input" class="form-label text-muted">Country</label>
                                        <input value="Philippines" type="text" class="form-control form-control-sm" id="bus_country_input" data-target="bus_country" readonly>
                                    </div>
                                </div>
                                <div class="map-wrapper pt-1">
                                    <input type="text" class="map-target-inputs" hidden 
                                        data-map-target="map-<?php echo $businessInfo['public_business_id']; ?>" 
                                        data-lat-target="loc_lat-<?php echo $businessInfo['public_business_id']; ?>" 
                                        data-long-target="loc_long-<?php echo $businessInfo['public_business_id']; ?>"
                                    >
                                    <input type="text" hidden 
                                        id="loc_lat-<?php echo $businessInfo['public_business_id']; ?>" 
                                        value="<?php echo $businessInfo['loc_lat']; ?>" 
                                        readonly
                                    >
                                    <input type="text" hidden 
                                        id="loc_long-<?php echo $businessInfo['public_business_id']; ?>" 
                                        value="<?php echo $businessInfo['loc_long']; ?>" 
                                        readonly
                                    >
                                    <div class=" w-100 w-md-75 rounded" id="map-<?php echo $businessInfo['public_business_id']; ?>"></div>
                                </div>
                                <div class="d-flex flex-column flex-md-row gap-3 mt-3 pb-2">
                                    <div class="w-100">
                                        <label class="form-label text-muted">Business Contact Number</label>
                                        <input value="<?php echo $businessInfo['business_contact_num']; ?>" type="text" class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="w-100">
                                        <label class="form-label text-muted">Business Email</label>
                                        <input value="<?php echo $businessInfo['business_email']; ?>" type="text" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="w-100">
                                    <label class="form-label text-muted">Business Permit</label>
                                    <div class="input-group input-group-sm mb-3">
                                        <?php 
                                            $lastSlashPos = strrchr($businessInfo['business_permit_url'], '/'); 
                                            $fileName = substr($lastSlashPos, 1);
                                        ?>
                                        <input type="text" class="form-control" value="<?php echo $fileName?>" readonly>
                                        <span class="input-group-text">
                                            <a class="text-secondary" target="_blank" href="<?php echo $businessInfo['business_permit_url']?>">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </span>
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
                                                class="form-check-input" 
                                                type="radio"
                                                id="defaultCheck1" 
                                                <?php echo $businessInfo['is_operating'] ? 'checked' : ''; ?>
                                                readonly
                                                disabled
                                                >
                                            <span  class="small">
                                                Yes, the business is currently operating.
                                            </span>
                                        </div>
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input" 
                                                type="radio" 
                                                id="defaultCheck2"
                                                <?php echo !$businessInfo['is_operating'] ? 'checked' : ''; ?>
                                                disabled
                                            >
                                            <span class="small">
                                                No, the business is not currently operating.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-lg-row gap-3">
                                    <div class="w-100">
                                        <label  class="form-label text-muted">My role within the Business</label>
                                        <input value="<?php echo ucwords(strtolower(str_replace(" ", "_", $businessInfo['business_position_name']))); ?>" type="text" class="form-control form-control-sm" readonly>
                                    </div>
                                    <?php if ($businessInfo['authorization_letter_url'] != null) { ?>
                                        <div class="w-100">
                                            <label class="form-label text-muted">Authorization Letter</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <?php 
                                                    $lastSlashPos = strrchr($businessInfo['authorization_letter_url'], '/'); 
                                                    $fileName = substr($lastSlashPos, 1);
                                                ?>
                                                <input type="text" class="form-control" value="<?php echo $fileName?>" readonly>
                                                <span class="input-group-text">
                                                    <a class="text-secondary" target="_blank" href="<?php echo $businessInfo['authorization_letter_url']?>">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded border border-light-gray">
                        <div class="p-3 border-bottom border-light-gray">
                            <p class="fw-bold fs-5 text-brand-primary mb-0 pb-0">Business Account Information</p>
                        </div>
                        <div class="d-flex flex-column gap-3 p-3"> 
                            <div class="w-100">
                                <div class="d-flex align-items-center justify-content-start gap-2 mb-2">
                                    <label class="form-label text-muted pb-0 mb-0">Business Account Status</label>
                                    <button type="button" class="bg-transparent border-0 p-0 m-0" data-bs-toggle="popover" data-bs-title="Account Status" data-bs-content="<?php echo $infoAccStatusMsg; ?>" data-bs-placement="top">
                                        <i class="bi bi-info-circle text-muted small info-popover-icon fw-bold"></i>
                                    </button>
                                </div>
                                <div class="">
                                    <div class="status-input-wrapper status-circle-input-icon w-100">
                                        <i class="bi bi-circle-fill <?php echo !$businessInfo['is_disabled'] ? 'text-success' : 'text-danger'; ?>"></i>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo ucfirst(!$businessInfo['is_disabled'] ? 'active' : 'disabled'); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded border border-light-gray">
                        <div class="p-3 border-bottom border-light-gray">
                            <p class="fw-bold fs-5 text-brand-primary mb-0 pb-0">Business Photos</p>
                        </div>
                        <div class="w-100 p-3">
                            <div>
                                <div class="row">
                                    <?php 
                                        $business_photos = explode(',', $businessInfo['group_concat(b_p.photo_url)']);
                                        foreach ($business_photos as $photo): ?>
                                            <div class="col-12  col-lg-4 mb-3">
                                                <div class="thumbnail w-100" style="height: 250px;">
                                                    <img src="<?php echo $photo; ?>" alt="Business Photo" class="object-fit-cover rounded mb-2" style="width: 100%; object-fit: cover; height: 100%;">
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded border border-light-gray">
                        <div class="p-3 border-bottom border-light-gray">
                            <p class="fw-bold fs-5 text-brand-primary mb-0 pb-0">Application Details</p>
                        </div>
                        <div class="pt-3 px-3">
                            <p class="text-muted small mb-0">The details of the application associated with this business</p>
                        </div>
                        <div class="d-flex flex-column gap-3 p-3">
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="w-100">
                                    <label class="form-label text-muted">Application ID</label>
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $businessInfo['public_business_application_id']; ?>" readonly>
                                </div>
                                <div class="w-100">
                                    <label class="form-label text-muted">Submitted At</label>
                                    <input class="form-control form-control-sm" type="text" value="<?php echo date('M j, Y, D h:i A', strtotime($businessInfo['application_created_at'])); ?>" readonly>
                                </div>
                            </div>
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="w-100">
                                    <label class="form-label text-muted">Application Status</label>
                                    <div class="status-input-wrapper status-circle-input-icon">
                                        <i class="bi bi-circle-fill text-success"></i>
                                        <input class="form-control form-control-sm" type="text" value="<?php echo ucfirst($businessInfo['application_status'])?>" readonly>
                                    </div>
                                </div>
                                 <div class="w-100">
                                    <label class="form-label text-muted">Approved At</label>
                                    <input class="form-control form-control-sm" type="text" value="<?php echo date('M j, Y, D h:i A', strtotime($businessInfo['updated_at'])); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../../../public/js/business-dashboard.js"></script>
</body>
</html>