<?php

$adminName = isset($_SESSION['first_name']) && isset($_SESSION['last_name']) ? $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] : 'Admin Name';
$adminEmail = isset($_SESSION['email']) ? $_SESSION['email'] : 'administration@email.com';
$adminAvatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'http://upload.wikimedia.org/wikipedia/commons/8/89/Portrait_Placeholder.png';

?>

<nav class="navbar bg-white sticky-top border-bottom">
  <div class="container-fluid">
    <div class="d-flex justify-space-between align-items-center">
        <button class="btn d-block d-md-none p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
            <i class="bi bi-list h1"></i>
        </button>
        <div class="d-none d-md-block d-flex align-items-center gap-1">
            <img src="../../../public/images/jlinklogo.png" style="width: 26px;" alt="">
            <span class="fw-bold text-brand-primary">JOURNEOLINK <span class=" text-brand-secondary">ADMIN</span></span>
        </div>
    </div>
    <div class="d-flex align-items-center">
        <div class="me-3 text-end">
            <p class="mb-0 fw-semibold"><?php echo $adminName; ?></p>
            <p class="mb-0 text-muted small"><?php echo $adminEmail; ?></p>
        </div>
        <img src="<?php echo $adminAvatar; ?>" alt="Profile Avatar" class="rounded-circle border" width="40" height="40" style="object-fit: cover;"> 
    </div>
  </div>
</nav>
<div class="offcanvas bg-brand-primary offcanvas-start d-block d-md-none d-flex flex-column" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasScrollingLabel"></h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-theme="dark"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="d-flex flex-column justify-content-between text-white h-100 flex-grow-1">
        <div class="">
            <div class="d-flex gap-2 px-3">
                    <i class="bi bi-grid-fill ml-2"></i>
                    <p class="mb-0">Dashboard
                </p>
            </div>
            <div class="border-bottom border-secondary my-3"></div>
            <div class="flex-column d-flex gap-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-currency-dollar"></i>
                    <p class="mb-0">Transactions</p>
                </div>
                <div>
                    <ul class="d-flex flex-column gap-2 mb-0" style="list-style-type: none; padding: left 50px;">
                        <li><a class="nav-link fw-light text-light-gray" href="" style="">Payments</a></li>
                        <li><a class="nav-link fw-light text-light-gray" href="" style="">Payouts</a></li>
                        <li><a class="nav-link fw-light text-light-gray" href="" style="">Refunds</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-bottom border-secondary my-3"></div>
        </div>
        <div>
            <div class="border-bottom border-secondary my-3"></div>
            <div>
                <ul class="pl-3 d-flex flex-column gap-3" style="list-style-type: none;">
                    <li>
                        <a class="nav-link d-flex gap-2 pb-1" href="" style="">
                            <i class="bi bi-person-circle mr-2"></i>
                            <span class="text-light-gray">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link d-flex gap-2" href="" style="">
                            <i class="bi bi-power mr-2"></i>
                            <span class="text-light-gray">Log Out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
  </div>
</div>