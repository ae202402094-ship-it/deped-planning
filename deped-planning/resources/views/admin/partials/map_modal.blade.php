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

    function openMapPopup(latId, lngId, currentLat, currentLng) {
        // Store the IDs of the inputs on the main form (e.g., 'lat' and 'lng')
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
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(fullMap);

                // Create the marker and enable dragging
                fullMarker = L.marker([startLat, startLng], {draggable: true}).addTo(fullMap);
                
                // Clicking on the map also moves the marker
                fullMap.on('click', function(e) { 
                    fullMarker.setLatLng(e.latlng); 
                });
            } else {
                // If map already exists, just move the view and marker to current school location
                fullMap.setView([startLat, startLng], 17);
                fullMarker.setLatLng([startLat, startLng]);
            }
            
            fullMap.invalidateSize();
        }, {once: true});
    }

    /**
     * FIX: This function now explicitly finds the main form inputs by ID
     */
    function confirmLocation() {
        if (!fullMarker) return;

        var selectedLatLng = fullMarker.getLatLng();
        console.log("Capturing:", selectedLatLng.lat, selectedLatLng.lng); // Debugging

        // 1. Find the inputs on the main Edit School form
        const latInput = document.getElementById(targetLatId);
        const lngInput = document.getElementById(targetLngId);

        if (latInput && lngInput) {
            // 2. Inject the values and force 8 decimal places for database precision
            latInput.value = selectedLatLng.lat.toFixed(8);
            lngInput.value = selectedLatLng.lng.toFixed(8);
            
            // 3. Close the modal
            var modalEl = document.getElementById('fullScreenMapModal');
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();

            // 4. Trigger the UI feedback if you have it
            if (window.showToast) {
                showToast("Registry Coordinates Synchronized");
            }
        } else {
            console.error("Critical Error: Target inputs '" + targetLatId + "' not found on main form.");
        }
    }
</script>