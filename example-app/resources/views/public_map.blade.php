@extends('layouts.public')

@section('content')
@php
    /**
     * DETECT EMBED STATUS
     * Uses session persistence or URL parameter to show/hide the mode switcher.
     */
    $isEmbed = session('is_embedded', false) || request()->query('embed') === 'true';
@endphp

{{-- 
    EMBED NAVIGATION SWITCHER
    Visible only when embedded to allow toggling between Map and Directories.
--}}
@if($isEmbed)
<div class="bg-white border-b border-slate-100 px-4 py-3 no-print">
    <div class="max-w-7xl mx-auto flex items-center justify-center gap-6">
        <a href="{{ route('public.map') }}" 
           class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.map') ? 'text-[#a52a2a]' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="bi bi-map-fill"></i> Interactive Map
        </a>
        
        <div class="h-4 w-px bg-slate-200"></div>

        <a href="{{ route('public.schools') }}" 
           class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.schools') ? 'text-[#a52a2a]' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="bi bi-list-ul"></i> School Directories
        </a>
    </div>
</div>
@endif

<div class="max-w-6xl mx-auto px-4 relative {{ $isEmbed ? 'py-4' : 'py-10' }}">
    {{-- Friendly Header: Hidden if embedded to save vertical space --}}
    @if(!$isEmbed)
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Explore Schools</h2>
        <p class="text-slate-500 text-sm">Find and view school profiles across Zamboanga City</p>
    </div>
    @endif

    <div class="relative overflow-hidden rounded-3xl shadow-lg border border-slate-200">
        {{-- Floating Search Bar --}}
        <div class="absolute top-4 left-1/2 -translate-x-1/2 z-[1001] w-full max-w-sm px-4">
            <div class="relative">
                <input type="text" id="mapSearch" placeholder="Search for a school..." 
                       class="w-full bg-white border-none rounded-full py-3 pl-12 pr-10 shadow-md outline-none focus:ring-2 focus:ring-[#a52a2a]/50 text-sm font-medium">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-[#a52a2a]">
                    <i class="bi bi-search"></i>
                </div>
                <button id="clearSearch" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-[#a52a2a] hidden transition-colors">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
                
                {{-- Search Results Dropdown --}}
                <div id="searchResults" class="absolute top-full mt-2 w-full bg-white rounded-2xl shadow-xl border border-slate-100 hidden max-h-48 overflow-y-auto z-[1002]"></div>
            </div>
        </div>

        {{-- Map Container --}}
        <div id="publicMap" class="h-[600px] w-full z-10"></div>
        
        {{-- Map Legend --}}
        <div class="absolute bottom-4 left-4 z-[1000] bg-white/90 backdrop-blur p-3 rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-[#a52a2a] rounded-full border border-white shadow-sm"></div>
                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight">Registered School</span>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Initialize Map
    var map = L.map('publicMap', { zoomControl: false }).setView([6.9214, 122.0739], 12);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    
    var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Esri' });

    var baseMaps = { "Standard Map": streets, "Satellite View": satellite };
    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

    // 2. Marker Registry
    var markerRegistry = {}; 
    var allSchools = @json($schools);
    
    // Check if embed mode is active for link generation
    const isEmbedMode = {{ $isEmbed ? 'true' : 'false' }};

    @foreach($schools as $school)
        @if($school->latitude && $school->longitude)
            var marker = L.marker([{{ $school->latitude }}, {{ $school->longitude }}], {
                icon: L.divIcon({
                    html: `<div class="text-[#a52a2a]">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" class="drop-shadow-lg">
                                <path stroke="white" stroke-width="1.5" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                           </div>`,
                    iconSize: [28, 28], iconAnchor: [14, 28], className: 'custom-pin'
                })
            }).addTo(map)
            .bindPopup(`
                <div class="p-3 text-center min-w-[160px]">
                    <h4 class="font-bold text-slate-800 text-sm mb-0.5">{{ $school->name }}</h4>
                    <p class="text-[10px] text-slate-400 mb-3 font-medium uppercase">ID: {{ $school->school_id }}</p>
                    <a href="{{ route('public.view', ['id' => $school->id]) }}${isEmbedMode ? '?embed=true' : ''}" 
                       class="inline-block py-2 px-5 bg-[#a52a2a] !text-white rounded-full text-[10px] font-bold no-underline hover:bg-slate-900 transition-colors shadow-sm">
                        View Profile
                    </a>
                </div>
            `);
            markerRegistry['{{ $school->id }}'] = marker; 
        @endif
    @endforeach

    // 3. Search Logic
    const searchInput = document.getElementById('mapSearch');
    const resultsBox = document.getElementById('searchResults');
    const clearBtn = document.getElementById('clearSearch');

    searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        resultsBox.innerHTML = '';
        
        if (term.length > 0) {
            clearBtn.classList.remove('hidden');
            resultsBox.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
            resultsBox.classList.add('hidden');
            return;
        }

        if (term.length < 2) return;

        const filtered = allSchools.filter(s => 
            s.name.toLowerCase().includes(term) || s.school_id.toString().includes(term)
        ).slice(0, 5);

        if (filtered.length > 0) {
            filtered.forEach(school => {
                const div = document.createElement('div');
                div.className = "px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-none transition-colors";
                div.innerHTML = `
                    <p class="text-xs font-bold text-slate-700">${school.name}</p>
                    <p class="text-[9px] text-slate-400 uppercase">ID: ${school.school_id}</p>
                `;
                div.onclick = () => {
                    var targetMarker = markerRegistry[school.id];
                    if (targetMarker) {
                        map.flyTo(targetMarker.getLatLng(), 16, { duration: 1.5 }); 
                        targetMarker.openPopup();
                        resultsBox.classList.add('hidden');
                        searchInput.value = school.name;
                    }
                };
                resultsBox.appendChild(div);
            });
        } else {
            resultsBox.innerHTML = `<div class="p-4 text-center text-[10px] text-slate-400 font-bold uppercase">No schools found</div>`;
        }
    });

    clearBtn.onclick = () => {
        searchInput.value = '';
        resultsBox.classList.add('hidden');
        clearBtn.classList.add('hidden');
    };
</script>

<style>
    .leaflet-popup-content-wrapper { border-radius: 20px; padding: 0; overflow: hidden; }
    .leaflet-popup-content { margin: 0 !important; }
    .leaflet-popup-tip-container { display: none; }
    #searchResults::-webkit-scrollbar { width: 4px; }
    #searchResults::-webkit-scrollbar-thumb { background: #a52a2a; border-radius: 10px; }
</style>
@endsection