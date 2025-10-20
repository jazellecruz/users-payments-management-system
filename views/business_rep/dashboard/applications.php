<?php
    session_start();

    require_once __DIR__ . '/../../../db/db_conn.php';
    require_once __DIR__ . '/../../../utils/auth.php';
    require_once __DIR__ . '/../../../queries/business.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bus_rep') {
        redirectUser('../auth/business_rep_login.php');
        exit();
    }

    $conn = getDBConnection();

    $businessRepId = null;

    if(isset($_SESSION['business_rep_id'])) {
        $businessRepId = $_SESSION['business_rep_id'];
    } else {
        $businessRepId = getBusinessRepByUserId($conn, $_SESSION['user_id'])['business_rep_id'];
    }

    $totalApplications = getAppsCountByStatus($conn, $businessRepId, '');
    $pendingApplications = getAppsCountByStatus($conn, $businessRepId, 'pending');
    $approvedApplications = getAppsCountByStatus($conn, $businessRepId, 'approved');
    $rejectedApplications = getAppsCountByStatus($conn, $businessRepId, 'rejected');
    $applications = getBusinessApplicationsByRepId($conn, $businessRepId);

    // corresponding class names for status circle icons
    $circleStatusClassNames= [
        'pending' => 'text-warning',
        'approved' => 'text-success',
        'rejected' => 'text-danger',
        'cancelled' => 'text-secondary'
    ]; 
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

        label {
            font-size: 12px;
        }

        input:read-only, textarea:read-only {
            background-color: #f4f7fbff;
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
                <!-- overview of applications -->
                <div class="pt-4 px-md-4 w-100">
                    <h5 class="fw-bold pb-3 text-brand-primary">Applications Overview</h5>
                    <!-- overview container -->
                    <div class="d-flex flex-column flex-md-row gap-3 mb-5">
                        <div class="bg-white rounded d-flex flex-column border border-light-gray p-3 justify-content-between flex-grow-1" style="height: 130px;">
                            <p class="mb-0">Total Applications Submitted</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h2><?php echo $totalApplications; ?></h2>
                            </div>
                        </div>
                        <div class="bg-white rounded d-flex flex-column border border-light-gray p-3 justify-content-between flex-grow-1" style="height: 130px;">
                            <p class="mb-0">Pending Applications</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h2><?php echo $pendingApplications; ?></h2>
                            </div>
                        </div>
                        <div class="bg-white rounded d-flex flex-column border border-light-gray p-3 justify-content-between flex-grow-1" style="height: 130px;">
                            <p class="mb-0">Approved applications</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h2><?php echo $approvedApplications; ?></h2>
                            </div>
                        </div>
                        <div class="bg-white rounded d-flex flex-column border border-light-gray p-3 justify-content-between flex-grow-1" style="height: 130px;">
                            <p class="mb-0">Rejected Applications</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <h2><?php echo $rejectedApplications; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- applications list container -->
                <div class="px-md-4 w-100">
                    <!-- table actions -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 bg-white p-2 rounded border border-light-gray">
                        <h5 class="fw-bold text-brand-primary m-0 p-0">All Applications</h5>
                        <div class="d-flex flex-row gap-2">
                            <div class="d-flex flex-row gap-2">
                                <select name="status" id="" class="form-select form-select-sm border-light-gray">
                                    <option disabled selected>Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button class="btn btn-brand-primary bg-brand-primary text-white btn-sm">Filter</button>
                            </div>
                            <form class="d-flex" role="search">
                                <input class="form-control form-control-sm me-2 border-light-gray" type="search" placeholder="Search" aria-label="Search"/>
                                <button class="btn btn-brand-primary bg-brand-primary text-white btn-sm" type="submit">Search</button>
                            </form>
                            <a class="btn bg-brand-secondary btn-brand-secondary text-white btn-sm" href="#" role="button">New Application</a>
                        </div>
                    </div>

                    <div class="">
                        <table class=" rounded table text-center overflow-scroll">
                            <thead>
                                <tr>
                                    <th class="text-brand-primary" style="background-color: #A7C58E" scope="col">ID</th>
                                    <th class="text-brand-primary" style="background-color: #A7C58E" scope="col">Business Name</th>
                                    <th class="text-brand-primary" style="background-color: #A7C58E" scope="col">Submitted At</th>
                                    <th class="text-brand-primary" style="background-color: #A7C58E" scope="col">Status</th>
                                    <th class="text-brand-primary" style="background-color: #A7C58E" scope="col">Remarks</th>
                                    <th class="text-brand-primary" style="background-color: #A7C58E" scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach ($applications as $application):
                                ?>
                                <tr>
                                    <td class="text-muted py-2"><?php echo $application['public_business_application_id']; ?></td>
                                    <td class="text-muted py-2"><?php echo $application['business_name']; ?></td>
                                    <td class="text-muted py-2"><?php echo date('M j, Y, D', strtotime($application['created_at'])); ?></td>
                                    <td class="text-muted py-2">
                                        <div class="mx-auto
                                        badge-base d-flex flex-row align-items-center gap-1 rounded-pill
                                        <?php if($application['application_status'] === 'pending') echo "badge-pending"; ?>
                                        <?php if($application['application_status'] === 'rejected') echo "badge-rejected"; ?>
                                        <?php if($application['application_status'] === 'approved') echo "badge-approved"; ?>
                                        <?php if($application['application_status'] === 'cancelled') echo "badge-cancelled"; ?>
                                        ">
                                            <i class="bi bi-circle-fill"></i>
                                            <span class=""><?php echo ucfirst($application['application_status']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted py-2">None</td>
                                    <td class="text-muted py-2">
                                        <i class="bi bi-eye-fill h5" data-bs-toggle="modal" data-bs-target="#view-modal-<?php echo $application['public_business_application_id']; ?>"></i>
                                    </td>
                                    <div class="modal modal-xl fade" tabindex="-1" id="view-modal-<?php echo $application['public_business_application_id']; ?>">
                                        <div class="modal-dialog  modal-dialog-scrollable">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Business Application</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h6 class="mb-2 fw-bold">Business Details</h6>
                                                <div class="d-flex flex-column flex-md-row gap-3 pb-3">
                                                    <div class="w-100">
                                                        <label for="business_name" class="form-label text-muted">Business Name</label>
                                                        <input class="form-control form-control-sm" type="text" id="business_name" value="<?php echo $application['business_name']; ?>" readonly>
                                                    </div>
                                                    <div class="w-100">
                                                        <label for="business_name" class="form-label text-muted">Business Type</label>
                                                        <input class="form-control form-control-sm" type="text" id="business_name" value="<?php echo $application['business_type_name']; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="w-100 pb-2">
                                                    <label for="business_desc" class="form-label text-muted">Business Description</label>
                                                    <textarea id="business_desc" class="form-control form-control-sm" readonly rows="3"><?php echo $application['business_desc']; ?></textarea>
                                                </div>
                                                <div class="w-100 pb-2">
                                                    <label for="business_address" class="form-label text-black">Business Address</label>
                                                    <div class="d-flex flex-column flex-md-row gap-3">
                                                        <div class="w-100">
                                                            <label  for="bus_unit_number_input" class="form-label text-muted">Unit Number</label>
                                                            <input type="text" class="form-control form-control-sm" id="bus_unit_number_input" value="<?php echo $application['business_unit_number']; ?>" readonly>
                                                        </div>
                                                        <div class="w-100">
                                                            <label for="bus_street_input" class="form-label text-muted">Street</label>
                                                            <input type="text" class="form-control form-control-sm" id="bus_street_input" value="<?php echo $application['business_street']; ?>" readonly>
                                                        </div>
                                                        <div class="w-100">
                                                            <label for="bus_postal_code_input" class="form-label text-muted">Postal Code</label>
                                                            <input type="number" class="form-control form-control-sm" id="bus_postal_code_input" value="<?php echo $application['business_postal_code']; ?>" readonly>
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
                                                            data-map-target="map-<?php echo $application['public_business_application_id']; ?>" 
                                                            data-lat-target="loc_lat-<?php echo $application['public_business_application_id']; ?>" 
                                                            data-long-target="loc_long-<?php echo $application['public_business_application_id']; ?>"
                                                        >
                                                        <input type="text" hidden 
                                                            id="loc_lat-<?php echo $application['public_business_application_id']; ?>" 
                                                            value="<?php echo $application['loc_lat']; ?>" 
                                                            readonly
                                                        >
                                                        <input type="text" hidden 
                                                            id="loc_long-<?php echo $application['public_business_application_id']; ?>" 
                                                            value="<?php echo $application['loc_long']; ?>" 
                                                            readonly
                                                        >
                                                        <div class=" w-100 w-md-75 rounded" id="map-<?php echo $application['public_business_application_id']; ?>"></div>
                                                    </div>
                                                    <div class="d-flex flex-column flex-md-row gap-3 mt-3 pb-2">
                                                        <div class="w-100">
                                                            <label class="form-label text-muted">Business Contact Number</label>
                                                            <input value="<?php echo $application['business_contact_num']; ?>" type="text" class="form-control form-control-sm" readonly>
                                                        </div>
                                                        <div class="w-100">
                                                            <label class="form-label text-muted">Business Email</label>
                                                            <input value="<?php echo $application['business_email']; ?>" type="text" class="form-control form-control-sm" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <label for="business_name" class="form-label text-muted">Business Permit</label>
                                                        <div class="input-group input-group-sm mb-3">
                                                            <?php 
                                                                $lastSlashPos = strrchr($application['business_permit_url'], '/'); 
                                                                $fileName = substr($lastSlashPos, 1);
                                                            ?>
                                                            <input type="text" class="form-control" value="<?php echo $fileName?>" readonly>
                                                            <span class="input-group-text">
                                                                <a class="text-secondary" target="_blank" href="<?php echo $application['business_permit_url']?>">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="w-100 pb-2">
                                                        <label for="business_name" class="form-label text-muted">Is business operating?</label>
                                                        <div class="d-flex flex-column flex-lg-row gap-3">
                                                            <div class="form-check">
                                                                <input 
                                                                    class="form-check-input" 
                                                                    type="radio"
                                                                    id="defaultCheck1" 
                                                                    <?php echo $application['is_operating'] ? 'checked' : ''; ?>
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
                                                                    <?php echo !$application['is_operating'] ? 'checked' : ''; ?>
                                                                    disabled
                                                                >
                                                                <span class="small">
                                                                    No, the business is not currently operating.
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <label class="pb-2 text-muted">Business Photos</label>
                                                        <div>
                                                            <div class="row">
                                                                <?php 
                                                                    $business_photos = explode(',', $application['group_concat(b_p.photo_url)']);
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
                                                    <div class="d-flex flex-column flex-lg-row gap-3 pb-2">
                                                        <div class="w-100">
                                                            <label  class="form-label text-muted">Role within the Business</label>
                                                            <input value="<?php echo ucwords(strtolower(str_replace(" ", "_", $application['business_rep_code']))); ?>" type="text" class="form-control form-control-sm" readonly>
                                                        </div>
                                                        <?php if (!$application['authorization_letter_url'] == null) { ?>
                                                        <div class="w-100">
                                                            <label class="form-label text-muted">Authorization Letter</label>
                                                            <div class="input-group input-group-sm mb-3">
                                                                <?php 
                                                                    $lastSlashPos = strrchr($application['authorization_letter_url'], '/'); 
                                                                    $fileName = substr($lastSlashPos, 1);
                                                                ?>
                                                                <input type="text" class="form-control" value="<?php echo $fileName?>" readonly>
                                                                <span class="input-group-text">
                                                                    <a class="text-secondary" target="_blank" href="<?php echo $application['authorization_letter_url']?>">
                                                                        <i class="bi bi-eye-fill"></i>
                                                                    </a>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="w-100 pb-2">
                                                        <label for="business_name" class="form-label text-muted"> Accepted the Terms and Conditions of Application?</label>
                                                        <div class="d-flex flex-column flex-lg-row gap-3">
                                                            <div class="form-check">
                                                                <input 
                                                                    class="form-check-input" 
                                                                    type="radio"
                                                                    id="defaultCheck1" 
                                                                    <?php echo $application['agreed_to_terms'] ? 'checked' : ''; ?>
                                                                    disabled
                                                                    >
                                                                <span class="small">
                                                                    Yes, I have read and accepted the terms and conditions.
                                                                </span>
                                                            </div>
                                                            <div class="form-check">
                                                                <input 
                                                                    class="form-check-input" 
                                                                    type="radio" 
                                                                    id="defaultCheck2"
                                                                    <?php echo !$application['agreed_to_terms'] ? 'checked' : ''; ?>
                                                                    disabled
                                                                >
                                                                <span class="small">
                                                                    No, I have not read and accepted the terms and conditions.
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h6 class="mb-2 fw-bold">Application Details</h6> 
                                                <div class="d-flex flex-column flex-md-row gap-3 pb-2">
                                                    <div class="w-100">
                                                        <label for="business_name" class="form-label text-muted">Application ID</label>
                                                        <input class="form-control form-control-sm" type="text" id="business_name" value="<?php echo $application['public_business_application_id']; ?>" readonly>
                                                    </div>
                                                    <div class="w-100">
                                                        <label for="business_name" class="form-label text-muted">Application Status</label>
                                                        <div class="status-input-wrapper status-circle-input-icon">
                                                            <i class="bi bi-circle-fill <?php echo $circleStatusClassNames[$application['application_status']]?>"></i>
                                                            <input class="form-control form-control-sm" type="text" id="business_name" value="<?php echo ucfirst($application['application_status']); ?>" readonly>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="w-100">
                                                        <label for="business_name" class="form-label text-muted">Submitted At</label>
                                                        <input class="form-control form-control-sm" type="text" id="business_name" value="<?php echo date('M j, Y, D h:i A', strtotime($application['created_at'])); ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="w-100 pb-2">
                                                    <label for="business_name" class="form-label text-muted">Remarks</label>
                                                    <textarea class="form-control form-control-sm" id="business_name" rows="3" readonly><?php echo $application['remarks'] ? $application['remarks'] : 'None'; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <?php if($application['application_status'] === 'pending') {?>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-cancellation-modal">Cancel Application</button>
                                                <?php } ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </tr>
                                <div>
                                    <div class="modal fade" id="confirm-cancellation-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light">
                                                    <h6 class="modal-title" id="exampleModalLabel">CONFIRM CANCELLATION</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-muted">
                                                    Are you sure you want to 
                                                    <span class="text-danger fw-semibold">CANCEL</span> application 
                                                    <span class="fw-semibold fst-italic text-black" id=""><?php echo $application['public_business_application_id']; ?></span>? 
                                                    This action will void your application and cannot be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm m-0" data-bs-dismiss="modal">Close</button>
                                                    <form action="../../../api/business/application.php" method="POST" class="p-0">
                                                        <input name="public_application_id" value="<?php echo $application['public_business_application_id']; ?>" hidden>
                                                        <input name="application_id" value="<?php echo $application['business_application_id']; ?>" hidden>
                                                        <input name="action" value="cancel_application" hidden>
                                                        <button type="submit" class="btn btn-danger btn-sm m-0">CANCEL</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div>
                             <nav>
                                <ul class="pagination justify-content-end">
                                    <li class="page-item">
                                        <button type="submit" name="page" value="" class="page-link page-nav-link bg-secondary text-white">Previous</button>
                                    </li>
                                    <li class="page-item"><button class="page-link text-black">1</button></li>
                                    <li class="page-item">
                                        <button type="submit" name="page" value="" class="page-link page-nav-link text-white btn-brand-primary bg-brand-primary">Next</button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../../../public/js/business-dashboard.js"></script>

<script>

</script>
</body>
</html>