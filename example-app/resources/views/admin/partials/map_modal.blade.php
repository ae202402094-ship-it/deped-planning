<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Prevent Bootstrap from affecting the sidebar or main navigation */
    #fullScreenMapModal a {
        color: inherit;
        text-decoration: inherit;
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

        // Initialize and show the modal
        var modalElement = document.getElementById('fullScreenMapModal');
        var modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        modalElement.addEventListener('shown.bs.modal', function() {
            if (!fullMap) {
                fullMap = L.map('fullMap').setView([startLat, startLng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(fullMap);
                fullMarker = L.marker([startLat, startLng], {draggable: true}).addTo(fullMap);
                
                fullMap.on('click', function(e) { 
                    fullMarker.setLatLng(e.latlng); 
                });
            } else {
                fullMap.setView([startLat, startLng], 15);
                fullMarker.setLatLng([startLat, startLng]);
            }
            // Critical: Fixes rendering bugs when map starts in a hidden modal
            fullMap.invalidateSize();
        }, {once: true});
    }

    function confirmLocation() {
        var pos = fullMarker.getLatLng();
        document.getElementById(targetLatId).value = pos.lat.toFixed(8);
        document.getElementById(targetLngId).value = pos.lng.toFixed(8);
        bootstrap.Modal.getInstance(document.getElementById('fullScreenMapModal')).hide();
    }
</script>