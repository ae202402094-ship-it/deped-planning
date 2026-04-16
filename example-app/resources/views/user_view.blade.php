@extends('layouts.public')

@section('content')
@php
    $teacherLearnerRatio = $school->no_of_teachers > 0 ? "1 : " . round($school->no_of_enrollees / $school->no_of_teachers) : "0 : 0";
    $classroomLearnerRatio = $school->no_of_classrooms > 0 ? "1 : " . round($school->no_of_enrollees / $school->no_of_classrooms) : "0 : 0";
    $chairLearnerRatio = ($school->no_of_chairs > 0) ? "1 : " . round($school->no_of_enrollees / $school->no_of_chairs, 1) : "0 : 0";
    $rawChairRatio = ($school->no_of_chairs > 0) ? ($school->no_of_enrollees / $school->no_of_chairs) : 0;
    $rawClassroomRatio = $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : 0;
    $rawTeacherRatio = $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : 0;

    $isHighRisk = ($school->hazard_level === 'High');
    $statusLabel = $isHighRisk ? 'Critical Risk' : ($rawClassroomRatio > 50 ? 'Overcrowded' : 'Standard');
    $statusColor = $isHighRisk ? 'text-red-600 bg-red-50 border-red-100' : ($rawClassroomRatio > 50 ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100');
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .map-pulse {
        border-radius: 50%;
        height: 28px;
        width: 28px;
        position: absolute;
        background: rgba(165, 42, 42, 0.5);
        animation: pulsate 2s ease-out infinite;
        opacity: 0;
    }
    @keyframes pulsate {
        0% { transform: scale(0.1, 0.1); opacity: 0; }
        50% { opacity: 1.0; }
        100% { transform: scale(1.2, 1.2); opacity: 0; }
    }
    .leaflet-popup-content-wrapper { border-radius: 12px; padding: 5px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
    .no-print { @media print { display: none !important; } }
    html { scroll-behavior: smooth; }
</style>

<div class="min-h-screen bg-[#f8fafc] py-10 px-4 sm:px-6 lg:px-8 text-base font-sans">
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- 1. Header Card --}}
        <header class="relative bg-white p-8 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-[#a52a2a]"></div>
            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pl-2">
                <div class="space-y-2">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-md shadow-sm">Institutional Profile</span>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Registry Cycle 2026</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">{{ $school->name }}</h1>
                    <div class="flex items-center gap-4 text-sm font-medium text-slate-500 mt-2">
                        <span class="flex items-center gap-1.5"><i data-lucide="fingerprint" class="w-4 h-4 text-slate-400"></i> ID: <span class="text-slate-700 font-bold">{{ $school->school_id }}</span></span>
                        <span class="w-1.5 h-1.5 bg-slate-300 rounded-full"></span>
                        <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i> Zamboanga Division</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-3 no-print">
                    <button onclick="window.print()" class="bg-[#a52a2a] text-white px-6 py-4 rounded-xl font-black uppercase text-sm tracking-widest shadow-md hover:bg-black hover:shadow-lg transition-all flex items-center group">
                        <i data-lucide="printer" class="w-5 h-5 inline-block mr-3 group-hover:-translate-y-0.5 transition-transform"></i> Print Profile
                    </button>
                </div>
            </div>
        </header>

        {{-- 2. Top-Level Metrics Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6">
            @foreach([
                ['label' => 'Teachers', 'value' => $school->no_of_teachers, 'icon' => 'users-round', 'trend' => 'Faculty'],
                ['label' => 'Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'graduation-cap', 'trend' => 'Learners'],
                ['label' => 'Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'door-open', 'trend' => 'Spaces'],
                ['label' => 'Toilets', 'value' => $school->no_of_toilets, 'icon' => 'toilet', 'trend' => 'Hygiene'],
                ['label' => 'Chairs', 'value' => $school->no_of_chairs, 'icon' => 'armchair', 'trend' => 'Seats'],
            ] as $metric)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 group hover:border-[#a52a2a]/30 hover:shadow-md transition-all relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 group-hover:scale-110 transition-all duration-500 pointer-events-none">
                    <i data-lucide="{{ $metric['icon'] }}" class="w-28 h-28 text-[#a52a2a]"></i>
                </div>
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-3 bg-slate-50 rounded-xl group-hover:bg-[#a52a2a] group-hover:text-white transition-colors border border-slate-100 group-hover:border-[#a52a2a] shadow-sm">
                        <i data-lucide="{{ $metric['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $metric['trend'] }}</span>
                </div>
                <h3 class="text-3xl font-black text-slate-900 mb-1 tabular-nums relative z-10">{{ number_format($metric['value']) }}</h3>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest leading-none mt-2 relative z-10">{{ $metric['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- 3. Main Split Layout (Map on Left, Data on Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- LEFT COLUMN: Enlarged Sticky Geospatial Map --}}
            <div class="lg:col-span-6 xl:col-span-5 lg:sticky lg:top-24 space-y-6">
                {{-- Increased Map Height to 820px on Large Screens --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-[550px] lg:h-[820px]">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-[#a52a2a] rounded-full"></div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest">Geospatial Intelligence</h2>
                        </div>
                    </div>
                    <div class="relative flex-grow w-full h-full bg-slate-100">
                        <div id="schoolMap" class="absolute inset-0 w-full h-full z-0"></div>
                        
                        {{-- Map Controls --}}
                        <div class="absolute bottom-6 left-6 z-[1000] no-print">
                            <div class="bg-white/95 backdrop-blur-md p-1.5 rounded-xl shadow-xl border border-slate-200 flex flex-col gap-1.5">
                                <button id="setVoyager" class="px-5 py-3 text-xs font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all shadow-md">Street</button>
                                <button id="setSatellite" class="px-5 py-3 text-xs font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all">Satellite</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Scrollable Analytics & Data --}}
            <div class="lg:col-span-6 xl:col-span-7 space-y-6 lg:space-y-8">
                
                {{-- KPI Section styled to match the DepEd Theme Header --}}
                <div class="bg-gradient-to-br from-[#8a2222] to-[#a52a2a] p-8 md:p-10 rounded-3xl text-white shadow-xl relative overflow-hidden border border-[#7a1d1d]">
                    {{-- Decorative Background Icon --}}
                    <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none mix-blend-overlay">
                        <i data-lucide="bar-chart-2" class="w-72 h-72 text-white"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-white/90 mb-8 border-b border-white/20 pb-5 flex items-center gap-3">
                            <i data-lucide="activity" class="w-5 h-5 text-amber-400"></i> Operational Capacity Ratios
                        </h3>
                        <div class="space-y-10">
                            @foreach([
                                ['label' => 'Room Saturation (Learners per Classroom)', 'ratio' => $classroomLearnerRatio, 'perc' => ($rawClassroomRatio / 60) * 100],
                                ['label' => 'Instructional Load (Learners per Teacher)', 'ratio' => $teacherLearnerRatio, 'perc' => ($rawTeacherRatio / 50) * 100],
                            ] as $analytic)
                            <div class="space-y-4">
                                <div class="flex justify-between items-end">
                                    <span class="text-xs font-bold uppercase tracking-widest text-white/80 drop-shadow-sm">{{ $analytic['label'] }}</span>
                                    <span class="text-3xl font-black text-amber-400 tabular-nums drop-shadow-md">{{ $analytic['ratio'] }}</span>
                                </div>
                                {{-- Progress bar track matches dark theme, fill uses high-contrast amber --}}
                                <div class="h-3 w-full bg-black/20 rounded-full overflow-hidden shadow-inner border border-black/10">
                                    <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-400 rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(251,191,36,0.5)]" style="width: {{ min($analytic['perc'], 100) }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Facility and Utilities (2-Column Grid inside the right side) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    {{-- Deficit Audit --}}
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm flex flex-col h-full hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3 border-b border-slate-100 pb-4 shrink-0">
                            <i data-lucide="package-search" class="w-5 h-5 text-[#a52a2a]"></i> Resource Deficit
                        </h3>
                        <div class="space-y-6 flex-grow flex flex-col justify-center">
                            @foreach([
                                ['label' => 'Classroom Units', 'val' => $school->classroom_shortage],
                                ['label' => 'Furniture/Chairs', 'val' => $school->chair_shortage],
                                ['label' => 'Sanitation Units', 'val' => $school->toilet_shortage]
                            ] as $short)
                            <div class="flex items-center justify-between group">
                                <span class="text-sm font-semibold text-slate-600 group-hover:text-slate-900 transition-colors">{{ $short['label'] }}</span>
                                <span class="text-xl font-black {{ ($short['val'] ?? 0) > 0 ? 'text-red-700 bg-red-50 ring-1 ring-red-100' : 'text-emerald-700 bg-emerald-50 ring-1 ring-emerald-100' }} px-4 py-1.5 rounded-xl shadow-sm">
                                    {{ number_format($short['val'] ?? 0) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Utility Provisioning --}}
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm flex flex-col h-full hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3 border-b border-slate-100 pb-4 shrink-0">
                            <i data-lucide="plug-zap" class="w-5 h-5 text-amber-500"></i> Provisioning
                        </h3>
                        <div class="space-y-4 flex-grow flex flex-col justify-center">
                            
                            {{-- Power Supply Row --}}
                            <div class="flex items-center justify-between p-4 rounded-xl border transition-colors {{ $school->with_electricity != 'None' ? 'border-emerald-100 bg-emerald-50/50 hover:bg-emerald-50' : 'border-red-100 bg-red-50/50 hover:bg-red-50' }}">
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Power</span>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-black uppercase {{ in_array($school->with_electricity, ['Grid Connection', 'Hybrid']) ? 'text-emerald-700' : 'text-amber-600' }}">
                                        @if(in_array($school->with_electricity, ['Grid Connection', 'Hybrid']))
                                            On-Grid
                                        @elseif(in_array($school->with_electricity, ['Solar Powered', 'Generator']))
                                            Off-Grid
                                        @else
                                            No Power
                                        @endif
                                    </span>
                                    <span class="text-xs font-bold text-slate-400 mt-0.5">{{ $school->with_electricity }}</span>
                                </div>
                            </div>

                            {{-- Water Row --}}
                            <div class="flex items-center justify-between p-4 rounded-xl border transition-colors {{ $school->with_potable_water ? 'border-emerald-100 bg-emerald-50/50 hover:bg-emerald-50' : 'border-red-100 bg-red-50/50 hover:bg-red-50' }}">
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Water</span>
                                <span class="text-sm font-black uppercase {{ $school->with_potable_water ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $school->with_potable_water ? 'Yes' : 'No' }}
                                </span>
                            </div>

                            {{-- Internet Row --}}
                            <div class="flex items-center justify-between p-4 rounded-xl border transition-colors {{ $school->with_internet ? 'border-emerald-100 bg-emerald-50/50 hover:bg-emerald-50' : 'border-red-100 bg-red-50/50 hover:bg-red-50' }}">
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Data</span>
                                <span class="text-sm font-black uppercase {{ $school->with_internet ? 'text-emerald-700' : 'text-red-700' }} text-right">
                                    {{ $school->with_internet ? 'Yes' : 'No' }}
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Risk Management --}}
                <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-[#a52a2a]"></i> Risk & Vulnerability Profile
                    </h3>
                    <div class="p-6 bg-slate-50 rounded-2xl border-l-4 border-[#a52a2a]">
                        <p class="text-lg font-black text-slate-900 uppercase mb-2">{{ $school->hazard_type ?: 'Secured Site' }}</p>
                        <p class="text-base text-slate-600 leading-relaxed italic">
                            {{ $school->hazards ?: 'No high-priority technical hazards or environmental risks have been identified at this location during the current review cycle.' }}
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        const lat = {{ $school->latitude }};
        const lng = {{ $school->longitude }};
        
        const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: 'CARTO' });
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Esri' });

        const map = L.map('schoolMap', { scrollWheelZoom: false, layers: [voyager] }).setView([lat, lng], 17);
        
        document.getElementById('setVoyager').addEventListener('click', function() {
            map.removeLayer(satellite); map.addLayer(voyager);
            this.className = 'px-5 py-3 text-xs font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all shadow-md';
            document.getElementById('setSatellite').className = 'px-5 py-3 text-xs font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        document.getElementById('setSatellite').addEventListener('click', function() {
            map.removeLayer(voyager); map.addLayer(satellite);
            this.className = 'px-5 py-3 text-xs font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all shadow-md';
            document.getElementById('setVoyager').className = 'px-5 py-3 text-xs font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        const markerIcon = L.divIcon({
            html: `<div class="relative flex items-center justify-center"><div class="map-pulse"></div><div class="relative w-9 h-9 bg-[#a52a2a] border-[3px] border-white rounded-full shadow-lg flex items-center justify-center z-10"><div class="w-2 h-2 bg-white rounded-full"></div></div></div>`,
            className: '', iconSize: [48, 48], iconAnchor: [24, 24]
        });

        L.marker([lat, lng], { icon: markerIcon }).addTo(map).bindPopup(`<div class="text-center p-2"><h4 class="text-sm font-black uppercase text-slate-800 mb-1 leading-tight">{{ $school->name }}</h4><p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">ID: {{ $school->school_id }}</p></div>`).openPopup();
        
        // Force map recalculation after initial render to fix tile loading issues in flex/grid setups
        setTimeout(() => { map.invalidateSize(); }, 200);
    });
</script>
@endsection