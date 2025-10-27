const mapTargetElements = document.querySelectorAll('.map-target-inputs');
const imgInputs = document.querySelectorAll('.img-input');

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');

const popoverList = [...popoverTriggerList].map(popoverTriggerEl => 
    new bootstrap.Popover(popoverTriggerEl, {
    trigger: 'focus'
    })
);

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


mapTargetElements.forEach(e => {
    let targetMap = e.dataset.mapTarget;
    let lat = document.getElementById(e.dataset.latTarget).value;
    let long = document.getElementById(e.dataset.longTarget).value;

    let mapElement = document.getElementById(targetMap);
    mapElement.style.height = "250px";

    const map = L.map(targetMap).setView([lat, long], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, long]).addTo(map);
    map.invalidateSize();
});

document.querySelectorAll('.truncate').forEach(el => {
const max = 100;
const txt = el.textContent.trim();
if (txt.length > max) {
    el.textContent = txt.slice(0, max) + '...';
}
});
