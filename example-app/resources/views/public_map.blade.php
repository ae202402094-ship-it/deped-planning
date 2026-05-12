@extends('layouts.public')

@section('content')
@php
    /**
     * DETECT EMBED STATUS
     */
    $isEmbed = session('is_embedded', false) || request()->query('embed') === 'true';

    /**
     * DYNAMIC DISTRICT COLOR MAPPING
     */
    $uniqueDistricts = $schools->pluck('district')->filter()->unique()->sort()->values();
    $palette = [
        '#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', 
        '#ec4899', '#06b6d4', '#f97316', '#84cc16', '#6366f1', 
        '#d946ef', '#14b8a6',
    ];
    
    $districtColors = [];
    foreach($uniqueDistricts as $index => $district) {
        $districtColors[$district] = $palette[$index % count($palette)];
    }
@endphp

{{-- EMBED NAVIGATION SWITCHER --}}
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

<div class="max-w-7xl mx-auto px-4 relative {{ $isEmbed ? 'py-4' : 'py-10' }}">
    
    {{-- Friendly Header --}}
    @if(!$isEmbed)
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Explore Schools</h2>
        <p class="text-slate-500 text-sm">Find and view school profiles across Zamboanga City</p>
    </div>
    @endif

    <div class="relative overflow-hidden rounded-[2.5rem] shadow-xl border border-slate-200">
        
        {{-- Floating Search Bar & Filters --}}
        <div class="absolute top-6 left-1/2 -translate-x-1/2 z-[1001] w-full max-w-4xl px-4 flex flex-col md:flex-row gap-3">
            
            {{-- Autocomplete Search --}}
            <div class="relative flex-1">
                <input type="text" id="mapSearch" placeholder="Search ID or Name..." 
                       class="w-full bg-white/95 backdrop-blur border border-slate-100 rounded-2xl py-3.5 pl-12 pr-10 shadow-lg outline-none focus:ring-2 focus:ring-[#a52a2a]/50 text-sm font-bold text-slate-700 transition-all">
                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-[#a52a2a]">
                    <i class="bi bi-search"></i>
                </div>
                <button id="clearSearch" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-[#a52a2a] hidden transition-colors">
                    <i class="bi bi-x-circle-fill text-lg"></i>
                </button>
                
                {{-- Dropdown Results --}}
                <div id="searchResults" class="absolute top-full mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 hidden max-h-60 overflow-y-auto z-[1002] custom-scrollbar"></div>
            </div>

            {{-- Interactive Map Filters --}}
            <div class="flex flex-wrap gap-2 justify-center">
                {{-- Sector Filter --}}
                <select id="mapSectorFilter" class="bg-white/95 backdrop-blur border border-slate-100 rounded-2xl py-3.5 px-4 shadow-lg outline-none focus:ring-2 focus:ring-[#a52a2a]/50 text-[10px] font-black uppercase tracking-widest text-slate-600 cursor-pointer flex-1 md:flex-none transition-all">
                    <option value="">All Sectors</option>
                    <option value="Public">Public Schools</option>
                    <option value="Private">Private Schools</option>
                </select>

                {{-- Level Filter --}}
                <select id="mapLevelFilter" class="bg-white/95 backdrop-blur border border-slate-100 rounded-2xl py-3.5 px-4 shadow-lg outline-none focus:ring-2 focus:ring-[#a52a2a]/50 text-[10px] font-black uppercase tracking-widest text-slate-600 cursor-pointer flex-1 md:flex-none transition-all">
                    <option value="">All Levels</option>
                    <option value="Primary">Primary (Elem)</option>
                    <option value="Secondary">Secondary (HS)</option>
                </select>

                {{-- District Filter --}}
                <select id="mapDistrictFilter" class="bg-white/95 backdrop-blur border border-slate-100 rounded-2xl py-3.5 px-4 shadow-lg outline-none focus:ring-2 focus:ring-[#a52a2a]/50 text-[10px] font-black uppercase tracking-widest text-slate-600 cursor-pointer flex-1 md:flex-none transition-all">
                    <option value="">All Districts</option>
                    @foreach($districtColors as $dist => $color)
                        <option value="{{ $dist }}">{{ $dist }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Map Container --}}
        <div id="publicMap" class="h-[700px] w-full z-10"></div>
        
        {{-- DYNAMIC COMPREHENSIVE LEGEND --}}
        <div class="absolute bottom-6 left-6 z-[1000] bg-white/95 backdrop-blur-md p-5 rounded-3xl shadow-xl border border-slate-100 min-w-[200px]">
            <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2 flex items-center gap-2">
                <i class="bi bi-geo-alt-fill text-[#a52a2a]"></i> Map Legend
            </h4>
            
            {{-- Sector Legend (Shapes) --}}
            <div class="mb-4">
                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Classification (Shape)</span>
                <div class="flex items-center gap-3 mb-1.5">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#64748b"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="3" fill="white"/></svg>
                    <span class="text-[10px] font-bold text-slate-700 uppercase tracking-tight">Public School</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#64748b"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><rect x="9.5" y="6.5" width="5" height="5" fill="white"/></svg>
                    <span class="text-[10px] font-bold text-slate-700 uppercase tracking-tight">Private School</span>
                </div>
            </div>

            {{-- District Legend (Colors) --}}
            <div>
                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mb-2 border-t border-slate-100 pt-2">Districts (Color)</span>
                <div class="flex flex-col gap-2.5 max-h-[25vh] overflow-y-auto pr-3 custom-scrollbar">
                    @foreach($districtColors as $dist => $color)
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full border border-white shadow-sm shrink-0" style="background-color: {{ $color }}"></div>
                        <span class="text-[9px] font-bold text-slate-600 uppercase tracking-tight truncate">{{ $dist }}</span>
                    </div>
                    @endforeach
                    
                    @if(count($districtColors) > 0)
                    <div class="h-px bg-slate-100 my-0.5"></div>
                    @endif
                    
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full border border-white shadow-sm bg-slate-400 shrink-0"></div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight truncate">Unassigned</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Pass PHP District Colors to JS
    const districtColors = @json($districtColors);
    const defaultColor = '#94a3b8'; // Slate-400 for unassigned schools

    // 1. Initialize Map
    var map = L.map('publicMap', { zoomControl: false }).setView([6.9214, 122.0739], 12);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    
    var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Esri' });

    var baseMaps = { "Standard Map": streets, "Satellite View": satellite };
    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

    // 2. Marker Registry with Metadata
    var markerRegistry = []; 
    var allSchools = @json($schools);
    const isEmbedMode = {{ $isEmbed ? 'true' : 'false' }};

    @foreach($schools as $school)
        @if($school->latitude && $school->longitude)
            
            // Determine Map Pin Attributes
            var schoolDistrict = '{{ $school->district }}';
            var schoolSector = '{{ $school->sector ?? 'Public' }}'; // Default to Public if null
            var pinColor = schoolDistrict && districtColors[schoolDistrict] ? districtColors[schoolDistrict] : defaultColor;
            
            // Public = Circle Inner Hole, Private = Square Inner Hole
            var innerShape = schoolSector === 'Private' 
                ? '<rect x="9" y="6.5" width="6" height="6" rx="1" fill="white"/>' 
                : '<circle cx="12" cy="9" r="3" fill="white"/>';

            var marker = L.marker([{{ $school->latitude }}, {{ $school->longitude }}], {
                icon: L.divIcon({
                    html: `<div style="color: ${pinColor}; transition: transform 0.2s; hover:scale-110">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" class="drop-shadow-lg filter drop-shadow-md">
                                <path stroke="white" stroke-width="1.5" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                ${innerShape}
                            </svg>
                           </div>`,
                    iconSize: [32, 32], iconAnchor: [16, 32], className: 'custom-pin'
                })
            }).bindPopup(`
                <div class="p-4 text-center min-w-[180px]">
                    <div class="flex justify-center gap-1 mb-3">
                        <span class="inline-block px-2 py-1 text-white text-[8px] font-black uppercase tracking-widest rounded-md shadow-sm" style="background-color: ${pinColor}">
                            {{ $school->sector ?? 'Public' }}
                        </span>
                        <span class="inline-block px-2 py-1 text-slate-600 bg-slate-100 text-[8px] font-black uppercase tracking-widest rounded-md shadow-sm border border-slate-200">
                            {{ $school->school_level ?? 'Unclassified' }}
                        </span>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ $school->district ?? 'No District' }}</p>
                    <h4 class="font-black text-slate-800 text-sm mb-1 leading-tight">{{ $school->name }}</h4>
                    <p class="text-[10px] text-slate-400 mb-4 font-bold uppercase tracking-widest">ID: {{ $school->school_id }}</p>
                    <a href="{{ route('public.view', ['id' => $school->id]) }}${isEmbedMode ? '?embed=true' : ''}" 
                       class="inline-block py-2.5 px-6 bg-slate-900 !text-white rounded-full text-[10px] font-black uppercase tracking-widest no-underline hover:bg-[#a52a2a] hover:shadow-lg transition-all shadow-md">
                        View Profile
                    </a>
                </div>
            `, { closeButton: false, offset: [0, -20] });
            
            marker.addTo(map);

            // Store marker with its attributes for filtering
            markerRegistry.push({
                id: '{{ $school->id }}',
                marker: marker,
                level: '{{ $school->school_level }}',
                sector: schoolSector,
                district: schoolDistrict,
                name: '{{ strtolower($school->name) }}',
                school_id: '{{ $school->school_id }}'
            });
        @endif
    @endforeach

    // 3. Elements and Variables
    const sectorFilter = document.getElementById('mapSectorFilter');
    const levelFilter = document.getElementById('mapLevelFilter');
    const districtFilter = document.getElementById('mapDistrictFilter');
    const searchInput = document.getElementById('mapSearch');
    const resultsBox = document.getElementById('searchResults');
    const clearBtn = document.getElementById('clearSearch');

    // 4. Filtering Logic (Handles Sector, Level, District, and specific typing)
    function applyMapFilters() {
        const term = searchInput.value.toLowerCase();
        const selectedSector = sectorFilter.value;
        const selectedLevel = levelFilter.value;
        const selectedDistrict = districtFilter.value;

        markerRegistry.forEach(item => {
            let matchesSearch = term === '' || item.name.includes(term) || item.school_id.includes(term);
            let matchesSector = selectedSector === '' || item.sector === selectedSector;
            let matchesLevel = selectedLevel === '' || item.level === selectedLevel;
            let matchesDistrict = selectedDistrict === '' || item.district === selectedDistrict;

            if (matchesSearch && matchesSector && matchesLevel && matchesDistrict) {
                if (!map.hasLayer(item.marker)) map.addLayer(item.marker);
            } else {
                if (map.hasLayer(item.marker)) map.removeLayer(item.marker);
            }
        });
    }

    sectorFilter.addEventListener('change', applyMapFilters);
    levelFilter.addEventListener('change', applyMapFilters);
    districtFilter.addEventListener('change', applyMapFilters);

    // 5. Autocomplete Dropdown Search Logic
    searchInput.addEventListener('input', (e) => {
        applyMapFilters(); // Instantly filter map pins while typing

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

        // Find matches based on filters + search term
        const selectedSector = sectorFilter.value;
        const selectedLevel = levelFilter.value;
        const selectedDistrict = districtFilter.value;

        const filtered = allSchools.filter(s => {
            let sSector = s.sector || 'Public';
            let matchesSearch = s.name.toLowerCase().includes(term) || s.school_id.toString().includes(term);
            let matchesSector = selectedSector === '' || sSector === selectedSector;
            let matchesLevel = selectedLevel === '' || s.school_level === selectedLevel;
            let matchesDistrict = selectedDistrict === '' || s.district === selectedDistrict;
            return matchesSearch && matchesSector && matchesLevel && matchesDistrict;
        }).slice(0, 5); // Max 5 results in dropdown

        if (filtered.length > 0) {
            filtered.forEach(school => {
                let sSector = school.sector || 'Public';
                let badgeColor = school.district && districtColors[school.district] ? districtColors[school.district] : defaultColor;
                
                const div = document.createElement('div');
                div.className = "px-5 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-none transition-colors flex items-center justify-between group";
                div.innerHTML = `
                    <div>
                        <p class="text-xs font-black text-slate-800 group-hover:text-[#a52a2a] transition-colors">${school.name}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                            ID: ${school.school_id} | ${sSector} | ${school.district || 'Unassigned'}
                        </p>
                    </div>
                    <div class="w-3 h-3 rounded-full shadow-sm" style="background-color: ${badgeColor}"></div>
                `;
                
                // When a specific school is clicked in the dropdown
                div.onclick = () => {
                    let foundItem = markerRegistry.find(m => m.id == school.id);
                    if (foundItem && foundItem.marker) {
                        map.flyTo(foundItem.marker.getLatLng(), 16, { duration: 1.5 }); 
                        foundItem.marker.openPopup();
                        resultsBox.classList.add('hidden');
                        searchInput.value = school.name;
                        applyMapFilters(); 
                    }
                };
                resultsBox.appendChild(div);
            });
        } else {
            resultsBox.innerHTML = `<div class="p-6 text-center text-[10px] text-slate-400 font-black uppercase tracking-widest">No matching schools found</div>`;
        }
    });

    // Clear search functionality
    clearBtn.onclick = () => {
        searchInput.value = '';
        resultsBox.classList.add('hidden');
        clearBtn.classList.add('hidden');
        applyMapFilters(); // Reset pins
        map.setView([6.9214, 122.0739], 12); // Reset view to center Zamboanga
    };
</script>

<style>
    /* Popup overrides for a cleaner look */
    .leaflet-popup-content-wrapper { border-radius: 24px; padding: 0; overflow: hidden; box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
    .leaflet-popup-content { margin: 0 !important; }
    .leaflet-popup-tip-container { display: none; }
    
    /* Custom Scrollbar for results and legend */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection