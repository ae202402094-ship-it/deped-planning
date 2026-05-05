@extends('layouts.public')

@section('content')
@php
    $isEmbed = session('is_embedded', false) || request()->query('embed') === 'true';

    $actualTeacherRatio = $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : '0';
    $actualClassroomRatio = $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : '0';
    $actualChairRatio = $school->no_of_chairs > 0 ? round($school->no_of_enrollees / $school->no_of_chairs, 1) : '0';
    $actualToiletRatio = $school->no_of_toilets > 0 ? round($school->no_of_enrollees / $school->no_of_toilets) : '0';
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .map-pulse { border-radius: 50%; height: 20px; width: 20px; position: absolute; background: rgba(165, 42, 42, 0.4); animation: pulsate 2s ease-out infinite; opacity: 0; }
    @keyframes pulsate { 0% { transform: scale(0.1, 0.1); opacity: 0; } 50% { opacity: 1.0; } 100% { transform: scale(1.2, 1.2); opacity: 0; } }
    .leaflet-popup-content-wrapper { border-radius: 12px; padding: 5px; }
    .no-print { @media print { display: none !important; } }
</style>

<div class="min-h-screen bg-[#f8fafc] {{ $isEmbed ? 'py-4' : 'py-12' }} px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Header Card --}}
        <header class="relative bg-white p-8 rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="absolute top-4 right-8 text-right flex items-center gap-2 group">
                <span class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">
                    as of [{{ $school->updated_at->format('F d, Y') }}]
                </span>
                <div class="relative cursor-help">
                    <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                    <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                        This reflects the exact date the school registry was last updated by the Division Office.
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mt-2">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-sm">Institutional Profile</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">Registry Cycle 2026</span>
                    </div>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight">{{ $school->name }}</h1>
                </div>

                @if(!$isEmbed)
                <div class="flex flex-col items-end gap-3 no-print">
                    <button onclick="window.print()" class="bg-[#a52a2a] text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-black transition-all flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print Profile
                    </button>
                </div>
                @endif
            </div>
        </header>

        {{-- Top Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            @foreach([
                ['label' => 'Total Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'graduation-cap', 'tip' => 'The total number of students currently officially registered in this school.'],
                ['label' => 'Total Teachers', 'value' => $school->no_of_teachers, 'icon' => 'users-round', 'tip' => 'The count of active teaching personnel assigned to this facility.'],
                ['label' => 'Total Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'door-open', 'tip' => 'Number of rooms used for daily academic instruction.'],
                ['label' => 'Total Toilets', 'value' => $school->no_of_toilets, 'icon' => 'toilet', 'tip' => 'Total sanitary cubicles available for student and staff use.'],
                ['label' => 'Total Chairs', 'value' => $school->no_of_chairs, 'icon' => 'armchair', 'tip' => 'Standard seating units available for the student population.'],
            ] as $metric)
            <div class="group relative bg-white pt-5 px-4 pb-4 sm:pt-6 sm:px-6 shadow-sm border border-slate-200 rounded-2xl overflow-hidden hover:border-[#a52a2a]/40 transition-all duration-300">
                <dt class="flex justify-between items-start">
                    <div class="absolute rounded-xl p-3 bg-red-50 text-[#a52a2a] group-hover:bg-[#a52a2a] group-hover:text-white transition-colors duration-300">
                        <i data-lucide="{{ $metric['icon'] }}" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-16 flex items-center gap-1.5">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest truncate group-hover:text-[#a52a2a]">
                            {{ $metric['label'] }}
                        </p>
                        <div class="relative inline-block group/tip cursor-help">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute left-1/2 bottom-full -translate-x-1/2 mb-2 w-40 p-2 bg-slate-900 text-white text-[9px] font-medium rounded shadow-2xl opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal text-center">
                                {{ $metric['tip'] }}
                            </div>
                        </div>
                    </div>
                </dt>
                <dd class="ml-16 flex items-baseline pb-1 mt-1">
                    <p class="text-3xl font-black text-slate-900 tabular-nums">{{ number_format($metric['value']) }}</p>
                </dd>
            </div>
            @endforeach
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Map Column --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-[630px] flex flex-col">
                    <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-[#a52a2a] rounded-full"></div>
                            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest">Interactive Map</h2>
                            <div class="relative group/maptip cursor-help">
                                <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                                <div class="absolute left-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/maptip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                    Visualizes the exact location. Use 'Street' for navigation or 'Satellite' to see the physical terrain and campus rooflines.
                                </div>
                            </div>
                        </div>
                        <div class="bg-white/90 p-1 rounded-xl border border-slate-200 flex gap-1 no-print">
                            <button id="setVoyager" class="px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all">Street</button>
                            <button id="setSatellite" class="px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all">Satellite</button>
                        </div>
                    </div>
                    <div class="relative flex-1">
                        <div id="schoolMap" class="absolute inset-0 w-full h-full"></div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="space-y-6">
                {{-- Shortage Analysis --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-50 pb-3">
                        <i data-lucide="clipboard-check" class="w-4 h-4 text-[#a52a2a]"></i> Resource Shortage
                        <div class="relative group/shorttip cursor-help ml-auto">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute right-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/shorttip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                Compares student population against available units. Red numbers indicate a critical deficit that needs budget priority.
                            </div>
                        </div>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="text-left text-[8px] font-black uppercase tracking-widest text-slate-400">
                                    <th class="pb-2 pr-1">Category</th>
                                    <th class="pb-2 px-1 text-center">Ratio</th>
                                    <th class="pb-2 pl-1 text-right">Shortage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach([
                                    ['Teachers', $actualTeacherRatio, $school->teacher_shortage],
                                    ['Classrooms', $actualClassroomRatio, $school->classroom_shortage],
                                    ['Seats', $actualChairRatio, $school->chair_shortage],
                                    ['Toilets', $actualToiletRatio, $school->toilet_shortage]
                                ] as $row)
                                <tr>
                                    <td class="py-3 pr-1 text-[10px] font-bold text-slate-700">{{ $row[0] }}</td>
                                    <td class="py-3 px-1 text-center text-[10px] font-black text-slate-500">1:{{ $row[1] }}</td>
                                    <td class="py-3 pl-1 text-right font-black text-xs {{ ($row[2] > 0) ? 'text-red-600' : 'text-emerald-500' }}">
                                        {{ number_format($row[2] ?? 0) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Provisioning Card --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-50 pb-3">
                        <i data-lucide="plug-zap" class="w-4 h-4 text-amber-500"></i> Utilities
                        <div class="relative group/utiltip cursor-help ml-auto">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute right-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/utiltip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                Shows the operational status of basic facilities. NO indicates a gap in infrastructure development.
                            </div>
                        </div>
                    </h3>
                    <div class="space-y-3">
                        @foreach([
                            ['Power', $school->with_electricity, in_array($school->with_electricity, ['Grid Connection', 'Hybrid'])],
                            ['Water', $school->with_potable_water ? 'YES' : 'NO', $school->with_potable_water],
                            ['Internet', $school->with_internet ? 'YES' : 'NO', $school->with_internet]
                        ] as $util)
                        <div class="flex items-center justify-between p-3 rounded-xl border {{ $util[2] ? 'bg-emerald-50/30 border-emerald-100' : 'bg-red-50/30 border-red-100' }}">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $util[0] }}</span>
                            <span class="text-[10px] font-black uppercase {{ $util[2] ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $util[1] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Risk Card --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-50 pb-3">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-[#a52a2a]"></i> Risk Profile
                        <div class="relative group/risktip cursor-help ml-auto">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute right-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/risktip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                Lists environmental threats the school is exposed to based on divisional audit.
                            </div>
                        </div>
                    </h3>
                    @php
                        $rawHazards = is_array($school->hazard_type) ? $school->hazard_type : (json_decode($school->hazard_type, true) ?? [$school->hazard_type]);
                        $activeHazards = [];
                        if (is_array($rawHazards)) {
                            foreach($rawHazards as $h) {
                                $clean = trim(str_replace(['"', '[', ']'], '', $h));
                                if (!empty($clean) && strtolower($clean) !== 'none' && strtolower($clean) !== 'others') {
                                    $activeHazards[] = $clean;
                                }
                            }
                        }
                    @endphp
                    <div class="p-4 bg-slate-50 rounded-xl border-l-4 {{ count($activeHazards) > 0 ? 'border-[#a52a2a]' : 'border-emerald-500' }}">
                        @if(count($activeHazards) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($activeHazards as $hazard)
                                    <span class="bg-red-100 text-red-800 text-[8px] font-black px-2 py-1 rounded uppercase tracking-widest">{{ $hazard }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-[9px] font-black text-emerald-700 uppercase tracking-widest flex items-center gap-1.5">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i> No Critical Hazards
                            </div>
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
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const lat = {{ $school->latitude }};
        const lng = {{ $school->longitude }};
        const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: 'CARTO' });
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Esri' });
        const map = L.map('schoolMap', { scrollWheelZoom: false, layers: [voyager] }).setView([lat, lng], 17);
        
        document.getElementById('setVoyager').addEventListener('click', function() {
            map.removeLayer(satellite); map.addLayer(voyager);
            this.className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all';
            document.getElementById('setSatellite').className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        document.getElementById('setSatellite').addEventListener('click', function() {
            map.removeLayer(voyager); map.addLayer(satellite);
            this.className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all';
            document.getElementById('setVoyager').className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        const markerIcon = L.divIcon({
            html: `<div class="relative flex items-center justify-center"><div class="map-pulse"></div><div class="relative w-8 h-8 bg-[#a52a2a] border-4 border-white rounded-full shadow-2xl flex items-center justify-center"><div class="w-1.5 h-1.5 bg-white rounded-full"></div></div></div>`,
            className: '', iconSize: [48, 48], iconAnchor: [24, 24]
        });

        L.marker([lat, lng], { icon: markerIcon }).addTo(map).bindPopup(`
            <div class="font-sans min-w-[150px]">
                <strong class="text-slate-800 block mb-1 text-xs uppercase">{{ $school->name }}</strong>
            </div>
        `).openPopup();
    });
</script>
@endsection