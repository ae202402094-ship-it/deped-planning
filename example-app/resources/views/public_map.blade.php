@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 relative">
    <div class="text-center mb-8">
        <h2 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">Interactive School Map</h2>
        <p class="text-slate-500 font-mono text-xs uppercase tracking-[0.3em] mt-2">Division of Zamboanga City</p>
    </div>

    <div class="relative group">
       {{-- Floating Search Bar Overlay --}}
<div class="absolute top-6 left-1/2 -translate-x-1/2 z-[1001] w-full max-w-md px-4">
    <div class="relative group/search">
        {{-- Search Icon --}}
        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <input type="text" id="mapSearch" placeholder="Search school name or ID..." 
               class="w-full bg-white/90 backdrop-blur-md border border-slate-200 rounded-2xl py-4 pl-12 pr-12 shadow-2xl outline-none focus:ring-2 focus:ring-red-800 transition-all font-bold text-sm uppercase tracking-tight">
        
        {{-- FIXED: Clear Button (X) --}}
        <button id="clearSearch" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-800 hidden transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Dropdown Results --}}
        <div id="searchResults" class="absolute top-full mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 hidden max-h-60 overflow-y-auto z-[1002]"></div>
    </div>
</div>

        {{-- Map Container --}}
        <div id="publicMap" class="h-[650px] w-full rounded-[3rem] shadow-2xl border-4 border-white overflow-hidden z-10"></div>
        
        {{-- Map Legend --}}
        <div class="absolute bottom-6 left-6 z-[1000] bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-slate-200">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Map Legend</p>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full shadow-sm"></div>
                <span class="text-[10px] font-bold text-slate-700 uppercase">Registered School</span>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize map centered on Zamboanga City
    var map = L.map('publicMap').setView([6.9214, 122.0739], 12);
    
    // Street Layer
    var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Satellite Layer (Esri)
    var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    });

    // Layer Toggle
    L.control.layers({ "Streets": streets, "Satellite": satellite }).addTo(map);

    // --- Search Logic & Marker Registry ---
    var markerRegistry = {}; 
    var allSchools = @json($schools);

    @foreach($schools as $school)
        @if($school->latitude && $school->longitude)
            var marker = L.marker([{{ $school->latitude }}, {{ $school->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2 text-center">
                        <h4 class="font-black text-slate-800 uppercase text-xs mb-1">{{ $school->name }}</h4>
                        <p class="text-[9px] text-slate-400 mb-3 uppercase tracking-tighter">ID: {{ $school->school_id }}</p>
                        <a href="{{ route('public.view', ['id' => $school->id]) }}" 
                           class="inline-block py-2 px-4 bg-red-800 text-white rounded-lg text-[9px] font-black uppercase tracking-widest no-underline hover:bg-black transition">
                           View Profile
                        </a>
                    </div>
                `);
            
            // Map the internal database ID to the marker object
            markerRegistry['{{ $school->id }}'] = marker; 
        @endif
    @endforeach

    const searchInput = document.getElementById('mapSearch');
    const resultsBox = document.getElementById('searchResults');

    searchInput.addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    resultsBox.innerHTML = '';
    
    // Toggle the Clear (X) button visibility
    if (term.length > 0) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
        resultsBox.classList.add('hidden');
        return;
    }

    if (term.length < 2) return;

    const filtered = allSchools.filter(s => 
        s.name.toLowerCase().includes(term) || s.school_id.toString().includes(term)
    ).slice(0, 6);

    if (filtered.length > 0) {
        resultsBox.classList.remove('hidden');
        filtered.forEach(school => {
            const div = document.createElement('div');
            div.className = "p-4 hover:bg-red-50 cursor-pointer border-b border-slate-50 last:border-none transition-colors group";
            div.innerHTML = `
                <p class="text-[10px] font-black text-slate-800 uppercase group-hover:text-red-800">${school.name}</p>
                <p class="text-[8px] font-mono text-slate-400 mt-1">ID: ${school.school_id}</p>
            `;
            
            div.onclick = () => {
                var targetMarker = markerRegistry[school.id];
                if (targetMarker) {
                    map.flyTo(targetMarker.getLatLng(), 16, { duration: 1.5 }); 
                    targetMarker.openPopup();
                    resultsBox.classList.add('hidden');
                    searchInput.value = '';
                    clearBtn.classList.add('hidden');
                }
            };
            resultsBox.appendChild(div);
        });
    } else {
        // FIXED: Show "No Results" state
        resultsBox.classList.remove('hidden');
        resultsBox.innerHTML = `
            <div class="p-8 text-center">
                <div class="inline-flex p-3 bg-slate-50 rounded-full mb-3">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No School Found</p>
                <p class="text-[8px] text-slate-300 mt-1 uppercase">Try a different name or ID</p>
            </div>
        `;
    }
});
    // Close results when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.add('hidden');
        }
    });
    const clearBtn = document.getElementById('clearSearch');

searchInput.addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    
    // Show or hide the clear button based on input length
    if (term.length > 0) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }

    // ... (rest of your existing search filtering logic) ...
});

// Logic to clear the search
clearBtn.onclick = () => {
    searchInput.value = '';
    resultsBox.innerHTML = '';
    resultsBox.classList.add('hidden');
    clearBtn.classList.add('hidden');
    searchInput.focus();
};
</script>
@endsection