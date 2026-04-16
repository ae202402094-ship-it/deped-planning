@extends('layouts.public')

@section('content')
@php
    // Calculate actual current ratios based on raw data
    $actualTeacherRatio = $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : $school->no_of_enrollees;
    $actualClassroomRatio = $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : $school->no_of_enrollees;
    $actualChairRatio = $school->no_of_chairs > 0 ? round($school->no_of_enrollees / $school->no_of_chairs, 1) : $school->no_of_enrollees;
    $actualToiletRatio = $school->no_of_toilets > 0 ? round($school->no_of_enrollees / $school->no_of_toilets) : $school->no_of_enrollees;

    // DepEd Standard Targets (Display Only)
    $targetTeacher = 45;
    $targetClassroom = 40;
    $targetChair = 1;
    $targetToilet = 50;
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .map-pulse {
        border-radius: 50%;
        height: 20px;
        width: 20px;
        position: absolute;
        background: rgba(165, 42, 42, 0.4);
        animation: pulsate 2s ease-out infinite;
        opacity: 0;
    }
    @keyframes pulsate {
        0% { transform: scale(0.1, 0.1); opacity: 0; }
        50% { opacity: 1.0; }
        100% { transform: scale(1.2, 1.2); opacity: 0; }
    }
    .leaflet-popup-content-wrapper { border-radius: 12px; padding: 5px; }
    .no-print { @media print { display: none !important; } }
</style>

<div class="min-h-screen bg-[#f8fafc] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Header Card --}}
        <header class="relative bg-white p-8 rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-sm">Institutional Profile</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">Registry Cycle 2026</span>
                    </div>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight">{{ $school->name }}</h1>
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-500">
                        <span class="flex items-center gap-1.5"><i data-lucide="fingerprint" class="w-3.5 h-3.5"></i> ID: {{ $school->school_id }}</span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i> Zamboanga Division</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-3 no-print">
                    <button onclick="window.print()" class="bg-[#a52a2a] text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-black transition-all flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print Profile
                    </button>
                </div>
            </div>
        </header>

        {{-- Metrics Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6">
            @foreach([
                ['label' => 'Teachers', 'value' => $school->no_of_teachers, 'icon' => 'users-round', 'trend' => 'Faculty'],
                ['label' => 'Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'graduation-cap', 'trend' => 'Learners'],
                ['label' => 'Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'door-open', 'trend' => 'Spaces'],
                ['label' => 'Toilets', 'value' => $school->no_of_toilets, 'icon' => 'toilet', 'trend' => 'Hygiene'],
                ['label' => 'Chairs', 'value' => $school->no_of_chairs, 'icon' => 'armchair', 'trend' => 'Seats'],
            ] as $metric)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-slate-50 rounded-xl group-hover:bg-[#a52a2a] group-hover:text-white transition-colors">
                        <i data-lucide="{{ $metric['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-tighter">{{ $metric['trend'] }}</span>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-1 tabular-nums">{{ number_format($metric['value']) }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">{{ $metric['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Main Layout Grid: Map (Left) + Stacked Cards (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Expanded Interactive Map --}}
            <div class="lg:col-span-2 flex flex-col">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex-1 min-h-[500px] flex flex-col">
                    <div class="p-6 border-b border-slate-50 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-[#a52a2a] rounded-full"></div>
                            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest">Geospatial Intelligence</h2>
                        </div>
                    </div>
                    <div class="relative flex-1">
                        <div id="schoolMap" class="absolute inset-0 w-full h-full"></div>
                        <div class="absolute bottom-6 left-6 z-[1000] no-print">
                            <div class="bg-white/90 backdrop-blur-md p-1.5 rounded-xl shadow-2xl border border-slate-200 flex flex-col gap-1">
                                <button id="setVoyager" class="px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white">Street</button>
                                <button id="setSatellite" class="px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600">Satellite</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Stacked Audit Cards --}}
            <div class="space-y-6">
                
                {{-- Integrated Resource Deficit & Ratio Audit --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="package-search" class="w-4 h-4 text-[#a52a2a]"></i> Resource Deficit Audit
                    </h3>
                    <div class="space-y-4">
                        @foreach([
                            ['label' => 'Faculty Members', 'val' => $school->teacher_shortage ?? 0, 'ratio' => $actualTeacherRatio, 'target' => $targetTeacher],
                            ['label' => 'Classroom Units', 'val' => $school->classroom_shortage ?? 0, 'ratio' => $actualClassroomRatio, 'target' => $targetClassroom],
                            ['label' => 'Furniture/Chairs', 'val' => $school->chair_shortage ?? 0, 'ratio' => $actualChairRatio, 'target' => $targetChair],
                            ['label' => 'Sanitation Units', 'val' => $school->toilet_shortage ?? 0, 'ratio' => $actualToiletRatio, 'target' => $targetToilet]
                        ] as $short)
                        <div class="flex items-center justify-between p-3.5 rounded-xl border {{ ($short['val'] > 0) ? 'bg-red-50/40 border-red-100' : 'bg-emerald-50/40 border-emerald-100' }}">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-700 uppercase tracking-tight">{{ $short['label'] }}</span>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded {{ ($short['ratio'] > $short['target']) ? 'bg-red-200/50 text-red-700' : 'bg-emerald-200/50 text-emerald-700' }}">
                                        1 : {{ $short['ratio'] }}
                                    </span>
                                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">Target 1:{{ $short['target'] }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end justify-center">
                                <span class="text-lg leading-none font-black {{ ($short['val'] > 0) ? 'text-red-600' : 'text-emerald-500' }}">
                                    {{ number_format($short['val']) }}
                                </span>
                                <span class="text-[7px] font-bold text-slate-400 uppercase tracking-widest mt-1">Deficit</span>
                            </div>
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
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[#a52a2a]"></i> Risk Profile
                    </h3>
                    
                    @php
                        $hazards = is_array($school->hazard_type) ? $school->hazard_type : (json_decode($school->hazard_type, true) ?? [$school->hazard_type]);
                        // Filter out empty or "None" values
                        $activeHazards = array_filter($hazards, fn($h) => !empty($h) && $h !== 'None');
                    @endphp

                    <div class="p-4 bg-slate-50 rounded-xl border-l-4 {{ count($activeHazards) > 0 ? 'border-[#a52a2a]' : 'border-emerald-500' }}">
                        @if(count($activeHazards) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($activeHazards as $hazard)
                                    <span class="bg-red-100 text-red-800 text-[9px] font-black px-2 py-1 rounded uppercase tracking-widest">{{ $hazard }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[10px] font-black text-emerald-700 uppercase">Secured Site</p>
                        @endif
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

        // --- MAP LOGIC ---
        const lat = {{ $school->latitude }};
        const lng = {{ $school->longitude }};
        
        const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: 'CARTO' });
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Esri' });

        const map = L.map('schoolMap', { scrollWheelZoom: false, layers: [voyager] }).setView([lat, lng], 17);
        
        document.getElementById('setVoyager').addEventListener('click', function() {
            map.removeLayer(satellite); map.addLayer(voyager);
            this.className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white';
            document.getElementById('setSatellite').className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600';
        });

        document.getElementById('setSatellite').addEventListener('click', function() {
            map.removeLayer(voyager); map.addLayer(satellite);
            this.className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white';
            document.getElementById('setVoyager').className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600';
        });

        const markerIcon = L.divIcon({
            html: `<div class="relative flex items-center justify-center"><div class="map-pulse"></div><div class="relative w-8 h-8 bg-[#a52a2a] border-4 border-white rounded-full shadow-2xl flex items-center justify-center"><div class="w-1.5 h-1.5 bg-white rounded-full"></div></div></div>`,
            className: '', iconSize: [48, 48], iconAnchor: [24, 24]
        });

        L.marker([lat, lng], { icon: markerIcon }).addTo(map).bindPopup(`<h4 class="text-xs font-black uppercase">{{ $school->name }}</h4>`).openPopup();
    });
</script>
@endsection