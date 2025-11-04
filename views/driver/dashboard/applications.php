<?php
$error = $_GET['error'] ?? null;
?>
<?php
session_start();

require_once '../../../db/db_conn.php';
require_once '../../../utils/auth.php';

$conn = getDbConnection();

$latestStatus = null;
$checkLatest = $conn->prepare("
  SELECT status FROM driver_applications 
  WHERE user_id = ? 
  ORDER BY applied_at DESC LIMIT 1
");
$checkLatest->bind_param("i", $_SESSION['user_id']);
$checkLatest->execute();
$checkLatest->bind_result($latestStatus);
$checkLatest->fetch();
$checkLatest->close();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'driver'
) {
    redirectUser('../auth/driver_login.php');
    exit;
}


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$countQuery = "SELECT COUNT(*) AS total FROM driver_applications WHERE user_id = " . $_SESSION['user_id'];
$countResult = $conn->query($countQuery);
$totalRows = $countResult ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalRows / $limit);

$query = "
    SELECT 
        COUNT(*) AS total,
        SUM(status = 'pending') AS pending,
        SUM(status = 'rejected') AS rejected
    FROM driver_applications WHERE user_id = " . $_SESSION['user_id'];
$counts = $conn->query($query)->fetch_assoc();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Driver Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    .text-muted-custom {
      color: #565656;
    }
  </style>

</head>
<body class="bg-light">
<?php if ($error === 'pending'): ?>
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Heads up!</strong> You already have a pending application. Please wait for it to be reviewed before submitting a new one.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
<?php
$userName = $_SESSION['first_name'] ?? 'User';
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #3F562C">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="../../../public/images/brand-logo1.png" width="32" height="32" class="me-2">
      <span class="fw-bold">JourneoLink Driver</span>
    </a>

    <div class="dropdown ms-auto">
      <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle me-2 fs-5"></i> <span class="fw-semibold"><?= htmlspecialchars($userName) ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
        <li><h6 class="dropdown-header">Welcome, <?= htmlspecialchars($userName) ?></h6></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="../../../api/auth/logout.php">Sign Out</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Applications Overview</h2>
     <?php
$hasPending = ($latestStatus === 'pending');
$canUpdate = ($latestStatus === 'cancelled');

?>
<?php if ($hasPending): ?>
  <button class="btn fw-bold" style="background-color: #aaa; color: white;" disabled>
    Add Application
  </button>
<?php else: ?>
  <a href="/users-payments-management-system/views/driver/dashboard/applicationform.php" class="btn fw-bold" style="background-color: <?= $canUpdate ? '#E8B364' : '#3F562C' ?>; color: white;">
    <?= $canUpdate ? 'Add Application' : 'Add Application' ?>
  </a>
<?php endif; ?>


  </div>

  <!-- Applications Overview -->
<div class="row mb-4">
  <!-- Applications Submitted -->
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
          <h5 class="mb-2 text-muted-custom">Applications Submitted</h5>
          <p class="fs-4 fw-bold mb-0"><?= $counts['total'] ?? 0 ?></p>
        </div>
        <div class="ms-3 mt-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="43" height="39" viewBox="0 0 43 39" fill="none">
<path fill-rule="evenodd" clip-rule="evenodd" d="M5.375 4.875C5.375 3.58207 5.94129 2.34209 6.9493 1.42785C7.95731 0.513615 9.32446 0 10.75 0L24.9749 0C25.6876 0.000138054 26.3711 0.257036 26.875 0.714188L36.8376 9.75C37.3416 10.207 37.6248 10.8269 37.625 11.4733V34.125C37.625 35.4179 37.0587 36.6579 36.0507 37.5721C35.0427 38.4864 33.6755 39 32.25 39H10.75C9.32446 39 7.95731 38.4864 6.9493 37.5721C5.94129 36.6579 5.375 35.4179 5.375 34.125V4.875ZM24.1875 9.75L25.5312 3.65625L33.5938 10.9688L26.875 12.1875C26.1622 12.1875 25.4787 11.9307 24.9747 11.4736C24.4706 11.0165 24.1875 10.3965 24.1875 9.75ZM12.0938 19.5C11.7374 19.5 11.3956 19.6284 11.1436 19.857C10.8916 20.0855 10.75 20.3955 10.75 20.7188C10.75 21.042 10.8916 21.352 11.1436 21.5805C11.3956 21.8091 11.7374 21.9375 12.0938 21.9375H30.9062C31.2626 21.9375 31.6044 21.8091 31.8564 21.5805C32.1084 21.352 32.25 21.042 32.25 20.7188C32.25 20.3955 32.1084 20.0855 31.8564 19.857C31.6044 19.6284 31.2626 19.5 30.9062 19.5H12.0938ZM10.75 25.5938C10.75 25.2705 10.8916 24.9605 11.1436 24.732C11.3956 24.5034 11.7374 24.375 12.0938 24.375H30.9062C31.2626 24.375 31.6044 24.5034 31.8564 24.732C32.1084 24.9605 32.25 25.2705 32.25 25.5938C32.25 25.917 32.1084 26.227 31.8564 26.4555C31.6044 26.6841 31.2626 26.8125 30.9062 26.8125H12.0938C11.7374 26.8125 11.3956 26.6841 11.1436 26.4555C10.8916 26.227 10.75 25.917 10.75 25.5938ZM10.75 30.4688C10.75 30.1455 10.8916 29.8355 11.1436 29.607C11.3956 29.3784 11.7374 29.25 12.0938 29.25H22.8438C23.2001 29.25 23.5419 29.3784 23.7939 29.607C24.0459 29.8355 24.1875 30.1455 24.1875 30.4688C24.1875 30.792 24.0459 31.102 23.7939 31.3305C23.5419 31.5591 23.2001 31.6875 22.8438 31.6875H12.0938C11.7374 31.6875 11.3956 31.5591 11.1436 31.3305C10.8916 31.102 10.75 30.792 10.75 30.4688Z" fill="#988E42"/>
</svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Applications -->
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
          <h5 class="mb-2 text-muted-custom">Pending Applications</h5>
          <p class="fs-4 fw-bold mb-0"><?= $counts['pending'] ?? 0 ?></p>
        </div>
        <div class="ms-3 mt-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
<g clip-path="url(#clip0_180_1791)">
<path fill-rule="evenodd" clip-rule="evenodd" d="M36 18C36 22.7739 34.1036 27.3523 30.7279 30.7279C27.3523 34.1036 22.7739 36 18 36C13.2261 36 8.64773 34.1036 5.27208 30.7279C1.89642 27.3523 0 22.7739 0 18C0 13.2261 1.89642 8.64773 5.27208 5.27208C8.64773 1.89642 13.2261 0 18 0C22.7739 0 27.3523 1.89642 30.7279 5.27208C34.1036 8.64773 36 13.2261 36 18ZM18 7.875C18 7.57663 17.8815 7.29048 17.6705 7.0795C17.4595 6.86853 17.1734 6.75 16.875 6.75C16.5766 6.75 16.2905 6.86853 16.0795 7.0795C15.8685 7.29048 15.75 7.57663 15.75 7.875V20.25C15.7501 20.4483 15.8025 20.6431 15.9021 20.8145C16.0017 20.986 16.1448 21.1281 16.317 21.2265L24.192 25.7265C24.4504 25.8662 24.7532 25.8992 25.0357 25.8186C25.3182 25.7379 25.5579 25.55 25.7037 25.2949C25.8494 25.0399 25.8896 24.7379 25.8157 24.4536C25.7417 24.1693 25.5595 23.9252 25.308 23.7735L18 19.5975V7.875Z" fill="#E8B364"/>
</g>
<defs>
<clipPath id="clip0_180_1791">
<rect width="36" height="36" fill="white"/>
</clipPath>
</defs>
</svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Rejected Applications -->
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
          <h5 class="mb-2 text-muted-custom">Rejected Applications</h5>
          <p class="fs-4 fw-bold mb-0"><?= $counts['rejected'] ?? 0 ?></p>
        </div>
        <div class="ms-3 mt-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
<g clip-path="url(#clip0_180_1798)">
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.5 0C3.30653 0 2.16193 0.474106 1.31802 1.31802C0.474106 2.16193 0 3.30653 0 4.5L0 31.5C0 32.6935 0.474106 33.8381 1.31802 34.682C2.16193 35.5259 3.30653 36 4.5 36H31.5C32.6935 36 33.8381 35.5259 34.682 34.682C35.5259 33.8381 36 32.6935 36 31.5V4.5C36 3.30653 35.5259 2.16193 34.682 1.31802C33.8381 0.474106 32.6935 0 31.5 0L4.5 0ZM12.0465 10.4535C11.8353 10.2423 11.5487 10.1236 11.25 10.1236C10.9513 10.1236 10.6647 10.2423 10.4535 10.4535C10.2423 10.6647 10.1236 10.9513 10.1236 11.25C10.1236 11.5487 10.2423 11.8353 10.4535 12.0465L16.4093 18L10.4535 23.9535C10.3489 24.0581 10.2659 24.1823 10.2093 24.3189C10.1527 24.4556 10.1236 24.6021 10.1236 24.75C10.1236 24.8979 10.1527 25.0444 10.2093 25.1811C10.2659 25.3177 10.3489 25.4419 10.4535 25.5465C10.6647 25.7577 10.9513 25.8764 11.25 25.8764C11.3979 25.8764 11.5444 25.8473 11.6811 25.7907C11.8177 25.7341 11.9419 25.6511 12.0465 25.5465L18 19.5907L23.9535 25.5465C24.0581 25.6511 24.1823 25.7341 24.3189 25.7907C24.4556 25.8473 24.6021 25.8764 24.75 25.8764C24.8979 25.8764 25.0444 25.8473 25.1811 25.7907C25.3177 25.7341 25.4419 25.6511 25.5465 25.5465C25.6511 25.4419 25.7341 25.3177 25.7907 25.1811C25.8473 25.0444 25.8764 24.8979 25.8764 24.75C25.8764 24.6021 25.8473 24.4556 25.7907 24.3189C25.7341 24.1823 25.6511 24.0581 25.5465 23.9535L19.5907 18L25.5465 12.0465C25.6511 11.9419 25.7341 11.8177 25.7907 11.6811C25.8473 11.5444 25.8764 11.3979 25.8764 11.25C25.8764 11.1021 25.8473 10.9556 25.7907 10.8189C25.7341 10.6823 25.6511 10.5581 25.5465 10.4535C25.4419 10.3489 25.3177 10.2659 25.1811 10.2093C25.0444 10.1527 24.8979 10.1236 24.75 10.1236C24.6021 10.1236 24.4556 10.1527 24.3189 10.2093C24.1823 10.2659 24.0581 10.3489 23.9535 10.4535L18 16.4093L12.0465 10.4535Z" fill="#D63936"/>
</g>
<defs>
<clipPath id="clip0_180_1798">
<rect width="36" height="36" fill="white"/>
</clipPath>
</defs>
</svg>
        </div>
      </div>
    </div>
  </div>
</div>


  <!-- Applications Table -->
   <div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 fw-bold">All Applications</h5>
  <form class="d-flex" method="GET" role="search">
    <div class="input-group">
      <span class="input-group-text bg-white border-end-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#3F562C" class="bi bi-search" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
        </svg>
      </span>
      <input 
      class="form-control border-start-0" 
      type="search" 
      name="search" 
      placeholder="Search..." 
      value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </div>
  </form>
</div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0 table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th>Application ID</th>
            <th>Submitted At</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Action</th>
          </tr>
        </thead>
        
        <?php
$query = "
  SELECT 
  driver_app_public_id,
  applied_at,
  status,
  first_name,
  last_name,
  middle_name,
  ext_name,
  birth_date,
  gender,
  user_address,
  active_phone_number,
  alternative_email,
  license_number,
  license_expiry_date
FROM driver_applications 
WHERE user_id = " . $_SESSION['user_id'] . "
ORDER BY applied_at DESC 
";


$result = $conn->query($query);
?>

<tbody>
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()):
      $status = $row['status'];
      $badgeClass = $status === 'pending' ? 'bg-warning text-dark' : ($status === 'rejected' ? 'bg-danger' : ($status === 'cancelled' ? 'bg-secondary' : 'bg-success'));
      $remarks = '-';
    ?>
      <tr>
        <td><?= htmlspecialchars($row['driver_app_public_id']) ?></td>
        <td><?= date("d M Y, h:iA", strtotime($row['applied_at'])) ?></td>
        <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span></td>
        <td><?= htmlspecialchars($remarks) ?></td>
        <td>
          <a href="#" class="btn btn-sm btn-outline-primary view-btn"
            data-id="<?= $row['driver_app_public_id'] ?>"
            data-date="<?= date("d M Y, h:iA", strtotime($row['applied_at'])) ?>"
            data-status="<?= $row['status'] ?>"
            data-remarks="No remarks yet"
            data-birth="<?= $row['birth_date'] ?>"
            data-gender="<?= $row['gender'] ?>"
            data-address="<?= htmlspecialchars($row['user_address']) ?>"
            data-contact="<?= $row['active_phone_number'] ?>"
            data-email="<?= $row['alternative_email'] ?>"
            data-license="<?= $row['license_number'] ?>"
            data-first="<?= $row['first_name'] ?>"
data-last="<?= $row['last_name'] ?>"
data-middle="<?= $row['middle_name'] ?>"
data-ext="<?= $row['ext_name'] ?>"

            data-expiry="<?= $row['license_expiry_date'] ?>">
            
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M15.75 12C15.75 12.9946 15.3549 13.9484 14.6517 14.6517C13.9484 15.3549 12.9946 15.75 12 15.75C11.0054 15.75 10.0516 15.3549 9.34835 14.6517C8.64509 13.9484 8.25 12.9946 8.25 12C8.25 11.0054 8.64509 10.0516 9.34835 9.34835C10.0516 8.64509 11.0054 8.25 12 8.25C12.9946 8.25 13.9484 8.64509 14.6517 9.34835C15.3549 10.0516 15.75 11.0054 15.75 12Z" fill="#373737"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M0 12C0 12 4.5 3.75 12 3.75C19.5 3.75 24 12 24 12C24 12 19.5 20.25 12 20.25C4.5 20.25 0 12 0 12ZM12 17.25C13.3924 17.25 14.7277 16.6969 15.7123 15.7123C16.6969 14.7277 17.25 13.3924 17.25 12C17.25 10.6076 16.6969 9.27226 15.7123 8.28769C14.7277 7.30312 13.3924 6.75 12 6.75C10.6076 6.75 9.27226 7.30312 8.28769 8.28769C7.30312 9.27226 6.75 10.6076 6.75 12C6.75 13.3924 7.30312 14.7277 8.28769 15.7123C9.27226 16.6969 10.6076 17.25 12 17.25Z" fill="#373737"/>
            </svg>
          </a>
        </td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr>
      <td colspan="5" class="text-center text-muted">No applications found.</td>
    </tr>
  <?php endif; ?>
</tbody>
</table>
</div>

<!-- Pagination Footer -->
<div class="card-footer text-end">
  <nav>
    <ul class="pagination justify-content-end mb-0">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
      </li>
      <li class="page-item disabled">
        <span class="page-link">(<?= $page ?>)</span>
      </li>
      <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="applicationModalLabel">Application Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="applicationForm">
          <!-- Personal Info -->
          <div class="mb-2">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" id="modalFirstName" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="modalMiddleName" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" id="modalLastName" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Extension Name</label>
            <input type="text" class="form-control" id="modalExtName" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Birth Date</label>
            <input type="text" class="form-control" id="modalBirthDate" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Gender</label>
            <input type="text" class="form-control" id="modalGender" readonly>
          </div>

          <!-- Contact Info -->
          <div class="mb-2">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" id="modalAddress" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="modalContact" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" id="modalEmail" readonly>
          </div>

          <!-- License Info -->
          <div class="mb-2">
            <label class="form-label">License Number</label>
            <input type="text" class="form-control" id="modalLicense" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">License Expiry</label>
            <input type="text" class="form-control" id="modalLicenseExpiry" readonly>
          </div>

          <!-- Application Info -->
          <div class="mb-2">
            <label class="form-label">Application ID</label>
            <input type="text" class="form-control" id="modalAppId" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Submitted At</label>
            <input type="text" class="form-control" id="modalSubmittedAt" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <input type="text" class="form-control" id="modalStatus" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" id="modalRemarks" rows="3" readonly>No remarks yet</textarea>
          </div>

          <button type="button" class="btn btn-danger d-none" id="cancelBtn">Cancel Application</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal Trigger Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();

      // Basic Info
      document.getElementById('modalAppId').value = this.getAttribute('data-id') || '';
      document.getElementById('modalSubmittedAt').value = this.getAttribute('data-date') || '';
      const status = this.getAttribute('data-status') || '';
      document.getElementById('modalStatus').value = status.charAt(0).toUpperCase() + status.slice(1);
      document.getElementById('modalRemarks').value = this.getAttribute('data-remarks') || 'No remarks yet';

      // Personal Info
      document.getElementById('modalFirstName').value = this.getAttribute('data-first') || '';
      document.getElementById('modalMiddleName').value = this.getAttribute('data-middle') || '';
      document.getElementById('modalLastName').value = this.getAttribute('data-last') || '';
      document.getElementById('modalExtName').value = this.getAttribute('data-ext') || '';
      document.getElementById('modalBirthDate').value = this.getAttribute('data-birth') || '';
      document.getElementById('modalGender').value = this.getAttribute('data-gender') || '';
      document.getElementById('modalAddress').value = this.getAttribute('data-address') || '';
      document.getElementById('modalContact').value = this.getAttribute('data-contact') || '';
      document.getElementById('modalEmail').value = this.getAttribute('data-email') || '';
      document.getElementById('modalLicense').value = this.getAttribute('data-license') || '';
      document.getElementById('modalLicenseExpiry').value = this.getAttribute('data-expiry') || '';

      const cancelBtn = document.getElementById('cancelBtn');
      if (status === 'pending') {
        cancelBtn.classList.remove('d-none');
        cancelBtn.setAttribute('data-id', this.getAttribute('data-id'));
      } else {
        cancelBtn.classList.add('d-none');
      }

      // Show Modal
      const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
      modal.show();
      document.getElementById('cancelBtn').addEventListener('click', function () {
  const appId = this.getAttribute('data-id');
  if (!appId) return;

  if (!confirm('Are you sure you want to cancel this application?')) return;

  fetch('../auth/cancel_application.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + encodeURIComponent(appId)
  })
  .then(res => res.text())
  .then(response => {
    alert(response); 
    location.reload(); 
  })
  .catch(err => {
    console.error(err);
    alert('Something went wrong.');
  });
});

    });
  });
});
</script>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

