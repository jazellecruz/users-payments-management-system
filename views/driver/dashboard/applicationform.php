<?php
session_start();
require_once '../../../utils/auth.php';

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'driver'
) {
    redirectUser('../auth/driver_login.php');
    exit;
}

$userName = $_SESSION['first_name'] ?? 'Driver';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Driver Dashboard - Application Form</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #3F562C">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="/driver/dashboard.php">
      <img src="../../../public/images/brand-logo1.png" width="32" height="32" class="me-2" alt="JourneoLink Logo">
      <span class="fw-bold">JourneoLink Driver</span>
    </a>

    <div class="dropdown ms-auto">
      <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" 
              type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle me-2 fs-5"></i> 
        <span class="fw-semibold"><?= htmlspecialchars($userName) ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
        <li><h6 class="dropdown-header">Welcome, <?= htmlspecialchars($userName) ?></h6></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="../../../api/auth/logout.php">Sign Out</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h1>Application Form</h1>

  <form action="submit-application.php" method="POST" enctype="multipart/form-data" class="row g-4 mt-3">
    
    <!-- First Column -->
    <div class="col-md-4">
      <div class="mb-3">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" placeholder="Enter your first name" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Middle Name</label>
        <input type="text" name="middle_name" class="form-control" placeholder="Enter your middle name (optional)">
      </div>
      <div class="mb-3">
        <label class="form-label">Birthdate</label>
        <input type="date" name="birth_date" class="form-control" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Contact Number</label>
        <input type="text" name="contact_number" class="form-control" placeholder="09XXXXXXXXX" required>
      </div>
      <div class="mb-3">
  <label class="form-label">Address</label>
  <input type="text" name="user_address" class="form-control" placeholder="Enter your current address" required>
</div>

     

      <div class="mb-3">
        <label class="form-label">Alternative Email Address</label>
        <input type="email" name="alternative_email" class="form-control" placeholder="Optional backup email">
      </div>
      

    </div>

    <!-- Second Column -->
    <div class="col-md-4">
      
      <div class="mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" placeholder="Enter your last name" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Extension Name</label>
        <input type="text" name="ext_name" class="form-control" placeholder="e.g. Jr., Sr., III">
      </div>
      <div class="mb-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select" required>
          <option value="">Select gender...</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
      </div>
      
      <div class="mb-3">
        <label class="form-label">NBI Clearance</label>
        <input type="file" name="nbi_clearance" class="form-control" required>
      </div>
      <div class="mb-3">
  <label class="form-label">Proof of Address</label>
  <input type="file" name="proof_of_address" class="form-control" required>
</div>
      

      <!-- Submit Button Row -->
<div class="col-12 d-flex justify-content-center gap-3 mt-4">
  <a href="/users-payments-management-system/views/driver/dashboard/applications.php" class="btn btn-secondary">
    Cancel
  </a>
  <button type="submit" class="btn btn-success">
    Submit
  </button>
      </div>
    </div>

    <!-- Third Column -->
    <div class="col-md-4">
      <!-- Upload a Photo -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Upload a Photo</label>
        <div id="photoDrop" class="upload-card text-center p-4 border rounded">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mb-2">
            <path d="M12 16l4-4h-3V4h-2v8H8l4 4z" fill="#3F562C"/>
            <path d="M19 19H5a2 2 0 0 1-2-2V9h2v8h14V9h2v8a2 2 0 0 1-2 2z" fill="#9bb08a"/>
          </svg>
          <p class="mb-2 text-muted">Drag and drop or click to upload</p>
          <button type="button" class="btn btn-outline-success btn-sm">Choose file</button>
          <input id="profilePhotoInput" type="file" name="profile_photo" accept="image/*" class="d-none" required>

          <!-- Preview -->
          <div id="photoPreview" class="preview mt-3 d-none">
            <img id="photoImg" src="" alt="Preview" class="rounded border" style="max-width: 160px; max-height: 160px; object-fit: cover;">
            <div class="small text-muted mt-2" id="photoName"></div>
          </div>
        </div>
      </div>

      <style>
      .upload-card {
        border-color: #dfe5d9 !important;
        background: #f8faf7;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
      }
      .upload-card:hover { border-color: #3F562C !important; background: #f2f6f0; }
      .upload-card.dragover { border-color: #3F562C !important; background: #eaf1e6; }
      </style>

      <script>
      (function () {
        const drop = document.getElementById('photoDrop');
        const input = document.getElementById('profilePhotoInput');
        const preview = document.getElementById('photoPreview');
        const img = document.getElementById('photoImg');
        const nameLabel = document.getElementById('photoName');

        drop.addEventListener('click', () => input.click());
        drop.querySelector('button').addEventListener('click', (e) => {
          e.stopPropagation(); input.click();
        });

        ['dragenter','dragover'].forEach(evt =>
          drop.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); drop.classList.add('dragover'); })
        );
        ['dragleave','drop'].forEach(evt =>
          drop.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); drop.classList.remove('dragover'); })
        );
        drop.addEventListener('drop', e => {
          const file = e.dataTransfer.files?.[0];
          if (file) { input.files = e.dataTransfer.files; showPreview(file); }
        });

        input.addEventListener('change', () => {
          const file = input.files?.[0];
          if (file) showPreview(file);
        });

        function showPreview(file) {
          if (!file.type.startsWith('image/')) return;
          const reader = new FileReader();
          reader.onload = e => {
            img.src = e.target.result;
            preview.classList.remove('d-none');
            nameLabel.textContent = file.name + ' (' + Math.round(file.size/1024) + ' KB)';
          };
          reader.readAsDataURL(file);
        }
      })();
      </script>

       <div class="mb-3">
        <label class="form-label">License Number</label>
        <input type="text" name="license_number" class="form-control" required>
      </div>
     <div class="mb-3">
        <label class="form-label">License Expiry Date</label>
        <input type="date" name="license_expiry_date" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">License Photo</label>
        <input type="file" name="license_photo" class="form-control" required>
      </div>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.body.style.zoom = "85%"; 
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>