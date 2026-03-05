@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-black text-slate-800 uppercase mb-6">Division School Map</h2>
    
    <div id="schoolMap" class="w-full h-[600px] rounded-[2rem] shadow-2xl border-4 border-white"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize map centered on Zamboanga City
    var map = L.map('schoolMap').setView([6.9214, 122.0739], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Dynamic Markers from PHP
    var schools = @json($schools);
    
    schools.forEach(function(school) {
        if(school.latitude && school.longitude) {
            var marker = L.marker([school.latitude, school.longitude]).addTo(map);
            
            // Professional Popup with "Tap to View"
            marker.bindPopup(`
                <div class="p-2">
                    <h3 class="font-black uppercase text-slate-800">${school.name}</h3>
                    <p class="text-[10px] text-slate-400 font-bold mb-2">ID: ${school.school_id}</p>
                    <a href="/admin/schools/${school.id}/edit" 
                       style="background-color: #a52a2a;" 
                       class="block text-center text-white text-[10px] font-bold py-2 rounded-lg uppercase tracking-widest no-underline">
                        View Profile
                    </a>
                </div>
            `);
        }
    });
</script>
@endsection