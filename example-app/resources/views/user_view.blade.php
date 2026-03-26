@extends('layouts.public')

@section('content')
@php
    // 1. Core Ratio Calculations
    $teacherLearnerRatio = $school->no_of_teachers > 0 
        ? "1 : " . round($school->no_of_enrollees / $school->no_of_teachers) 
        : "0 : 0";

    $classroomLearnerRatio = $school->no_of_classrooms > 0 
        ? "1 : " . round($school->no_of_enrollees / $school->no_of_classrooms) 
        : "0 : 0";

    $rawClassroomRatio = $school->no_of_classrooms > 0 
        ? round($school->no_of_enrollees / $school->no_of_classrooms) 
        : 0;

    $rawTeacherRatio = $school->no_of_teachers > 0 
        ? round($school->no_of_enrollees / $school->no_of_teachers) 
        : 0;

    // 2. UPDATED Status Logic (Using new hazard_level)
    $isHighRisk = ($school->hazard_level === 'High');

    if ($isHighRisk) {
        $statusLabel = 'Critical Risk';
        $statusColor = 'bg-red-600';
        $statusBorder = 'border-red-200';
        $statusText = 'text-red-700';
    } elseif ($rawClassroomRatio > 50) {
        $statusLabel = 'Overcrowded';
        $statusColor = 'bg-rose-500';
        $statusBorder = 'border-rose-200';
        $statusText = 'text-rose-700';
    } elseif ($rawClassroomRatio >= 40) {
        $statusLabel = 'At Capacity';
        $statusColor = 'bg-amber-500';
        $statusBorder = 'border-amber-200';
        $statusText = 'text-amber-700';
    } else {
        $statusLabel = 'Optimal';
        $statusColor = 'bg-emerald-500';
        $statusBorder = 'border-emerald-200';
        $statusText = 'text-emerald-700';
    }
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="max-w-6xl mx-auto px-6">
    {{-- Navigation & Status Bar --}}
    <div class="mb-8 flex justify-between items-center">
        <a href="{{ route('public.map') }}" class="group inline-flex items-center gap-3 text-slate-400 hover:text-red-800 transition-all">
            <div class="p-2 rounded-full group-hover:bg-red-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Return to Interactive Map</span>
        </a>

        <div class="flex items-center gap-3 px-6 py-2 rounded-full border {{ $statusBorder }} bg-white shadow-sm">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $statusColor }} opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 {{ $statusColor }}"></span>
            </span>
            <span class="text-[10px] font-black uppercase tracking-widest {{ $statusText }}">{{ $statusLabel }}</span>
        </div>
    </div>

    {{-- Main Profile Card --}}
    <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 mb-12">
        
        {{-- Header Banner --}}
        <div style="background-color: #a52a2a;" class="p-12 text-white text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <svg width="100%" height="100%"><rect width="100%" height="100%" fill="url(#grid)"/><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/></pattern></defs></svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-5xl font-black uppercase tracking-tighter leading-none mb-4 drop-shadow-lg">{{ $school->name }}</h1>
                <div class="inline-flex items-center gap-2 bg-black/30 backdrop-blur-md px-6 py-2 rounded-full border border-white/20">
                    <span class="text-[10px] font-black uppercase tracking-widest text-red-300">Official Registry</span>
                    <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                    <span class="text-sm font-mono font-bold italic tracking-tighter">ID: {{ $school->school_id }}</span>
                </div>
            </div>
        </div>

        {{-- Top Metrics Grid --}}
        <div class="p-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 border-b border-slate-100">
            @foreach([
                ['label' => 'Teachers', 'value' => $school->no_of_teachers, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label' => 'Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l9-5-9-5-9 5 9 5zm0 0v6.5L7 20v-6.5l5 3.5 5-3.5z'],
                ['label' => 'Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 4h1m4 0h1m-5 4h1m4 0h1'],
                ['label' => 'Toilets', 'value' => $school->no_of_toilets, 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ] as $m)
                <div class="text-center group">
                    <div class="mb-4 flex justify-center">
                        <div class="p-4 bg-slate-50 rounded-2xl group-hover:bg-red-50 group-hover:text-red-800 text-slate-400 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}"/></svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $m['label'] }}</p>
                    <p class="text-5xl font-black text-slate-800 tabular-nums">{{ number_format($m['value']) }}</p>
                </div>
            @endforeach
        </div>

        {{-- Map & Sidebar Analytics --}}
        <div class="p-12 bg-slate-50/30">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                {{-- Map Container --}}
                <div class="lg:col-span-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Institutional Mapping</p>
                    <div id="schoolMap" class="h-[550px] w-full rounded-[2.5rem] border border-slate-200 shadow-inner"></div>
                </div>

                {{-- Sidebar: Capacity & Hazards --}}
                <div class="space-y-8">
                    
                    {{-- Utilization Ratios --}}
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Utilization Metrics</p>
                        
                        <div class="space-y-8">
                            {{-- Classroom Ratio --}}
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-500 uppercase">Physical Capacity</p>
                                        <h4 class="text-2xl font-black text-slate-800">{{ $classroomLearnerRatio }}</h4>
                                    </div>
                                    <span class="text-[9px] font-black px-2 py-1 rounded bg-slate-100 text-slate-500 uppercase">Classrooms</span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-500 {{ $rawClassroomRatio > 50 ? 'bg-rose-500' : ($rawClassroomRatio >= 40 ? 'bg-amber-500' : 'bg-emerald-500') }}" 
                                         style="width: {{ min(($rawClassroomRatio / 60) * 100, 100) }}%">
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            {{-- Teacher Ratio --}}
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-500 uppercase">Staffing Efficiency</p>
                                        <h4 class="text-2xl font-black text-slate-800">{{ $teacherLearnerRatio }}</h4>
                                    </div>
                                    <span class="text-[9px] font-black px-2 py-1 rounded bg-slate-100 text-slate-500 uppercase">Teachers</span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-slate-800 transition-all duration-500" 
                                         style="width: {{ min(($rawTeacherRatio / 50) * 100, 100) }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modular Hazard Assessment View --}}
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Environmental Risk Assessment</p>
                        
                        <div class="flex items-start gap-6">
                            {{-- Dynamic Icon Based on Hazard Type --}}
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 
                                {{ $school->hazard_level === 'High' ? 'bg-red-50 text-red-600' : ($school->hazard_level === 'Moderate' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}">
                                @if($school->hazard_type === 'Landslide')
                                    <i class="bi bi-land-layers text-2xl"></i>
                                @elseif($school->hazard_type === 'Flood')
                                    <i class="bi bi-water text-2xl"></i>
                                @elseif($school->hazard_type === 'Traffic')
                                    <i class="bi bi-car-front text-2xl"></i>
                                @elseif($school->hazard_type === 'None')
                                    <i class="bi bi-shield-check text-2xl"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle text-2xl"></i>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-800">
                                    {{ $school->hazard_type === 'None' ? 'Safety Status: Nominal' : $school->hazard_type }}
                                </h4>
                                
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase px-3 py-1 rounded-full border 
                                        {{ $school->hazard_level === 'High' ? 'bg-red-600 text-white border-red-600' : 
                                           ($school->hazard_level === 'Moderate' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200') }}">
                                        {{ $school->hazard_level }} Risk
                                    </span>
                                </div>

                                <p class="text-[9px] text-slate-400 italic mt-2 leading-tight">
                                    @if($school->hazard_level === 'High')
                                        Critical risk detected. This institution is prioritized for DRRM monitoring.
                                    @elseif($school->hazard_level === 'Moderate')
                                        Standard precautions in effect for seasonal environmental changes.
                                    @else
                                        No significant geospatial hazards reported in the current registry cycle.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="text-center pb-20">
        <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.5em]">Division of Zamboanga City Data Analytics</p>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $school->latitude }};
        const lng = {{ $school->longitude }};
        const map = L.map('schoolMap', { scrollWheelZoom: false }).setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const icon = L.divIcon({
            html: `<div style="background-color: #a52a2a; width: 45px; height: 45px; border-radius: 50%; border: 4px solid white; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);"><i class="bi bi-building-fill" style="color: white; font-size: 20px;"></i></div>`,
            iconSize: [45, 45], iconAnchor: [22, 22]
        });

        L.marker([lat, lng], {icon}).addTo(map).bindPopup('<b class="uppercase tracking-widest">{{ $school->name }}</b>').openPopup();
    });
</script>
@endsection