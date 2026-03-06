@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200">
        <div style="background-color: #a52a2a;" class="p-10 text-white">
            <h1 class="text-3xl font-black uppercase tracking-tighter">{{ $school->name }}</h1>
            <p class="opacity-70 font-mono text-sm uppercase">Verification & Location Update</p>
        </div>

        <form id="updateForm" action="{{ route('schools.update', $school->id) }}" method="POST" class="p-10 space-y-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b pb-2">Physical Inventory</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="no_of_teachers" id="new_teachers" value="{{ $school->no_of_teachers }}" class="bg-slate-50 p-4 rounded-2xl font-bold">
                        <input type="number" name="no_of_enrollees" id="new_enrollees" value="{{ $school->no_of_enrollees }}" class="bg-slate-50 p-4 rounded-2xl font-bold">
                        <input type="number" name="no_of_classrooms" id="new_classrooms" value="{{ $school->no_of_classrooms }}" class="bg-slate-50 p-4 rounded-2xl font-bold">
                        <input type="number" name="no_of_toilets" id="new_toilets" value="{{ $school->no_of_toilets }}" class="bg-slate-50 p-4 rounded-2xl font-bold">
                    </div>

                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b pb-2 pt-4">Coordinates</h3>
                    <div class="flex gap-4">
                        <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" readonly class="w-full bg-slate-100 p-3 rounded-xl text-xs font-mono">
                        <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" readonly class="w-full bg-slate-100 p-3 rounded-xl text-xs font-mono">
                    </div>
                </div>

                <div class="space-y-4">
    <div class="flex justify-between items-center">
        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">
            Tap map to set school location
        </label>
        <button type="button" onclick="toggleMapSize()" id="expandBtn" 
                class="text-[9px] font-black uppercase bg-slate-100 px-3 py-1 rounded-lg hover:bg-slate-200 transition">
            ⤢ Enlarge Map
        </button>
    </div>

    <div id="pickerMap" class="h-64 w-full rounded-3xl border-4 border-slate-50 shadow-inner transition-all duration-500 ease-in-out"></div>
</div>

            <button type="button" onclick="openReviewModal()" class="w-full py-6 rounded-3xl text-white font-black uppercase tracking-widest shadow-xl hover:bg-red-900 transition" style="background-color: #a52a2a;">
                Review & Commit Changes
            </button>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize Picker Map
    // Center on Zamboanga City or existing school location
    var initialLat = {{ $school->latitude ?? 6.9214 }};
    var initialLng = {{ $school->longitude ?? 122.0739 }};
    
    var map = L.map('pickerMap').setView([initialLat, initialLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);

    // Update inputs when marker is dragged
    marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        document.getElementById('lat').value = pos.lat.toFixed(8);
        document.getElementById('lng').value = pos.lng.toFixed(8);
    });

    // Update inputs when map is clicked
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('lat').value = e.latlng.lat.toFixed(8);
        document.getElementById('lng').value = e.latlng.lng.toFixed(8);
    });
</script>

<div id="reviewModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl">
        <div class="p-8 text-center bg-slate-50 border-b">
            <h2 class="text-2xl font-black text-slate-800 uppercase italic">Confirm Audit Update</h2>
        </div>
        <div class="p-8 space-y-4" id="modalContent"></div>
        <div class="p-8 pt-0 grid grid-cols-2 gap-4">
            <button type="button" onclick="closeReviewModal()" class="py-4 rounded-2xl bg-slate-100 font-bold uppercase text-[10px]">Cancel</button>
            <button type="button" onclick="document.getElementById('updateForm').submit()" style="background-color: #a52a2a;" class="py-4 rounded-2xl text-white font-bold uppercase text-[10px]">Confirm Save</button>
        </div>
    </div>
</div>

<script>
function openReviewModal() {
    const teachers = document.getElementById('new_teachers').value;
    const enrollees = document.getElementById('new_enrollees').value;
    const classrooms = document.getElementById('new_classrooms').value;
    const toilets = document.getElementById('new_toilets').value;
    const lat = document.getElementById('lat').value;
    const lng = document.getElementById('lng').value;

    document.getElementById('modalContent').innerHTML = `
        <div class="text-sm font-bold text-slate-600 space-y-2">
            <p>Teachers: ${teachers}</p>
            <p>Enrollees: ${enrollees}</p>
            <p>Classrooms: ${classrooms}</p>
            <p>Toilets: ${toilets}</p>
            <p class="text-[10px] font-mono text-slate-400">Location: ${lat}, ${lng}</p>
        </div>
    `;
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

let isExpanded = false;

function toggleMapSize() {
    const mapContainer = document.getElementById('pickerMap');
    const btn = document.getElementById('expandBtn');
    
    if (!isExpanded) {
        // Enlarge: Increase height significantly
        mapContainer.style.height = "600px";
        btn.innerText = "Collapse Map ⤡";
        isExpanded = true;
    } else {
        // Shrink: Return to original height
        mapContainer.style.height = "256px"; // matches h-64
        btn.innerText = "⤢ Enlarge Map";
        isExpanded = false;
    }
    
    // IMPORTANT: Tell Leaflet the container size changed
    setTimeout(() => {
        map.invalidateSize();
        // Smoothly pan back to the current marker
        map.panTo(marker.getLatLng());
    }, 500); // Wait for CSS transition to finish
}
</script>
@endsection