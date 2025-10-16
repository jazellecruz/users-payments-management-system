<div class="vh-100 d-none d-md-block bg-brand-primary text-white" style="width: 280px;">
    <div class="d-flex flex-column justify-content-between h-100">
        <div class="pt-3">
            <div class="d-flex gap-2 px-3">
                    <i class="bi bi-grid-fill ml-2"></i>
                    <p class="mb-0">Dashboard
                </p>
            </div>
            <div class="border-bottom border-secondary my-3"></div>
            <div class="flex-column d-flex gap-2 px-3">
                <p class="mb-0">
                    <i class="bi bi-currency-dollar"></i>
                    Transactions
                </p>
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
                    <li><a class="nav-link d-flex gap-2" href="" style=""><i class="bi bi-person-circle mr-2"></i><span class="text-light-gray">My Profile</span></a></li>
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