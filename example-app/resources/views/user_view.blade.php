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
    <div class="max-w-6xl mx-auto space-y-8">
        
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
                    <button onclick="window.print()" class="bg-[#a52a2a] text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-black transition-all">
                        <i data-lucide="printer" class="w-4 h-4 inline-block mr-2"></i> Print Profile
                    </button>
                </div>
            </div>
        </header>

        {{-- Metrics Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6">
            @foreach([
                ['label' => 'Teachers', 'value' => $school->no_of_teachers, 'icon' => 'users-round', 'trend' => 'Faculty'],
                ['label' => 'Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'graduation-cap', 'trend' => 'Learners'],
                ['label' => 'Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'door-open', 'trend' => 'Classrooms'],
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                {{-- Map Section --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-[#a52a2a] rounded-full"></div>
                            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest">Geospatial Intelligence</h2>
                        </div>
                    </div>
                    <div class="relative">
                        <div id="schoolMap" class="h-[400px] w-full"></div>
                        <div class="absolute bottom-6 left-6 z-[1000] no-print">
                            <div class="bg-white/90 backdrop-blur-md p-1.5 rounded-xl shadow-2xl border border-slate-200 flex flex-col gap-1">
                                <button id="setVoyager" class="px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white">Street</button>
                                <button id="setSatellite" class="px-3 py-2 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600">Satellite</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Facility and Utilities --}}
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
                                <span class="text-sm font-black {{ ($short['val'] ?? 0) > 0 ? 'text-red-600' : 'text-emerald-500' }}">
                                    {{ number_format($short['val'] ?? 0) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i data-lucide="plug-zap" class="w-4 h-4 text-amber-500"></i> Utility Provisioning
                        </h3>
                        <div class="space-y-5">
                            
                            {{-- Power Supply Row --}}
                            <div class="flex items-center justify-between p-3 rounded-xl {{ $school->with_electricity != 'None' ? 'bg-emerald-50/30' : 'bg-red-50/30' }}">
                                <span class="text-[10px] font-bold text-slate-600 uppercase">Power Supply</span>
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-black uppercase {{ in_array($school->with_electricity, ['Grid Connection', 'Hybrid']) ? 'text-emerald-700' : 'text-amber-600' }}">
                                        @if(in_array($school->with_electricity, ['Grid Connection', 'Hybrid']))
                                            On-Grid System
                                        @elseif(in_array($school->with_electricity, ['Solar Powered', 'Generator']))
                                            Off-Grid System
                                        @else
                                            Off-Grid System (No Electricity)
                                        @endif
                                    </span>
                                    <span class="text-[8px] font-medium text-slate-400">{{ $school->with_electricity }}</span>
                                </div>
                            </div>

                            {{-- UPDATED: Water Row --}}
                            <div class="flex items-center justify-between p-3 rounded-xl {{ $school->with_potable_water ? 'bg-emerald-50/30' : 'bg-red-50/30' }}">
                                <span class="text-[10px] font-bold text-slate-600 uppercase">Potable Water</span>
                                <span class="text-[10px] font-black uppercase {{ $school->with_potable_water ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $school->with_potable_water ? 'With Water' : 'Without Water' }}
                                </span>
                            </div>

                            {{-- UPDATED: Internet Row --}}
                            <div class="flex items-center justify-between p-3 rounded-xl {{ $school->with_internet ? 'bg-emerald-50/30' : 'bg-red-50/30' }}">
                                <span class="text-[10px] font-bold text-slate-600 uppercase">Connectivity</span>
                                <span class="text-[10px] font-black uppercase {{ $school->with_internet ? 'text-emerald-700' : 'text-red-700' }} text-right leading-tight">
                                    {{ $school->with_internet ? 'With Internet Connectivity' : 'Without Internet Connectivity' }}
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Operational Ratios --}}
                <div class="bg-slate-900 p-8 rounded-3xl text-white shadow-xl relative overflow-hidden">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8">Operational Ratios</h3>
                    <div class="space-y-10">
                        @foreach([
                            ['label' => 'Room Saturation', 'ratio' => $classroomLearnerRatio, 'perc' => ($rawClassroomRatio / 60) * 100],
                            ['label' => 'Instructional Load', 'ratio' => $teacherLearnerRatio, 'perc' => ($rawTeacherRatio / 50) * 100],
                        ] as $analytic)
                        <div class="space-y-3">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-300">{{ $analytic['label'] }}</span>
                                <span class="text-sm font-black text-[#fca311]">{{ $analytic['ratio'] }}</span>
                            </div>
                            <div class="h-1.5 w-full bg-white/10 rounded-full">
                                <div class="h-full bg-[#a52a2a] rounded-full transition-all duration-1000" style="width: {{ min($analytic['perc'], 100) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Risk Management --}}
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[#a52a2a]"></i> Risk Profile
                    </h3>
                    <div class="p-5 bg-slate-50 rounded-2xl border-l-4 border-[#a52a2a]">
                        <p class="text-[11px] font-bold text-slate-900 uppercase mb-1">{{ $school->hazard_type ?: 'Secured Site' }}</p>
                        <p class="text-[10px] text-slate-500 leading-relaxed italic">
                            {{ $school->hazards ?: 'No high-priority hazards identified.' }}
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