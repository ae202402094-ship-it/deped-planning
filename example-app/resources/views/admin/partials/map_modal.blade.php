<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Prevent Bootstrap from over-styling your global links */
    a {
        color: inherit !important;
        text-decoration: none !important;
    }

    /* Keep the modal's internal buttons looking correct */
    .modal-content a {
        color: #0d6efd;
        text-decoration: underline;
    }

    /* FIX: High Z-Index ensures the modal is above all other UI elements */
    #fullScreenMapModal {
        z-index: 2050 !important;
    }
    .modal-backdrop {
        z-index: 2040 !important;
    }
    
    /* Ensure Leaflet UI controls stay within the map layer */
    .leaflet-control-container {
        z-index: 1000 !important;
    }

    /* Fullscreen Modal Styling */
    .modal-fullscreen .modal-content {
        border-radius: 0;
    }
</style>

<div class="modal fade" id="fullScreenMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="modal-header border-0" style="background-color: #1e293b; color: white;">
                <h5 class="modal-title fw-bold text-uppercase tracking-wider" style="font-size: 0.8rem;">Pinpoint School Location</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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

    /**
     * Initializes and shows the map modal
     */
    function openMapPopup(latId, lngId, currentLat, currentLng) {
        targetLatId = latId;
        targetLngId = lngId;
        
        let startLat = parseFloat(currentLat) || 6.9214; 
        let startLng = parseFloat(currentLng) || 122.0739;

        var modalElement = document.getElementById('fullScreenMapModal');
        var modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Wait for modal to be fully visible before loading the map
        modalElement.addEventListener('shown.bs.modal', function() {
            if (!fullMap) {
                fullMap = L.map('fullMap').setView([startLat, startLng], 17);
                
                // Street Layer
                var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(fullMap);

                // Satellite Layer (High-detail for Zamboanga)
                var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri'
                });

                L.control.layers({ "Streets": streets, "Satellite": satellite }).addTo(fullMap);

                // Initialize Draggable Marker
                fullMarker = L.marker([startLat, startLng], {draggable: true}).addTo(fullMap);
                
                // Allow clicking anywhere on map to move the marker
                fullMap.on('click', function(e) { 
                    fullMarker.setLatLng(e.latlng); 
                });
            } else {
                fullMap.setView([startLat, startLng], 17);
                fullMarker.setLatLng([startLat, startLng]);
            }
            
            // Fix Leaflet tile rendering issues inside modals
            fullMap.invalidateSize();
        }, {once: true});
    }

    /**
     * Captures coordinates and updates the form
     */
    function confirmLocation() {
        var selectedLatLng = fullMarker.getLatLng();

        // Inject high-precision coordinates into the original form
        if (targetLatId && targetLngId) {
            document.getElementById(targetLatId).value = selectedLatLng.lat.toFixed(8);
            document.getElementById(targetLngId).value = selectedLatLng.lng.toFixed(8);
            
            // Close modal programmatically
            var modalEl = document.getElementById('fullScreenMapModal');
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }

            // Trigger visual feedback toast if available
            if (window.showToast) {
                showToast("Location Captured Successfully");
            }
        }
    }
</script>