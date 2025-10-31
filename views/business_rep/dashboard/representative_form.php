<?php
    session_start();
    
    require_once __DIR__ . '/../../../utils/auth.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bus_rep') {
        redirectUser("../auth/business_rep_login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];

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
    <link rel="stylesheet" href="../../../public/css/business-dashboard.css">
    <style>
        body {
            background-color: #f3f4f5ff;
        }

        .form-label {
            font-size: 12px;
        }

        .form-check span{
            font-size: 14px;
        }

        button > * {
            pointer-events: none;
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
                <div class="pb-3 pt-2">
                    <h3 class="text-brand-primary fw-bold pb-2">Business Representative Form</h3>
                    <p class="text-muted lh-md">Complete the following fields to register as an official business representative. Your representative information will be used for future applications.</p>
                </div>
                <div class="bg-white p-4 rounded-3 border border-light-gray">
                    <form action="" method="POST" id="business-rep-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create_bus_rep_profile">
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="w-100">
                                    <label  for="first_name" class="form-label text-muted fw-bold">First Name</label>
                                    <input name="first_name" type="text" class="form-control form-control-sm custom-input data-input required" id="first_name" data-target="first_name_review"  placeholder="Enter your first name">
                                </div>
                                <div class="w-100">
                                    <label for="last_name" class="form-label text-muted fw-bold">Last Name</label>
                                    <input name="last_name" type="text" class="form-control form-control-sm custom-input data-input required" id="last_name" data-target="last_name_review" placeholder="Enter your last name">
                                </div>
                                <div class="w-100">
                                    <label for="middle_name" class="form-label text-muted fw-bold">Middle Name</label>
                                    <input name="middle_name" type="text" class="form-control form-control-sm custom-input data-input required" id="middle_name" data-target="middle_name_review" placeholder="Enter your middle name">
                                </div>
                                <div class="w-100">
                                    <label for="ext_name" class="form-label text-muted fw-bold">Ext</label>
                                    <input name="ext_name" type="text" class="form-control form-control-sm custom-input data-input " id="ext_name" data-target="ext_name_review" placeholder="Ex. Jr., Sr. III">
                                </div>
                            </div>
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="w-100">
                                    <label for="birthdate" class="form-label text-muted fw-bold">Birthdate</label>
                                    <input name="birth_date" type="date" class="form-control form-control-sm custom-input data-input required" id="birthdate" data-target="birthdate_review" placeholder="Enter your birthdate">
                                </div>
                                <div class="w-100">
                                    <label for="gender" class="form-label text-muted fw-bold">Gender</label>
                                    <select name="gender" class="form-select form-select-sm custom-input data-input required" id="gender" data-target="gender_review">
                                        <option selected disabled>Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="address" class="form-label text-muted fw-bold">Address</label>
                                <input name="address" type="text" class="form-control form-control-sm custom-input data-input required" id="address" data-target="address_review" placeholder="Enter your address">
                            </div>
                            <div class="d-flex flex-column flex-md-row gap-3">
                                <div class="w-100">
                                    <label for="contact_number" class="form-label mb-1 text-muted fw-bold">Contact Number</label>
                                    <span class="text-muted d-block mb-2 form-label">Your contact number must be active.</span>
                                    <input name="contact_num" type="text" class="form-control form-control-sm custom-input data-input required" id="contact_number" data-target="contact_number_review" placeholder="Enter your contact number">
                                </div>
                                <div class="w-100">
                                    <label for="alt_email" class="form-label mb-1 text-muted fw-bold">Alternative Email</label>
                                    <span class="text-muted d-block mb-2 form-label">Your alternative email must be active.</span>
                                    <input name="alt_email" type="text" class="form-control form-control-sm custom-input data-input required" id="alt_email" data-target="alt_email_review" placeholder="Enter your alternative email">
                                </div>
                            </div>
                            <div class="w-100 d-flex flex-column flex-lg-row gap-3">
                                <div class="w-100">
                                    <label for="valid_id" class="form-label text-muted fw-bold">Any valid government ID</label>
                                    <input name="valid_id" type="file" class="form-control form-control-sm custom-input data-input required" id="valid_id" data-target="valid_id_review" placeholder="Upload your valid government ID">
                                </div>
                                <div class="w-100">
                                    <label for="id_picture" class="form-label text-muted fw-bold">2x2 ID Picture</label>
                                    <input name="id_picture" type="file" class="form-control form-control-sm custom-input data-input required" id="id_picture" data-target="id_picture_review" placeholder="Upload your 2x2 ID Picture">
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input data-input" type="checkbox" name="terms_conds_checkbox" id="rep_form_terms_conds_checkbox" data-target="terms_conds_checkbox_review">
                                <span class="form-label" for="terms_conds_checkbox">I accept the 
                                    <a class="text-underline text-brand-secondary pe-auto" data-bs-toggle="modal" data-bs-target="#terms-conditions-modal">Terms and Conditions</a> 
                                    and consent to the use of my data for verification purposes and to represent as a Business Representative in the Journeolink Business platform.</span>
                            </div>
                            <div class="d-flex justify-content-start">
                                <button type="button" id="rep-form-submit-btn" class="btn btn-sm px-4 py-2 bg-brand-primary text-white btn-brand-primary d-flex gap-2 align-items-center" disabled>
                                    <div class="spinner-border text-light loading-spinner d-none small" role="status" style="width: 20px; height: 20px;">
                                        <span class="visually-hidden">Loading...</span>
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
                    <p>By submitting this form, you agree to the collection, storage, and processing of the personal information and documents you provide, including but not limited to your name, contact details, government-issued IDs and documents. This information will be used <strong>solely for the purpose of verifying your identity and eligibility</strong> to represent as a Business Representative in the Journeolink Business platform.</p>
                    <h6 class="mt-3">How we handle your information</h6>
                    <ul>
                        <li><strong>Secure storage:</strong> Your data is stored securely and access is limited to authorized personnel involved in verification.</li>
                        <li><strong>Purpose limitation:</strong> Collected data is used only for identity verification.</li>
                        <li><strong>Retention:</strong> We retain data only as long as necessary to complete verification or as required by law.</li>
                        <li><strong>No unauthorized sharing:</strong> We do not share your verification documents with third parties except where required by law or authorized by you.</li>
                    </ul>
                    <h6 class="mt-3">Your rights</h6>
                    <p>You may withdraw your consent or request deletion of your data after submission by contacting our support team at <a href="mailto:support@journeolink.com">support@journeolink.com</a>. Please note that withdrawing consent may prevent you from being verified as an eligible representative of your business.</p>
                    <hr>
                    <p class="small text-muted mb-0">By accepting, you confirm that the information and documents you provide are accurate and that you consent to the processing described above.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // TO DO: move into a separate js file later
    const termsCondsCheckbox = document.getElementById('rep_form_terms_conds_checkbox');
    const repFormSubmitBtn = document.getElementById('rep-form-submit-btn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const businessRepForm = document.getElementById('business-rep-form');

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

    const handleRepFormSubmit = async (e) => {

        if (!checkIfRequiredFieldsFilled()) {
            showUserMsgModal({
                title: "Missing Information",
                content: "Please fill in all required fields.",
                headerBgColor: 'bg-danger',
                titleColor: 'text-white'
            });
            return;
        }

        const confirmMsg = "You’re about to submit your Business Representative Application. Confirm that all provided details are accurate. Proceed?";
        const confirmed = await showConfirmModal(confirmMsg);

        if (!confirmed) return;

        hideConfirmModal();

        try {
            // disable submission
            e.target.disabled = true;
            e.target.querySelector('.loading-spinner').classList.remove('d-none');
            e.target.querySelector('.loading-spinner').classList.add('d-inline-block');

            const res = await axios.post(
                '../../../api/business/profile.php', 
                new FormData(businessRepForm)
            );
            
            showUserMsgModal({
                title: "Success!",
                content: res.data.message,
                headerBgColor: 'bg-success',
                titleColor: 'text-white'
            });
            
        } catch(err) {
            showUserMsgModal({
                title: "Error",
                content: err.response.data.userMsg,
                headerBgColor: 'bg-danger',
                titleColor: 'text-white'
            });
        } finally {
            e.target.disabled = false;
            e.target.querySelector('.loading-spinner').classList.remove('d-inline-block');
            e.target.querySelector('.loading-spinner').classList.add('d-none');
            businessRepForm.reset();
        }


    };

    const checkIfRequiredFieldsFilled = () => {
        const requiredFields = document.querySelectorAll('.data-input.required');
        for (let field of requiredFields) {
            if (!field.value.trim()) {
                return false;
            }
        }
        return true;
    };

    termsCondsCheckbox.addEventListener('change', function() {
        repFormSubmitBtn.disabled = !this.checked;
    });

    repFormSubmitBtn.addEventListener('click', (e) => handleRepFormSubmit(e));

</script>
</body>
</html>