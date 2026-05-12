@extends(auth()->user()->role === 'super_admin' ? 'layouts.super_admin' : 'layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 relative">
    <div class="mb-8 flex justify-between items-end border-b border-slate-100 pb-6">
        <div>
            <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter italic">Geographic Registry</h2>
            <p class="text-slate-500 font-mono text-[10px] uppercase tracking-[0.3em] mt-1">Division of Zamboanga City Administration</p>
        </div>
        <div class="text-right">
            <span class="text-[10px] font-black text-red-800 uppercase tracking-widest">Live Database Sync</span>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Total Schools Pinned: {{ count($schools) }}</p>
        </div>
    </div>

    <div class="relative group">
        {{-- Floating Search Bar Overlay --}}
        <div class="absolute top-6 left-1/2 -translate-x-1/2 z-[1001] w-full max-w-md px-4">
            <div class="relative group/search">
                <input type="text" id="mapSearch" placeholder="Quick find school name or ID..." 
                       class="w-full bg-white/95 backdrop-blur-md border border-slate-200 rounded-2xl py-4 pl-12 pr-12 shadow-2xl outline-none focus:ring-2 focus:ring-red-800 transition-all font-bold text-sm uppercase tracking-tight">
                
                {{-- Search Icon --}}
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                {{-- Clear Button (X) --}}
                <button id="clearSearch" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-800 hidden transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- Dropdown Results --}}
                <div id="searchResults" class="absolute top-full mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 hidden max-h-60 overflow-y-auto z-[1002]">
                    </div>
            </div>
        </div>

        {{-- Map Container --}}
        <div id="adminMap" class="h-[650px] w-full rounded-[3rem] shadow-2xl border border-slate-200 overflow-hidden z-10"></div>
        
        {{-- Map Legend / Controls --}}
        <div class="absolute bottom-6 right-6 z-[1000] space-y-2">
            <div class="bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-slate-200">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Admin Tools</p>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 bg-red-800 rounded-full"></div>
                    <span class="text-[9px] font-bold text-slate-700 uppercase">Interactive Marker</span>
                </div>
                <button onclick="map.setView([6.9214, 122.0739], 12)" class="text-[8px] font-black text-red-800 uppercase tracking-tighter hover:underline">Reset Map View</button>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Initialize Map
    var map = L.map('adminMap').setView([6.9214, 122.0739], 12);
    
    var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    });

    L.control.layers({ "Streets": streets, "Satellite": satellite }).addTo(map);

    // 2. Marker Registry & Data Loading
    var markerRegistry = {}; 
    var allSchools = @json($schools);

    @foreach($schools as $school)
        @if($school->latitude && $school->longitude)
            var marker = L.marker([{{ $school->latitude }}, {{ $school->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-3 text-center min-w-[150px]">
                        <h4 class="font-black text-slate-800 uppercase text-xs mb-1 tracking-tight">{{ $school->name }}</h4>
                        <p class="text-[9px] text-slate-400 mb-4 uppercase font-mono">ID: {{ $school->school_id }}</p>
                        <a href="{{ route('schools.edit', $school->id) }}" 
                           class="inline-block w-full py-2 bg-slate-800 text-white rounded-lg text-[9px] font-black uppercase tracking-widest no-underline hover:bg-red-800 transition">
                           Edit Registry
                        </a>
                    </div>
                `);
            
            markerRegistry['{{ $school->id }}'] = marker; 
        @endif
    @endforeach

    // 3. Advanced Search Engine
    const searchInput = document.getElementById('mapSearch');
    const resultsBox = document.getElementById('searchResults');
    const clearBtn = document.getElementById('clearSearch');

    searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        resultsBox.innerHTML = '';
        
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
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black text-slate-800 uppercase group-hover:text-red-800">${school.name}</p>
                            <p class="text-[8px] font-mono text-slate-400 mt-1 uppercase">ID: ${school.school_id}</p>
                        </div>
                        <span class="text-[8px] font-black text-slate-300 uppercase opacity-0 group-hover:opacity-100 transition-opacity tracking-widest italic">Fly To →</span>
                    </div>
                `;
                
                div.onclick = () => {
                    var targetMarker = markerRegistry[school.id];
                    if (targetMarker) {
                        map.flyTo(targetMarker.getLatLng(), 16, { duration: 1.5 }); // Smooth zoom
                        targetMarker.openPopup();
                        resultsBox.classList.add('hidden');
                        searchInput.value = '';
                        clearBtn.classList.add('hidden');
                    }
                };
                resultsBox.appendChild(div);
            });
        } else {
            // No Results Found UI
            resultsBox.classList.remove('hidden');
            resultsBox.innerHTML = `
                <div class="p-8 text-center bg-slate-50/50">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">No Registry Found</p>
                    <p class="text-[8px] text-slate-300 mt-1 uppercase">Verify School ID or Name</p>
                </div>
            `;
        }
    });

    // Clear Button Logic
    clearBtn.onclick = () => {
        searchInput.value = '';
        resultsBox.innerHTML = '';
        resultsBox.classList.add('hidden');
        clearBtn.classList.add('hidden');
        searchInput.focus();
    };

    // Close results when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.add('hidden');
        }
    });
</script>
@endsection