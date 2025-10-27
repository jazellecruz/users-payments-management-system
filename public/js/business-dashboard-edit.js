// this is for business_rep/dashboard/edit-business.php
const confirmModalEl = document.getElementById('confirmModal');
const confirmModal = new bootstrap.Modal(confirmModalEl);
const confirmActionModalBtn = document.getElementById('confirmActionModalBtn');
const confirmModalMsgEl = document.getElementById('confirmActionMessage');
const saveBusInfoEditBtn = document.getElementById('saveBusInfoEditBtn');
const editBusInfoForm = document.getElementById('editBusInfoForm');
const lonInput = document.getElementById('loc_long');
const latInput = document.getElementById('loc_lat');
const addressInputs = document.getElementsByClassName('address-input');
const addressReviewMapEl = document.getElementById('address-review-map');
const toggleCheckImgBtns = document.querySelectorAll('.toggleCheckImgBtn');
const uploadNewImgBtn = document.getElementById('uploadNewImgBtn');
const uploadImgPreviewContainer = document.getElementById('uploadImgsPreviewContainer');
const uploadImgInput = document.getElementById('uploadImgInput');
const uploadImgModal = new bootstrap.Modal(document.getElementById('uploadPhotosModal'));
const businessImgs = document.getElementsByClassName('business-img');
const uploadImgForm = document.getElementById('uploadImgForm');
const userMsgModal = new bootstrap.Modal(document.getElementById('userMsgModal'));
const userMsgModalHeader = document.getElementById('userMsgModalHeader');
const userMsgContent = document.getElementById('userMsgContent');
const userMsgTitle = document.getElementById('userMsgTitle');
const deletePhotosModal = new bootstrap.Modal(document.getElementById('deletePhotosModal'));
const deletePhotosForm = document.getElementById('deletePhotosForm');
const deletePhotosBtn = document.getElementById('deletePhotosBtn');
const imgToDelCheckbox = document.getElementsByClassName('imgToDelCheckbox');
const editProfileForm = document.getElementById('editProfileForm');
const editCoverForm = document.getElementById('editCoverForm');

let newBusinessPhotos = [];

let debounceTimer;

const baseLayer = {
    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    options: {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }
}

const addressMapLat = parseFloat(latInput.value);
const addressMapLon = parseFloat(lonInput.value);

const addressReviewMap = L.map('address-review-map').setView([addressMapLat, addressMapLon], 16);
L.tileLayer(baseLayer.url, baseLayer.options).addTo(addressReviewMap);
const addressReviewMarker = L.marker([addressMapLat, addressMapLon]).addTo(addressReviewMap);

const showConfirmModal = (msg) => {
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

async function handleAddressChange(map, marker) {
    const address = {
        unitNumber: document.getElementById("bus_unit_number_input").value,
        street: document.getElementById("bus_street_input").value,
        city: document.getElementById("bus_city_input").value,
        province: document.getElementById("bus_province_input").value,
        postalCode: document.getElementById("bus_postal_code_input").value,
        country: document.getElementById("bus_country_input").value
    }

    const data = await getGeoCoordinates(address);

    if(data.length === 0) {
        console.error("No location found");
        return;
    }

    const lat = parseFloat(data[0].lat);
    const lon = parseFloat(data[0].lon);

    renderAddressOnMap(map, marker, {lat: lat, lon: lon});
    
    lonInput.value = lon;
    latInput.value = lat;
}

const renderAddressOnMap = (map, marker, coordinates) => {
    if (marker) map.removeLayer(marker);

    map.setView([coordinates.lat, coordinates.lon], 16);

    if(marker) marker = null;

    marker = L.marker([coordinates.lat, coordinates.lon]).addTo(map);
}


imgInputs.forEach(input => {
    input.addEventListener('change', function(e) {
        const targetId = e.target.dataset.target;
        const previewImg = document.getElementById(targetId);
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
});

saveBusInfoEditBtn.addEventListener('click', async () =>  {
    const confirmMsg = "Are you sure you want to save the changes made to the business information?";
    const proceedToSubmit = await showConfirmModal(confirmMsg);
    if(proceedToSubmit) {
        editBusInfoForm.submit();
    }
});

for(input of addressInputs) {
  input.addEventListener("input", debounce(() => handleAddressChange(addressReviewMap, addressReviewMarker), 3000));
}

toggleCheckImgBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // assuming the checkbox is before the button
        let checkbox = btn.previousElementSibling.previousElementSibling;
        checkbox.checked = !checkbox.checked;
    });
});

const removeImage = (index) => {
    newBusinessPhotos.splice(index, 1);
    updateUploadPhotoInput();
    showUploadImgPreview();
}

const updateUploadPhotoInput = () => {
    const dataTransfer = new DataTransfer();
    newBusinessPhotos.forEach(file => dataTransfer.items.add(file));
    uploadImgInput.files = dataTransfer.files;
}

uploadImgInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    newBusinessPhotos = [...newBusinessPhotos, ...files];
    updateUploadPhotoInput();
    showUploadImgPreview();
});

const showUploadImgPreview = () => {
    uploadImgPreviewContainer.innerHTML = '';

    newBusinessPhotos.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItemContainer = document.createElement('div');
            previewItemContainer.classList.add('col', 'col-12', 'col-lg-4', 'mb-3');

            const previewItem = document.createElement('div');
            previewItem.classList.add('preview-upload-item');

            const img = document.createElement('img');
            img.src = e.target.result;

            const removeBtn = document.createElement('button');
            removeBtn.classList.add('remove-upload-btn');
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', () => removeImage(index));

            // previewItem.classList.add('flex-fill');
            previewItem.appendChild(img);
            previewItem.appendChild(removeBtn);
            previewItemContainer.appendChild(previewItem);
            uploadImgPreviewContainer.appendChild(previewItemContainer);
        };
        reader.readAsDataURL(file);
    });
}

const showUserMsgModal = ({ title, content, headerBgColor}) => {
    userMsgModalHeader.innerText = title ? title : "Notice";
    userMsgContent.innerText = content;
    userMsgModalHeader.classList.add(headerBgColor ? headerBgColor : 'bg-secondary');
    userMsgModal.show();
}

uploadNewImgBtn.addEventListener('click', () => {
    MAX_AMT_IMG_UPLOAD = 5;

    // do form validation before submitting request
    if(newBusinessPhotos.length + businessImgs.length > MAX_AMT_IMG_UPLOAD) {
        uploadImgModal.hide();
        showUserMsgModal({
            title: "Max Image Limit Reached",
            content: `A business can only have a maximum of ${MAX_AMT_IMG_UPLOAD} images. Please remove some images before uploading new ones.`,
            headerBgColor: 'bg-warning'
        });
        return;
    }

    if(newBusinessPhotos.length === 0) {
        uploadImgModal.hide();
        showUserMsgModal({
            title: "No Images Uploaded",
            content: `Please choose or add photos you want to upload before proceeding.`,
            headerBgColor: 'bg-warning'
        });
        return;
    }

    showLoadingSpinner('uploadLoadingSpinner');
    uploadNewImgBtn.disabled = true;
    uploadImgForm.submit();
});

function showLoadingSpinner (className) {
    const spinner = document.getElementById(className);
    spinner.classList.remove('d-none');
    spinner.classList.add('d-inline-block');
}

deletePhotosBtn.addEventListener('click', function (e)  {
    // do form validation before submitting request 
    const checkedImgsArr = Array.from(imgToDelCheckbox);
    const checkedImgs = checkedImgsArr.filter(cb => cb.checked);

    if(checkedImgs.length === 0) {
        deletePhotosModal.hide();
        showUserMsgModal({
            title: "No Images Selected",
            content: `Please select at least one image to delete.`,
            headerBgColor: 'bg-warning'
        });
        return;
    }

    if(imgToDelCheckbox.length === checkedImgs.length) {
        deletePhotosModal.hide();
        showUserMsgModal({
            title: "No Images Selected",
            content: "Cannnot delete all images. A business must have at least one image.",
            headerBgColor: 'bg-warning'
        });
        return;
    }
    showLoadingSpinner('delLoadingSpinner');
    deletePhotosBtn.disabled = true;
    deletePhotosForm.submit();
});

// To do: instead of targeting by ID, use class name to target multiple forms if needed in the future 
editCoverForm.addEventListener('submit', function(e) {
    this.querySelector('.loading-spinner').classList.remove('d-none');
    this.querySelector('.loading-spinner').classList.add('d-inline-block');
    this.querySelector('button[type="submit"]').disabled = true;
});

editProfileForm.addEventListener('submit', function(e) {
    this.querySelector('.loading-spinner').classList.remove('d-none');
    this.querySelector('.loading-spinner').classList.add('d-inline-block');
    this.querySelector('button[type="submit"]').disabled = true;
});