
const submitBtn = document.getElementById('submitBtn');
const onboardingForm = document.getElementById('onboardingForm');
const dataInputs = document.querySelectorAll('.data-input');
const requiredInputs = document.querySelectorAll('.data-input.required');
const termsAndCondsCheckbox = document.getElementById('terms_conds_checkbox');
const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

const swiper = new Swiper('.form-swiper', {
    allowTouchMove: false, // disables swipe by user, use buttons only
    effect: 'slide',
    speed: 400, 
});

const checkRequiredInputs = () => {
    let allInputsFilled = true;
    [...requiredInputs].forEach(input => {
        if(input.type === "checkbox" && !input.checked) {
            allInputsFilled = false;
        } else if(input.type === "file" && input.files.length === 0) {
            allInputsFilled = false;
        } else if(input.value.length === 0) {
            allInputsFilled = false;
        }
    });
    return allInputsFilled;
}

const showErrorModal = ({ title, message }) => {
    document.getElementById('errorModalTitle').innerText = title;
    document.getElementById('errorModalMessage').innerText = message;
    errorModal.show();
}

// check first if all required inputs are filled before submitting the form
submitBtn.addEventListener('click', () => {
    if(!checkRequiredInputs()) {
        const errorDetails = {
            title: "Form Incomplete",
            message: "Please fill all required fields before submitting the form."
        }
        return showErrorModal(errorDetails);
    }
    onboardingForm.submit();
});

// Only enable submit button if terms and conditions checkbox is checked
termsAndCondsCheckbox.addEventListener('change', (event) => {
    submitBtn.removeAttribute('disabled');
    if(event.target.checked) {
        submitBtn.removeAttribute('disabled');
    } else {
        submitBtn.setAttribute('disabled', 'disabled');
    }
});

//  Add eventr listeners to all data inputs to update the review section
dataInputs.forEach(input => {
    input.addEventListener('change', (event) => {
        const targetId = event.target.getAttribute('data-target');
        const reviewInput = document.getElementById(targetId);
        if (event.target.type === "file") {
            reviewInput.value = event.target.files[0].name.toString();
        } else if(event.target.type === "checkbox"){
            reviewInput.checked = event.target.checked;
        } else {
            reviewInput.value = event.target.value;
        }
    });
});
