@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">School Management</h2>
        
        <form action="{{ route('admin.schools') }}" method="GET" class="w-full md:w-96 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or ID..." 
                   class="w-full border border-slate-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none shadow-sm">
            <button type="submit" style="background-color: #a52a2a;" class="text-white px-6 py-2 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-red-900 transition shadow-md">
                Search
            </button>
        </form>
    </div>

    {{-- REGISTRATION PANEL WITH MAP --}}
    <div class="bg-white rounded-[2rem] shadow-xl p-8 mb-10 border border-slate-200">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-2 h-6 bg-red-800 rounded-full"></div>
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em]">Register New School Profile</h3>
        </div>
        
        <form action="{{ route('schools.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            {{-- Form Inputs --}}
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">School ID</label>
                    <input type="text" name="school_id" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">School Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm outline-none">
                </div>
                
                {{-- Hidden/Readonly Coordinate Inputs --}}
                <input type="hidden" name="latitude" id="reg_lat" value="6.9214">
                <input type="hidden" name="longitude" id="reg_lng" value="122.0739">

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Teachers</label>
                    <input type="number" name="no_of_teachers" value="0" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Enrollees</label>
                    <input type="number" name="no_of_enrollees" value="0" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm">
                </div>
            </div>

            {{-- Registration Mini-Map --}}
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase block text-center">Set Location (Tap Map)</label>
                <div id="regMap" class="h-48 rounded-2xl border-2 border-slate-100 shadow-inner"></div>
            </div>
            
            <button type="submit" style="background-color: #a52a2a;" class="text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-red-900 transition lg:col-span-full py-4 shadow-lg">
                Add School to Registry
            </button>
        </form>
    </div>

    {{-- SCHOOLS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($schools as $school)
            <a href="{{ route('schools.edit', $school->id) }}" class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">ID: {{ $school->school_id }}</span>
                        <span class="text-red-700 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-black uppercase tracking-widest">Edit Profile →</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase leading-tight mb-6 group-hover:text-red-800 transition-colors">{{ $school->name }}</h3>
                </div>
                <div class="mt-auto bg-slate-50 p-4 text-center border-t border-slate-100 group-hover:bg-red-50 transition-colors">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] group-hover:text-red-700">Open Official Census Data</span>
                </div>
            </a>
        @endforeach
    </div>
</div>

{{-- Leaflet Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize Registration Map
    var regMap = L.map('regMap').setView([6.9214, 122.0739], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(regMap);

    var regMarker = L.marker([6.9214, 122.0739], {draggable: true}).addTo(regMap);

    function updateCoords(lat, lng) {
        document.getElementById('reg_lat').value = lat.toFixed(8);
        document.getElementById('reg_lng').value = lng.toFixed(8);
    }

    regMap.on('click', function(e) {
        regMarker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
    });

    regMarker.on('dragend', function(e) {
        updateCoords(regMarker.getLatLng().lat, regMarker.getLatLng().lng);
    });
</script>
@endsection