<div class="vh-100 d-none d-md-block bg-brand-primary text-white" style="width: 280px;">
    <div class="d-flex flex-column justify-content-between h-100">
        <div class="pt-3">
            <div class="d-flex gap-2 px-3">
                    <i class="bi bi-grid-fill ml-2"></i>
                    <p class="mb-0">Dashboard
                </p>
            </div>
            <div class="border-bottom border-secondary my-3"></div>
            <div class="px-3">
                <div class="d-flex flex-column gap-4" style="list-style-type: none; padding: left 50px;">
                    <div class="d-flex flex-row align-items-center gap-2">
                        <i class="bi bi-file-earmark-text-fill ml-2"></i>
                        <a class="nav-link fw-light text-light-gray" href="applications.php" style="">My Applications</a>
                    </div>
                    <div class="d-flex flex-row align-items-center gap-2">
                        <i class="bi  bi-building-fill ml-2"></i>
                        <a class="nav-link fw-light text-light-gray" href="businesses.php" style="">My Businesses</a>
                    </div>
                    <div class="d-flex flex-row align-items-center gap-2">
                        <i class="bi bi-person-badge-fill ml-2"></i>
                        <a class="nav-link fw-light text-light-gray" href="profile.php" style="">My Profile</a>
                    </div>
                    <div class="d-flex flex-row align-items-center gap-2">
                        <i class="bi bi-person-fill-gear  ml-2"></i>
                        <a class="nav-link fw-light text-light-gray" href="account.php" style="">My Account</a>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="border-bottom border-secondary my-3"></div>
            <div>
                <ul class="pl-3 d-flex flex-column gap-2" style="list-style-type: none;">
                    <li class=""><button class="d-flex gap-2 text-white bg-transparent border-0 p-0" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal"><i class="bi bi-power mr-2"></i><span class="text-light-gray">Log Out</span></button></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="confirmLogoutModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title fw-bold">CONFIRM ACTION</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to log out?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <a type="button" href="../../../api/auth/logout.php" class="btn btn-danger btn-sm">Log Out</a>
      </div>
    </div>
  </div>
</div>