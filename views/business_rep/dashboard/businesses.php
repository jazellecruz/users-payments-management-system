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

    if(isset($_SESSION['business_rep_id'])) {
        $businessRepId = $_SESSION['business_rep_id'];
    } else {
        $res = getBusinessRepByUserId($conn, $_SESSION['user_id']);

        if(!$res) {
            $businessRepId = null;
        } else {            
            $businessRepId = $res['business_rep_id'];
        }
    }

    $businesses = getBusinessesByRepId($conn, $businessRepId);

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
                <?php if(!empty($businesses)) { ?>
                    <div class="pb-3 px-md-4 pt-4">
                        <h4 class="fw-bold  text-brand-primary">Businesses</h4>
                        <p class="small">All your businesses approved by Journeolink are listed down below.</p>
                    </div>
                    <div class="px-md-4">
                        <div class="bg-white d-flex justify-content-between align-items-center py-2 px-3 rounded border border-light-gray">
                            <h6 class="fw-bold  text-brand-primary  mb-0">All Businesses Handled</h6>
                            <form class="d-flex" role="search">
                                <input class="form-control me-2 form-control-sm" type="search" placeholder="Search" aria-label="Search"/>
                                <button class="btn btn-brand-primary btn-sm text-white bg-brand-primary" type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex flex-wrap gap-4 px-md-4 py-4">
                            <?php foreach($businesses as $b){ ?>
                            <div class="rounded overflow-hidden bg-white border border-light-gray" style="width: 18rem;">
                                <img src="<?php echo $b['business_cover_img_url'] ? $b['business_cover_img_url'] : 'https://www.ufwc.co.uk/images/no-img-placeholder.png'; ?>" class="card-img-top" alt="..." style="height: 120px; object-fit: cover;">
                                <div class="p-3">
                                    <h6 class=" fw-bold text-brand-primary"><?php echo $b['business_name']; ?></h6>
                                    <p class=" small text-muted ellipsis"><?php echo $b['business_desc']; ?></p>
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="edit-business.php" method="get">
                                            <input type="hidden" name="id" value="<?php echo $b['public_business_id']; ?>">
                                            <button type="submit" class="btn bg-brand-primary bg-brand-secondary btn-brand-secondary text-white btn-sm">Edit</button>
                                        </form>
                                        <form action="view-business.php" method="get">
                                            <input type="hidden" name="id" value="<?php echo $b['public_business_id']; ?>">
                                            <button type="submit" class="btn bg-brand-primary btn-brand-primary text-white btn-sm">View</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="px-md-4 mb-4">
                        <div class="bg-white d-flex justify-content-end align-items-center py-2 px-3 rounded border border-light-gray">
                            <nav>
                                <ul class="pagination justify-content-end pb-0 mb-0">
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
                <?php } else { ?>
                    <div class="">
                        <div class="d-flex flex-column justify-content-center align-items-center py-5 px-md-4">
                            <i class="bi bi-building-fill-slash text-muted h1"></i>
                            <h5 class="fw-bold  text-brand-primary mt-3">No Businesses Found</h5>
                            <p class="small text-muted text-center">Your businesses will show here once approved by the Journeolink team.</p>
                        </div>
                    </div>   
                <?php }  ?>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>