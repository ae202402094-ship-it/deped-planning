@extends('layouts.admin')

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
@endphp

<div class="max-w-6xl mx-auto px-6 py-4">
    {{-- Top Navigation & Title --}}
    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-slate-100 pb-8 gap-6">
        <div>
            <span class="text-[10px] font-black text-red-800 uppercase tracking-[0.4em] mb-2 block">System Protocol: Edit</span>
            <h1 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">{{ $school->name }}</h1>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <a href="{{ route('schools.report', $school->id) }}" target="_blank"
               class="px-6 py-3 border-2 border-slate-800 text-slate-800 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-800 hover:text-white transition-all flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5m11 4v2a4 4 0 004 4h1m-4-4l-4-4m4 4l4-4"/></svg>
                Generate Report Card
            </a>
            <a href="{{ route('admin.schools') }}" class="group flex items-center justify-center gap-2 text-[10px] font-black text-slate-400 hover:text-red-800 transition-all uppercase tracking-widest px-4 py-3">
                <span class="group-hover:-translate-x-1 transition-transform">←</span> Return to Registry
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-8 p-6 bg-red-50 border-l-4 border-red-800 rounded-2xl shadow-sm animate-pulse">
            <ul class="list-none">
                @foreach ($errors->all() as $error)
                    <li class="text-[11px] font-bold text-red-600 uppercase tracking-tight italic">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Update Form --}}
    <form action="{{ route('schools.update', $school->id) }}" method="POST" id="editSchoolForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
            {{-- Left Column --}}
            <div class="lg:col-span-8 space-y-16">
                {{-- 01. Identification --}}
                <section>
                    <div class="flex items-center gap-4 mb-8">
                        <span class="text-xs font-black text-slate-300 font-mono">01</span>
                        <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em]">Identification & Nomenclature</h3>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="relative group">
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">Official School ID</label>
                            <input type="text" name="school_id" value="{{ $school->school_id }}" 
                                   class="w-full py-2 bg-transparent text-xl font-mono font-bold text-slate-700 outline-none border-b border-slate-200 focus:border-transparent transition-all">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-800 transition-all duration-500 group-focus-within:w-full"></div>
                        </div>

                        <div class="relative group">
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">Institutional Name</label>
                            <input type="text" name="name" value="{{ $school->name }}" 
                                   class="w-full py-2 bg-transparent text-xl font-black text-slate-800 outline-none border-b border-slate-200 focus:border-transparent transition-all uppercase tracking-tight">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-800 transition-all duration-500 group-focus-within:w-full"></div>
                        </div>
                    </div>
                </section>

                {{-- 02. Physical Inventory --}}
                <section>
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-xs font-black text-slate-300 font-mono">02</span>
                        <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em]">Inventory Spreadsheet View</h3>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>

                    <div class="overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                <tr>
                                    <th class="p-4 border-r border-slate-200 text-center">Teachers</th>
                                    <th class="p-4 border-r border-slate-200 text-center">Enrollees</th>
                                    <th class="p-4 border-r border-slate-200 text-center">Classrooms</th>
                                    <th class="p-4 text-center">Toilets</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-0 border-r border-slate-100">
                                        <input type="number" name="no_of_teachers" value="{{ $school->no_of_teachers }}" class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums text-center focus:bg-red-50/30">
                                    </td>
                                    <td class="p-0 border-r border-slate-100">
                                        <input type="number" name="no_of_enrollees" value="{{ $school->no_of_enrollees }}" class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums text-center focus:bg-red-50/30">
                                    </td>
                                    <td class="p-0 border-r border-slate-100">
                                        <input type="number" name="no_of_classrooms" value="{{ $school->no_of_classrooms }}" class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums text-center focus:bg-red-50/30">
                                    </td>
                                    <td class="p-0">
                                        <input type="number" name="no_of_toilets" value="{{ $school->no_of_toilets }}" class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums text-center focus:bg-red-50/30">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Analytical Insights --}}
                <section>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 shadow-sm">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Teacher : Learner Ratio</p>
                                <p class="text-2xl font-black text-slate-800 tracking-tighter">{{ $teacherLearnerRatio }}</p>
                            </div>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 shadow-sm">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Classroom : Learner Ratio</p>
                                <p class="text-2xl font-black text-slate-800 tracking-tighter">{{ $classroomLearnerRatio }}</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div class="flex items-center gap-5">
                                <div class="h-12 w-1.5 {{ $rawClassroomRatio > 40 ? 'bg-red-600' : 'bg-green-500' }} rounded-full"></div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Capacity Verification Status</p>
                                    <p class="text-lg font-black text-slate-800">
                                        {{ $rawClassroomRatio > 40 ? 'Action Required: High Congestion' : 'Nominal Capacity: Standard' }}
                                    </p>
                                </div>
                            </div>
                            <div class="hidden sm:block">
                                <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest {{ $rawClassroomRatio > 40 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                                    {{ $rawClassroomRatio > 40 ? 'Overcrowded' : 'Optimal' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Right Column: GPS & Minimap --}}
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 shadow-sm relative overflow-hidden">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-6 text-center">Satellite Verification</h3>
                    
                    <div id="miniMap" class="w-full h-48 rounded-2xl mb-6 border border-slate-200 shadow-inner z-0 overflow-hidden bg-slate-100"></div>

                    <div class="space-y-6 mb-4 font-mono">
                        <div class="relative flex justify-between items-center border-b border-slate-200 pb-2">
                            <span id="lat_status" class="text-[9px] font-black text-slate-300 uppercase tracking-widest transition-colors">Latitude</span>
                            <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" readonly 
                                   class="bg-transparent text-right text-xs font-bold text-slate-700 outline-none border-none cursor-not-allowed opacity-60">
                        </div>
                        <div class="relative flex justify-between items-center border-b border-slate-200 pb-2">
                            <span id="lng_status" class="text-[9px] font-black text-slate-300 uppercase tracking-widest transition-colors">Longitude</span>
                            <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" readonly 
                                   class="bg-transparent text-right text-xs font-bold text-slate-700 outline-none border-none cursor-not-allowed opacity-60">
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" 
                                class="flex-1 py-4 bg-slate-800 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all">
                            Re-Pin
                        </button>
                        <button type="button" onclick="toggleManualEntry()" 
                                class="flex-1 py-4 bg-white border border-slate-200 text-slate-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:border-red-800 hover:text-red-800 transition-all">
                            Manual
                        </button>
                    </div>
                </div>

                {{-- Trigger Update Modal --}}
                <button type="button" onclick="triggerVerification()" style="background-color: #a52a2a;" 
                        class="w-full py-6 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.2em] shadow-2xl hover:scale-[1.02] transition-all">
                    Commit Registry Changes
                </button>

                {{-- Decommission Section --}}
                <div class="mt-8 pt-10 border-t border-slate-100 text-center">
                    <button type="button" onclick="openDeleteModal()" 
                            class="w-full px-10 py-3 border border-red-200 text-red-800 rounded-2xl font-black uppercase text-[9px] tracking-widest hover:bg-red-800 hover:text-white transition-all">
                        Decommission Record
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Separate Form for Decommissioning --}}
    <form action="{{ route('schools.destroy', $school->id) }}" method="POST" id="decommissionForm" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

{{-- 1. Verification Modal (For Updating) --}}
<div id="verificationModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200">
        <div class="bg-slate-800 p-8 text-center">
            <div class="inline-flex p-3 bg-red-800/20 rounded-2xl mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-white font-black uppercase tracking-widest text-sm">Official Data Verification</h3>
        </div>
        
        <div class="p-10 space-y-6 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Verify the counts for: <span id="confirmName" class="text-slate-800 font-black"></span></p>
            <div class="grid grid-cols-4 gap-4 border-y border-slate-100 py-8">
                <div><p class="text-[8px] font-black text-slate-400 uppercase">TCH</p><p id="confirmTeachers" class="text-xl font-black">0</p></div>
                <div><p class="text-[8px] font-black text-slate-400 uppercase">ENR</p><p id="confirmEnrollees" class="text-xl font-black">0</p></div>
                <div><p class="text-[8px] font-black text-slate-400 uppercase">CLS</p><p id="confirmClassrooms" class="text-xl font-black">0</p></div>
                <div><p class="text-[8px] font-black text-slate-400 uppercase">TLT</p><p id="confirmToilets" class="text-xl font-black">0</p></div>
            </div>
            <div class="flex flex-col gap-3">
                <button type="button" onclick="submitOfficialForm()" class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-colors shadow-lg">Confirm & Save Registry</button>
                <button type="button" onclick="closeVerification()" class="w-full py-3 text-slate-400 font-bold uppercase text-[9px] tracking-widest hover:text-slate-600 transition-colors">Go Back & Edit</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. Decommission Modal (For Deleting) --}}
<div id="customDeleteModal" class="fixed inset-0 z-[3000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-200">
        <div class="bg-slate-800 p-6 text-center">
            <h3 class="text-white font-black uppercase tracking-widest text-xs">System Protocol: Decommission</h3>
        </div>
        
        <div class="p-8 space-y-6 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">
                Confirming decommission of <span class="text-slate-800 font-black">{{ $school->name }}</span>. 
                Record will be moved to the institutional archive.
            </p>
            
            <div class="flex flex-col gap-3">
                <button type="button" onclick="executeDecommission()" class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-colors shadow-lg">
                    Confirm Decommission
                </button>
                <button type="button" onclick="closeDeleteModal()" class="w-full py-3 text-slate-400 font-bold uppercase text-[9px] tracking-widest hover:text-slate-600 transition-colors">
                    Abort Mission
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. MINIMAP INITIALIZATION
    let miniMap, miniMarker;
    
    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = parseFloat(document.getElementById('lat').value) || 6.9214;
        const initialLng = parseFloat(document.getElementById('lng').value) || 122.0739;

        miniMap = L.map('miniMap', {
            zoomControl: false,
            dragging: false,
            touchZoom: false,
            scrollWheelZoom: false,
            doubleClickZoom: false
        }).setView([initialLat, initialLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(miniMap);

        miniMarker = L.marker([initialLat, initialLng]).addTo(miniMap);
    });

    function updateMiniMap(lat, lng) {
        if (miniMap && miniMarker) {
            const newPos = [parseFloat(lat), parseFloat(lng)];
            miniMarker.setLatLng(newPos);
            miniMap.panTo(newPos);
        }
    }

    // 2. VERIFICATION LOGIC (Update Form)
    function triggerVerification() {
        document.getElementById('confirmName').innerText = document.querySelector('input[name="name"]').value.toUpperCase();
        document.getElementById('confirmTeachers').innerText = document.querySelector('input[name="no_of_teachers"]').value;
        document.getElementById('confirmEnrollees').innerText = document.querySelector('input[name="no_of_enrollees"]').value;
        document.getElementById('confirmClassrooms').innerText = document.querySelector('input[name="no_of_classrooms"]').value;
        document.getElementById('confirmToilets').innerText = document.querySelector('input[name="no_of_toilets"]').value;
        document.getElementById('verificationModal').classList.remove('hidden');
    }

    function closeVerification() { document.getElementById('verificationModal').classList.add('hidden'); }
    function submitOfficialForm() { document.getElementById('editSchoolForm').submit(); }

    // 3. MANUAL OVERRIDE LOGIC
    function toggleManualEntry() {
        const lat = document.getElementById('lat');
        const lng = document.getElementById('lng');
        const isReadOnly = lat.readOnly;
        lat.readOnly = !isReadOnly;
        lng.readOnly = !isReadOnly;

        [lat, lng].forEach(el => {
            el.classList.toggle('cursor-not-allowed');
            el.classList.toggle('opacity-60');
            el.classList.toggle('text-red-600');
        });
    }

    // 4. DECOMMISSION LOGIC (Delete Form)
    function openDeleteModal() {
        document.getElementById('customDeleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('customDeleteModal').classList.add('hidden');
    }

    function executeDecommission() {
        closeDeleteModal();
        if(document.getElementById('globalLoader')) {
            document.getElementById('globalLoader').classList.remove('hidden');
        }
        document.getElementById('decommissionForm').submit();
    }
</script>

@include('admin.partials.map_modal')
@endsection