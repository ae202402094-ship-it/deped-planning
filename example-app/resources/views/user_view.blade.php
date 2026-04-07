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
    /* Pulse Animation for Map Marker */
    .map-pulse {
        border-radius: 50%;
        height: 20px;
        width: 20px;
        position: absolute;
        background: rgba(165, 42, 42, 0.4);
        animation: pulsate 2s ease-out;
        animation-iteration-count: infinite;
        opacity: 0;
    }
    @keyframes pulsate {
        0% { transform: scale(0.1, 0.1); opacity: 0; }
        50% { opacity: 1.0; }
        100% { transform: scale(1.2, 1.2); opacity: 0; }
    }
    /* Custom Popup Styling */
    .leaflet-popup-content-wrapper { border-radius: 12px; padding: 5px; }
    .leaflet-popup-tip { background: white; }
</style>

<div class="min-h-screen bg-[#f8fafc] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">
        
        {{-- Header Card (Unchanged) --}}
        <header class="relative bg-white p-8 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-bl-full opacity-50"></div>
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
                <div class="flex flex-col items-end gap-3">
                    <div class="flex items-center gap-3 px-4 py-2 rounded-xl border {{ $statusColor }}">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ str_replace('text-', 'bg-', explode(' ', $statusColor)[0]) }}"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 {{ str_replace('text-', 'bg-', explode(' ', $statusColor)[0]) }}"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-widest">Audit: {{ $statusLabel }}</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Metrics Grid (Unchanged) --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            @foreach([
                ['label' => 'Teachers', 'value' => $school->no_of_teachers, 'icon' => 'users-round', 'trend' => 'Faculty'],
                ['label' => 'Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'graduation-cap', 'trend' => 'Learners'],
                ['label' => 'Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'door-open', 'trend' => 'Classrooms'],
                ['label' => 'Sanitary Facilities', 'value' => $school->no_of_toilets, 'icon' => 'toilet', 'trend' => 'Hygiene'],
                ['label' => 'Furniture Inventory', 'value' => $school->no_of_chairs, 'icon' => 'armchair', 'trend' => 'Seats'],
            ] as $metric)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2.5 bg-slate-50 rounded-xl group-hover:bg-[#a52a2a] group-hover:text-white transition-colors duration-300">
                        <i data-lucide="{{ $metric['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-tighter">{{ $metric['trend'] }}</span>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-1 tabular-nums">{{ number_format($metric['value']) }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">{{ $metric['label'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- ENHANCED: Map & Location --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-[#a52a2a] rounded-full"></div>
                            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest">Geospatial Intelligence</h2>
                        </div>
                        <div class="flex items-center gap-2">
                             {{-- Navigation Link --}}
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $school->latitude }},{{ $school->longitude }}" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-[10px] font-black uppercase text-slate-600 rounded-lg hover:bg-[#a52a2a] hover:text-white transition-all">
                                <i data-lucide="navigation" class="w-3.5 h-3.5"></i>
                                Get Directions
                            </a>
                        </div>
                    </div>

                    {{-- Map Container --}}
                    <div class="relative">
                        <div id="schoolMap" class="h-[450px] w-full transition-all duration-700"></div>
                        
                        {{-- Custom Layer Control Overlay --}}
                        <div class="absolute bottom-6 left-6 z-[1000] no-print">
                            <div class="bg-white/90 backdrop-blur-md p-1.5 rounded-xl shadow-2xl border border-slate-200 flex flex-col gap-1">
                                <button id="setVoyager" class="px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all">Street</button>
                                <button id="setSatellite" class="px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all">Satellite</button>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 flex justify-between items-center text-[10px] font-mono text-slate-400">
                        <div class="flex gap-4">
                            <span>LAT: {{ $school->latitude }}</span>
                            <span>LNG: {{ $school->longitude }}</span>
                        </div>
                        <span class="uppercase tracking-widest">DepEd GIS Registry v2.0</span>
                    </div>
                </div>

                {{-- Facility Audit Grid (Unchanged) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i data-lucide="package-search" class="w-4 h-4 text-[#a52a2a]"></i> Resource Deficit Audit
                        </h3>
                        <div class="space-y-6">
                            @foreach([
                                ['label' => 'Classroom Units', 'val' => $school->classroom_shortage],
                                ['label' => 'Furniture/Chairs', 'val' => $school->chair_shortage],
                                ['label' => 'Sanitation Units', 'val' => $school->toilet_shortage]
                            ] as $short)
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500">{{ $short['label'] }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-black {{ ($short['val'] ?? 0) > 0 ? 'text-red-600' : 'text-slate-300' }}">
                                        {{ number_format($short['val'] ?? 0) }}
                                    </span>
                                    <div class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500" style="width: {{ $short['val'] > 0 ? '70%' : '0%' }}"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i data-lucide="plug-zap" class="w-4 h-4 text-amber-500"></i> Utility Provisioning
                        </h3>
                        <div class="space-y-5">
                            @foreach([
                                ['label' => 'Power Grid', 'status' => $school->with_electricity != 'None', 'meta' => $school->with_electricity],
                                ['label' => 'Potable Water', 'status' => $school->with_potable_water, 'meta' => 'Supply Active'],
                                ['label' => 'Data Connectivity', 'status' => $school->with_internet, 'meta' => 'Network']
                            ] as $u)
                            <div class="flex items-center justify-between p-3 rounded-xl {{ $u['status'] ? 'bg-emerald-50/30' : 'bg-red-50/30' }} transition-colors">
                                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight">{{ $u['label'] }}</span>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-black uppercase {{ $u['status'] ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ $u['status'] ? 'Functional' : 'Absent' }}
                                    </span>
                                    <span class="text-[8px] font-medium text-slate-400">{{ $u['meta'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Analytics & Remarks (Unchanged) --}}
            <div class="space-y-8">
                <div class="bg-slate-900 p-8 rounded-3xl text-white shadow-2xl shadow-slate-200 relative overflow-hidden">
                    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-[#a52a2a] rounded-full blur-3xl opacity-20"></div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8">Operational Ratios</h3>
                    
                    <div class="space-y-10">
                        @foreach([
                            ['label' => 'Room Saturation', 'ratio' => $classroomLearnerRatio, 'perc' => ($rawClassroomRatio / 60) * 100],
                            ['label' => 'Instructional Load', 'ratio' => $teacherLearnerRatio, 'perc' => ($rawTeacherRatio / 50) * 100],
                            ['label' => 'Furniture Ratio', 'ratio' => $chairLearnerRatio, 'perc' => ($rawChairRatio / 2) * 100]
                        ] as $analytic)
                        <div class="space-y-3">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-300">{{ $analytic['label'] }}</span>
                                <span class="text-sm font-black font-mono text-[#fca311]">{{ $analytic['ratio'] }}</span>
                            </div>
                            <div class="h-1.5 w-full bg-white/10 rounded-full">
                                <div class="h-full bg-gradient-to-r from-[#a52a2a] to-[#ff4d4d] rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(255,77,77,0.4)]" style="width: {{ min($analytic['perc'], 100) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[#a52a2a]"></i> Risk Management
                    </h3>
                    <div class="p-5 bg-slate-50 rounded-2xl border-l-4 border-[#a52a2a]">
                        <p class="text-[11px] font-bold text-slate-900 uppercase mb-1">{{ $school->hazard_type ?: 'Secured Site' }}</p>
                        <p class="text-[10px] text-slate-500 leading-relaxed italic">
                            {{ $school->hazards ?: 'No high-priority environmental or structural hazards identified in this cycle.' }}
                        </p>
                    </div>
                </div>

                {{-- Back Action (Unchanged) --}}
                <a href="{{ route('public.map') }}" class="flex items-center justify-center gap-3 w-full py-4 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-[#a52a2a] hover:border-[#a52a2a] transition-all group">
                    <i data-lucide="chevron-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    Registry Explorer
                </a>
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
        
        // Define Different Tile Layers
        const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: 'CARTO'
        });
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Esri'
        });

        // Initialize Map
        const map = L.map('schoolMap', { 
            scrollWheelZoom: false, 
            zoomControl: true,
            layers: [voyager] 
        }).setView([lat, lng], 17);
        
        // Layer Toggles Logic
        document.getElementById('setVoyager').addEventListener('click', function() {
            map.removeLayer(satellite);
            map.addLayer(voyager);
            this.className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all';
            document.getElementById('setSatellite').className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        document.getElementById('setSatellite').addEventListener('click', function() {
            map.removeLayer(voyager);
            map.addLayer(satellite);
            this.className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all';
            document.getElementById('setVoyager').className = 'px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        // High-End Ripple Marker
        const markerIcon = L.divIcon({
            html: `
                <div class="relative flex items-center justify-center">
                    <div class="map-pulse"></div>
                    <div class="relative w-8 h-8 bg-[#a52a2a] border-4 border-white rounded-full shadow-2xl flex items-center justify-center">
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                    </div>
                </div>
            `,
            className: '',
            iconSize: [48, 48],
            iconAnchor: [24, 24]
        });

        const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);

        // Interactive Popup
        marker.bindPopup(`
            <div class="p-1 min-w-[150px]">
                <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight mb-1">{{ $school->name }}</h4>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">Verified Geospatial Node</p>
                <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between items-center">
                    <span class="text-[9px] font-mono text-slate-400">ID: {{ $school->school_id }}</span>
                </div>
            </div>
        `).openPopup();
    });
</script>
@endsection