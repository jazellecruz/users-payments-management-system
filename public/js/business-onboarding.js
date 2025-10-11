
const submitBtn = document.getElementById("submitBtn");
const onboardingForm = document.getElementById("onboardingForm");
const dataInputs = document.querySelectorAll(".data-input");
const requiredInputs = document.querySelectorAll(".data-input.required");
const termsAndCondsCheckbox = document.getElementById("terms_conds_checkbox");
const errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
const authLetterInputGrp = document.getElementById("auth_letter_input_grp");
const authLetterInput = document.getElementById("auth_letter");
const authLetterReviewGrp = document.getElementById("auth_letter_review_grp");
const busRepRoleSelect = document.getElementById("bus_rep_role");
const busRepRoleOpts = document.querySelectorAll(".bus_rep_role_opt");
const busTypeOpts = document.querySelectorAll(".bus_type_opt");
const busPhotoInput = document.getElementById('business_photos');
const busPhotoPreviewContainer = document.getElementById('preview');
const reviewContainer = document.getElementById('bus-photo-review-container');
const loadingSpinner = document.getElementById("loadingSpinner");
const lonInput = document.getElementById("bus_longitude");
const latInput = document.getElementById("bus_latitude");
let businessPhotos = [];

const ownerOptIndex = 1; // assuming "Owner" is the second option in the select dropdown

// Temporary coordinates for initial viewing of map (Quezon City, PH)
const tempLong = 121.0437; 
const tempLat = 14.6760;
const tempZoomLvl = 6;

let debounceTimer;
let markerMain;
let markerReview;

const map = L.map('map').setView([tempLat, tempLong], tempZoomLvl);
const mapReview = L.map('map-review').setView([tempLat, tempLong], tempZoomLvl);

const swiper = new Swiper(".form-swiper", {
    allowTouchMove: false, 
    effect: "slide",
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
    document.getElementById("errorModalTitle").innerText = title;
    document.getElementById("errorModalMessage").innerText = message;
    errorModal.show();
}

const showReviewPreview = () => {
    reviewContainer.innerHTML = '';

    businessPhotos.forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        const previewItem = document.createElement('div');
        previewItem.classList.add('preview-item');

        const img = document.createElement('img');
        img.src = e.target.result;

        previewItem.appendChild(img);
        reviewContainer.appendChild(previewItem);
    };
    reader.readAsDataURL(file);
    });
}

const removeImage = (index) => {
    businessPhotos.splice(index, 1);
    updateBusPhotoInput();
    showPreview();
    showReviewPreview();
}

const updateBusPhotoInput = () => {
    const dataTransfer = new DataTransfer();
    businessPhotos.forEach(file => dataTransfer.items.add(file));
    busPhotoInput.files = dataTransfer.files;
}

const showPreview = () => {
    busPhotoPreviewContainer.innerHTML = '';

    businessPhotos.forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        const previewItem = document.createElement('div');
        previewItem.classList.add('preview-item');

        const img = document.createElement('img');
        img.src = e.target.result;

        const removeBtn = document.createElement('button');
        removeBtn.classList.add('remove-btn');
        removeBtn.innerHTML = '&times;';
        removeBtn.addEventListener('click', () => removeImage(index));

        previewItem.appendChild(img);
        previewItem.appendChild(removeBtn);
        busPhotoPreviewContainer.appendChild(previewItem);
    };
    reader.readAsDataURL(file);
    });
}

function debounce(func, delay) {
  return (...args) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => func(...args), delay);
  };
}

const getGeoCoordinates = async (address) => {
    const res = await axios({
        method: 'get',
        url: 'http://localhost/users-payments-management-system/api/maps/geolocation.php',
        params: {
            action: 'get-geolocation',
            unitNumber: address.unitNumber,
            street: address.street,
            city: address.city,
            province: address.province,
            postalCode: address.postalCode,
            country: address.country
        }
    });

    return res.data;    
}

// Separate find location service and handling re-rendering of maps
async function renderAddressOnMap() {
    const address = {
        unitNumber: document.getElementById("bus_unit_number").value,
        street: document.getElementById("bus_street").value,
        city: document.getElementById("bus_city").value,
        province: document.getElementById("bus_province").value,
        postalCode: document.getElementById("bus_postal_code").value,
        country: document.getElementById("bus_country").value
    }

    const data = await getGeoCoordinates(address);

    if(data.length === 0) {
        console.error("No location found");
        return;
    }

    const lat = parseFloat(data[0].lat);
    const lon = parseFloat(data[0].lon);

    if (markerMain) map.removeLayer(markerMain);
    if (markerReview) mapReview.removeLayer(markerReview);

    map.setView([lat, lon], 16);
    mapReview.setView([lat, lon], 16);

    markerMain = L.marker([lat, lon]).addTo(map)
        .bindPopup(`<b>${data[0].display_name}</b>`)
        .openPopup();

    markerReview = L.marker([lat, lon]).addTo(mapReview)
        .bindPopup(`<b>${data[0].display_name}</b>`)
        .openPopup();

    lonInput.value = lon;
    latInput.value = lat;
}


// check first if all required inputs are filled before submitting the form
submitBtn.addEventListener("click", () => {
    if(!checkRequiredInputs()) {
        const errorDetails = {
            title: "Form Incomplete",
            message: "Please fill all required fields before submitting the form."
        }
        return showErrorModal(errorDetails);
    }
    loadingSpinner.classList.remove("d-none");
    submitBtn.setAttribute("disabled", "disabled");
    onboardingForm.submit();
});

// Only enable submit button if terms and conditions checkbox is checked
termsAndCondsCheckbox.addEventListener("change", (event) => {
    submitBtn.removeAttribute("disabled");
    if(event.target.checked) {
        submitBtn.removeAttribute("disabled");
    } else {
        submitBtn.setAttribute("disabled", "disabled");
    }
});

 // Add eventr listeners to all data inputs to update the review section
dataInputs.forEach(input => {
    input.addEventListener("change", (event) => {
        const targetId = event.target.getAttribute("data-target");
        const reviewInput = document.getElementById(targetId);
        if(event.target.type === "file" && !event.target.multiple) {
            reviewInput.value = event.target.files[0].name.toString();
        } 
        if(event.target.type === "checkbox") {
            reviewInput.checked = event.target.checked;
        } 
        if(event.target.type === "radio") {
             reviewInput.checked = event.target.checked;
            document.getElementById(event.target.getAttribute("data-opposite-target")).checked = false;
        } 
        if(event.target.type === "text") {
            reviewInput.value = event.target.value;
        }
        if(event.target.type === "textarea") {
            reviewInput.value = event.target.value;
        }
        if(event.target.type === "select-one") { 
        const selectedText = event.target.options[event.target.selectedIndex].text;
        reviewInput.value = selectedText;
        }
    });
});

busRepRoleSelect.addEventListener("change", (e) => {
    if(busRepRoleSelect.selectedIndex === ownerOptIndex) {
        authLetterInputGrp.classList.remove("d-block");
        authLetterReviewGrp.classList.remove("d-block");
        authLetterReviewGrp.classList.add("d-none");
        authLetterInputGrp.classList.add("d-none");
        authLetterInput.value = null;
        authLetterInput.classList.remove("required");
    } else {
        authLetterInputGrp.classList.remove("d-none");
        authLetterReviewGrp.classList.remove("d-none");
        authLetterInputGrp.classList.add("d-block");
        authLetterReviewGrp.classList.add("d-block");
        authLetterInput.classList.add("required");
    }
});


busPhotoInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    businessPhotos = [...businessPhotos, ...files];
    updateBusPhotoInput();
    showPreview();
    showReviewPreview();
});

const baseLayer = {
    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    options: {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }
}

L.tileLayer(baseLayer.url, baseLayer.options).addTo(map);
L.tileLayer(baseLayer.url, baseLayer.options).addTo(mapReview);

["bus_unit_number", "bus_street", "bus_postal_code"].forEach(id => {
  const input = document.getElementById(id);
  input.addEventListener("input", debounce(renderAddressOnMap, 3000));
});