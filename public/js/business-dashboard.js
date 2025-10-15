const mapTargetElements = document.querySelectorAll('.map-target-inputs');

mapTargetElements.forEach(e => {
    let targetMap = e.dataset.mapTarget;
    let lat = document.getElementById(e.dataset.latTarget).value;
    let long = document.getElementById(e.dataset.longTarget).value;

    console.log(lat, long);
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
