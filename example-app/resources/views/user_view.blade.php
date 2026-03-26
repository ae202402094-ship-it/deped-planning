@extends('layouts.public')

@section('content')
@php
    $teacherLearnerRatio = $school->no_of_teachers > 0 ? "1 : " . round($school->no_of_enrollees / $school->no_of_teachers) : "0 : 0";
    $classroomLearnerRatio = $school->no_of_classrooms > 0 ? "1 : " . round($school->no_of_enrollees / $school->no_of_classrooms) : "0 : 0";
    $rawClassroomRatio = $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : 0;
    $rawTeacherRatio = $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : 0;

    $isHighRisk = ($school->hazard_level === 'High');
    if ($isHighRisk) {
        $statusLabel = 'Critical Risk';
        $statusColor = 'bg-red-600';
    } elseif ($rawClassroomRatio > 50) {
        $statusLabel = 'Overcrowded';
        $statusColor = 'bg-rose-500';
    } else {
        $statusLabel = 'Standard';
        $statusColor = 'bg-slate-800';
    }
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="max-w-5xl mx-auto px-6 py-10">
    {{-- Top Protocol Bar --}}
    <div class="mb-10 flex justify-between items-end border-b-2 border-slate-900 pb-4">
        <div>
            <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.4em] mb-1 block">Institutional Audit Record</span>
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter leading-none">{{ $school->name }}</h1>
        </div>
        <div class="text-right flex items-center gap-6">
            <div class="hidden md:block">
                <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest block">System Reference ID</span>
                <span class="text-xs font-mono font-bold text-slate-900 tracking-tighter italic">#{{ $school->school_id }}</span>
            </div>
            <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 border border-slate-200">
                <span class="h-1.5 w-1.5 rounded-full {{ $statusColor }}"></span>
                <span class="text-[8px] font-black uppercase tracking-widest text-slate-600">Status: {{ $statusLabel }}</span>
            </div>
        </div>
    </div>

    {{-- Section 01: Core Demographics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 border border-slate-200 mb-12">
        @foreach([
            ['label' => 'Total Instructional Personnel', 'value' => $school->no_of_teachers],
            ['label' => 'Total Registered Enrollees', 'value' => $school->no_of_enrollees],
            ['label' => 'Instructional Spaces', 'value' => $school->no_of_classrooms],
            ['label' => 'Sanitary Facilities', 'value' => $school->no_of_toilets],
        ] as $metric)
            <div class="p-6 border-r border-slate-100 last:border-none bg-white">
                <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-2 leading-tight h-4">{{ $metric['label'] }}</p>
                <p class="text-3xl font-black text-slate-900 tabular-nums tracking-tighter">{{ number_format($metric['value']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Section 02: Resource & Utility Audit (MOVED UP) --}}
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-8">
            <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">01 / Infrastructure & Resource Audit</span>
            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
            {{-- Utilities Matrix --}}
            <div class="space-y-4">
                <h3 class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Utilities Matrix</h3>
                <div class="space-y-4">
                    @foreach([
                        ['label' => 'Electricity', 'status' => $school->with_electricity, 'icon' => '⚡'],
                        ['label' => 'Potable Water', 'status' => $school->with_potable_water, 'icon' => '💧'],
                        ['label' => 'Connectivity', 'status' => $school->with_internet, 'icon' => '🌐']
                    ] as $util)
                        <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                            <span class="text-[9px] font-bold text-slate-600 uppercase flex items-center gap-2">
                                <span class="{{ $util['status'] ? 'opacity-100' : 'opacity-20 grayscale' }}">{{ $util['icon'] }}</span> 
                                {{ $util['label'] }}
                            </span>
                            <span class="text-[8px] font-black uppercase tracking-tighter {{ $util['status'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $util['status'] ? 'Functional' : 'Non-Functional' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Shortages --}}
            <div class="space-y-4">
                <h3 class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Inventory Deficit Audit</h3>
                <div class="space-y-3">
                    @foreach([
                        ['label' => 'Classroom Shortage', 'val' => $school->classroom_shortage],
                        ['label' => 'Chair Shortage', 'val' => $school->chair_shortage],
                        ['label' => 'Toilet Shortage', 'val' => $school->toilet_shortage]
                    ] as $short)
                        <div class="flex justify-between items-end border-b border-slate-50 pb-2">
                            <span class="text-[9px] font-bold text-slate-500 uppercase">{{ $short['label'] }}</span>
                            <span class="text-xs font-black {{ ($short['val'] ?? 0) > 0 ? 'text-rose-800' : 'text-slate-400' }} font-mono">
                                {{ number_format($short['val'] ?? 0) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Environmental Note --}}
            <div class="space-y-4">
                <h3 class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Technical Remarks</h3>
                <div class="p-5 bg-slate-50 border border-slate-200">
                    <p class="text-[9px] font-bold text-slate-500 leading-relaxed italic uppercase tracking-tighter">
                        {{ $school->hazards ?: 'No high-priority geospatial hazards identified in the current registry cycle.' }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-[7px] font-black text-slate-300 uppercase italic">Ref: Audit_Log_Zambo_City</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 03: Geospatial & Analytics (MOVED DOWN) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">02 / Geospatial Mapping</span>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>
            <div id="schoolMap" class="h-[350px] w-full border border-slate-200 grayscale shadow-sm"></div>
            <div class="mt-3 flex justify-between items-center text-[8px] font-mono text-slate-400 uppercase">
                <span>Lat: {{ $school->latitude }}</span>
                <span>Lng: {{ $school->longitude }}</span>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-10">
            {{-- Efficiency Metrics --}}
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">03 / Efficiency</span>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between text-[8px] font-black uppercase mb-1">
                            <span class="text-slate-400 tracking-widest">Classroom-Learner Ratio</span>
                            <span class="text-slate-900 font-mono">{{ $classroomLearnerRatio }}</span>
                        </div>
                        <div class="h-1 w-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-slate-900" style="width: {{ min(($rawClassroomRatio / 60) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[8px] font-black uppercase mb-1">
                            <span class="text-slate-400 tracking-widest">Teacher-Learner Ratio</span>
                            <span class="text-slate-900 font-mono">{{ $teacherLearnerRatio }}</span>
                        </div>
                        <div class="h-1 w-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-slate-400" style="width: {{ min(($rawTeacherRatio / 50) * 100, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hazard Assessment --}}
            <div class="bg-slate-50 p-6 border-l-2 border-slate-900">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-4">DRRM Hazard Assessment</span>
                <div class="flex items-center gap-4">
                    <i class="bi bi-shield-shaded text-xl text-slate-900"></i>
                    <div>
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tighter">{{ $school->hazard_type ?: 'NO SPECIFIC THREATS' }}</p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest">{{ $school->hazard_level }} Priority Level</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-24 text-center border-t border-slate-100 pt-8 no-print">
        <a href="{{ route('public.map') }}" class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] hover:text-red-800 transition-colors">
            ← Archive Return Protocol
        </a>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $school->latitude }};
        const lng = {{ $school->longitude }};
        const map = L.map('schoolMap', { scrollWheelZoom: false, zoomControl: false }).setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([lat, lng], {
            icon: L.divIcon({
                html: `<div style="background-color: #0f172a; width: 30px; height: 30px; border: 3px solid white; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);"></div>`,
                iconSize: [30, 30], iconAnchor: [15, 15]
            })
        }).addTo(map);
    });
</script>
@endsection