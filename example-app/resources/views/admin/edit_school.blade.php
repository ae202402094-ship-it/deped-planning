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
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Tap map to set school location</label>
                    <div id="pickerMap" class="h-64 rounded-3xl border-4 border-slate-50 shadow-inner"></div>
                </div>
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
@endsection