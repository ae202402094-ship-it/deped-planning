<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* FIX: This prevents Bootstrap from turning your sidebar links blue.
       We tell the browser: "Only apply Bootstrap link styles inside the modal."
    */
    a {
        color: inherit !important;
        text-decoration: none !important;
    }

    /* Keep the modal's internal buttons and links looking correct */
    .modal-content a {
        color: #0d6efd;
        text-decoration: underline;
    }
</style>
<div class="modal fade" id="fullScreenMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="modal-header border-0" style="background-color: #1e293b; color: white;">
                <h5 class="modal-title fw-bold text-uppercase tracking-wider" style="font-size: 0.8rem;">Pinpoint School Location</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="fullMap" style="height: 100%; width: 100%;"></div>
            </div>
            <div class="modal-footer border-0 shadow-lg">
                <button type="button" onclick="confirmLocation()" class="btn btn-lg w-100 text-white fw-bold text-uppercase" style="background-color: #a52a2a; border-radius: 1rem; font-size: 0.75rem; letter-spacing: 0.1em;">
                    Confirm Selected Location
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
   var fullMap, fullMarker, targetLatId, targetLngId;

function openMapPopup(latId, lngId, currentLat, currentLng) {
    targetLatId = latId;
    targetLngId = lngId;
    
    let startLat = parseFloat(currentLat) || 6.9214; 
    let startLng = parseFloat(currentLng) || 122.0739;

    var modalElement = document.getElementById('fullScreenMapModal');
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    modalElement.addEventListener('shown.bs.modal', function() {
        if (!fullMap) {
            fullMap = L.map('fullMap').setView([startLat, startLng], 17);
            
            // Standard Street View (OpenStreetMap)
            var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(fullMap);

            // Satellite View (Esri World Imagery) - Professional for Zamboanga context
            var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EBP, and the GIS User Community'
            });

            // Create the Toggle Control (Top Right)
            var baseMaps = {
                "Streets": streets,
                "Satellite": satellite
            };
            L.control.layers(baseMaps).addTo(fullMap);

            fullMarker = L.marker([startLat, startLng], {draggable: true}).addTo(fullMap);
            fullMap.on('click', function(e) { fullMarker.setLatLng(e.latlng); });
        } else {
            fullMap.setView([startLat, startLng], 17);
            fullMarker.setLatLng([startLat, startLng]);
        }
        
        // Critical fix for rendering bugs in modals
        fullMap.invalidateSize();
    }, {once: true});
}
</script>