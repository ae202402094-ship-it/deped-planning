@extends('layouts.admin')

@section('content')
@php
    $teacherLearnerRatio = $school->no_of_teachers > 0 
        ? "1 : " . round($school->no_of_enrollees / $school->no_of_teachers) : "0 : 0";
    $classroomLearnerRatio = $school->no_of_classrooms > 0 
        ? "1 : " . round($school->no_of_enrollees / $school->no_of_classrooms) : "0 : 0";
@endphp

<div class="max-w-6xl mx-auto px-6 py-4">
    {{-- Header --}}
    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-slate-100 pb-8 gap-6">
        <div>
            <span class="text-[10px] font-black text-red-800 uppercase tracking-[0.4em] mb-2 block">System Protocol: Edit</span>
            <h1 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">{{ $school->name }}</h1>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <a href="{{ route('schools.report', $school->id) }}" target="_blank" class="px-6 py-3 border-2 border-slate-800 text-slate-800 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-800 hover:text-white transition-all flex items-center justify-center gap-2 shadow-sm">
                Generate Report Card
            </a>
            <a href="{{ route('admin.schools') }}" class="group flex items-center justify-center gap-2 text-[10px] font-black text-slate-400 hover:text-red-800 transition-all uppercase tracking-widest px-4 py-3">
                <span class="group-hover:-translate-x-1 transition-transform">←</span> Return to Registry
            </a>
        </div>
    </div>

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
                            <input type="text" name="school_id" value="{{ $school->school_id }}" class="w-full py-2 bg-transparent text-xl font-mono font-bold text-slate-700 outline-none border-b border-slate-200 focus:border-red-800 transition-all">
                        </div>
                        <div class="relative group">
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">Institutional Name</label>
                            <input type="text" name="name" value="{{ $school->name }}" class="w-full py-2 bg-transparent text-xl font-black text-slate-800 outline-none border-b border-slate-200 focus:border-red-800 transition-all uppercase">
                        </div>
                    </div>
                </section>

                {{-- 02. Physical Inventory + Ratios --}}
                <section>
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-xs font-black text-slate-300 font-mono">02</span>
                        <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em]">Resource Inventory</h3>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        @foreach(['no_of_teachers' => 'Teachers', 'no_of_enrollees' => 'Enrollees', 'no_of_classrooms' => 'Classrooms', 'no_of_toilets' => 'Toilets'] as $field => $label)
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm focus-within:border-red-800 transition-all">
                            <label class="block text-[8px] font-black text-slate-400 uppercase mb-2 tracking-widest">{{ $label }}</label>
                            <input type="number" name="{{ $field }}" id="input_{{ $field }}" oninput="updateRatios()" value="{{ $school->$field }}" class="w-full bg-transparent text-xl font-black text-slate-800 outline-none">
                        </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between px-6 py-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Teacher-Learner Ratio</span>
                            </div>
                            <span id="liveTeacherRatio" class="text-sm font-mono font-black text-slate-800">{{ $teacherLearnerRatio }}</span>
                        </div>

                        <div class="flex items-center justify-between px-6 py-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div id="ratioIndicatorCircle" class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Classroom-Learner Ratio</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="ratioStatusLabel" class="text-[8px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">Optimal</span>
                                <span id="liveClassroomRatio" class="text-sm font-mono font-black text-slate-800">{{ $classroomLearnerRatio }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 03. Environmental Hazards --}}
                <section>
                    <div class="flex items-center gap-4 mb-8">
                        <span class="text-xs font-black text-slate-300 font-mono">03</span>
                        <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em]">Environmental Hazards</h3>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Primary Hazard</label>
                            <select name="hazard_type" id="hazard_type" onchange="toggleOtherHazard()" class="w-full py-4 bg-white border-b-2 border-slate-100 font-black text-xs uppercase tracking-widest focus:border-red-800 outline-none transition-all">
                                <option value="None" {{ $school->hazard_type == 'None' ? 'selected' : '' }}>None</option>
                                <option value="Landslide" {{ $school->hazard_type == 'Landslide' ? 'selected' : '' }}>Landslide</option>
                                <option value="Flood" {{ $school->hazard_type == 'Flood' ? 'selected' : '' }}>Flood</option>
                                <option value="Traffic" {{ $school->hazard_type == 'Traffic' ? 'selected' : '' }}>High Traffic</option>
                                <option value="Others" {{ !in_array($school->hazard_type, ['None', 'Landslide', 'Flood', 'Traffic']) ? 'selected' : '' }}>Others</option>
                            </select>
                            <div id="other_hazard_container" class="{{ !in_array($school->hazard_type, ['None', 'Landslide', 'Flood', 'Traffic']) ? '' : 'hidden' }} mt-4">
                                <input type="text" name="hazard_others" id="hazard_others" value="{{ !in_array($school->hazard_type, ['None', 'Landslide', 'Flood', 'Traffic']) ? $school->hazard_type : '' }}" placeholder="Specify hazard..." class="w-full py-3 border-b border-red-200 text-xs font-black uppercase outline-none focus:border-red-800">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Risk Severity</label>
                            <select name="hazard_level" id="hazard_level" class="w-full py-4 bg-white border-b-2 border-slate-100 font-black text-xs uppercase tracking-widest focus:border-red-800 outline-none transition-all">
                                <option value="None" {{ $school->hazard_level == 'None' ? 'selected' : '' }}>None / Minimal</option>
                                <option value="Moderate" {{ $school->hazard_level == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="High" {{ $school->hazard_level == 'High' ? 'selected' : '' }} class="text-red-600">High Risk</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Right Column --}}
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 shadow-sm relative">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-6 text-center">Location</h3>
                    <div id="miniMap" class="w-full h-48 rounded-2xl mb-6 border border-slate-200 z-0 bg-slate-100"></div>
                    <button type="button" onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" class="w-full py-4 bg-slate-800 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all">Re-Pin</button>
                    <input type="hidden" name="latitude" id="lat" value="{{ $school->latitude }}">
                    <input type="hidden" name="longitude" id="lng" value="{{ $school->longitude }}">
                </div>

                <button type="button" onclick="triggerVerification()" style="background-color: #a52a2a;" class="w-full py-6 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.2em] shadow-2xl hover:scale-[1.02] transition-all">
                    Commit Changes
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Verification Modal --}}
<div id="verificationModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-xl rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="bg-slate-800 p-8 text-center"><h3 class="text-white font-black uppercase tracking-widest text-sm">Review Changes</h3></div>
        <div class="p-10 space-y-6 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Updating: <span id="confirmName" class="text-slate-800 font-black"></span></p>
            <div class="grid grid-cols-4 gap-4 border-y border-slate-100 py-8 text-center">
                <div><p class="text-[8px] font-black text-slate-400">TCH</p><p id="confirmTeachers" class="text-lg font-black">0</p></div>
                <div><p class="text-[8px] font-black text-slate-400">ENR</p><p id="confirmEnrollees" class="text-lg font-black">0</p></div>
                <div><p class="text-[8px] font-black text-slate-400">CLS</p><p id="confirmClassrooms" class="text-lg font-black">0</p></div>
                <div><p class="text-[8px] font-black text-slate-400">TLT</p><p id="confirmToilets" class="text-lg font-black">0</p></div>
            </div>
            <div class="py-4 bg-slate-50 rounded-2xl">
                <p class="text-[8px] font-black text-slate-400 uppercase">Hazard Status</p>
                <p id="confirmHazardSummary" class="text-xs font-black text-red-800 uppercase mt-1"></p>
            </div>
            <div class="flex flex-col gap-3">
                <button type="button" onclick="submitOfficialForm()" class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-colors">Confirm Save</button>
                <button type="button" onclick="closeVerification()" class="w-full py-3 text-slate-400 font-bold uppercase text-[9px]">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    function updateRatios() {
        const enrollees = parseFloat(document.getElementById('input_no_of_enrollees').value) || 0;
        const teachers = parseFloat(document.getElementById('input_no_of_teachers').value) || 0;
        const classrooms = parseFloat(document.getElementById('input_no_of_classrooms').value) || 0;

        const tRatio = teachers > 0 ? Math.round(enrollees / teachers) : 0;
        const cRatio = classrooms > 0 ? Math.round(enrollees / classrooms) : 0;

        document.getElementById('liveTeacherRatio').innerText = `1 : ${tRatio}`;
        document.getElementById('liveClassroomRatio').innerText = `1 : ${cRatio}`;

        const statusLabel = document.getElementById('ratioStatusLabel');
        const indicator = document.getElementById('ratioIndicatorCircle');

        if (cRatio > 45) {
            statusLabel.innerText = "Critical";
            statusLabel.className = "text-[8px] font-black uppercase px-2 py-0.5 rounded bg-red-100 text-red-700";
            indicator.className = "w-2 h-2 rounded-full bg-red-600 animate-pulse";
        } else if (cRatio > 35) {
            statusLabel.innerText = "Approaching";
            statusLabel.className = "text-[8px] font-black uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-700";
            indicator.className = "w-2 h-2 rounded-full bg-amber-500";
        } else {
            statusLabel.innerText = "Optimal";
            statusLabel.className = "text-[8px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-700";
            indicator.className = "w-2 h-2 rounded-full bg-emerald-500";
        }
    }

    function toggleOtherHazard() {
        const typeSelect = document.getElementById('hazard_type');
        const otherContainer = document.getElementById('other_hazard_container');
        if (typeSelect.value === 'Others') {
            otherContainer.classList.remove('hidden');
            document.getElementById('hazard_others').focus();
        } else {
            otherContainer.classList.add('hidden');
        }
    }

    function triggerVerification() {
        document.getElementById('confirmName').innerText = document.querySelector('input[name="name"]').value.toUpperCase();
        document.getElementById('confirmTeachers').innerText = document.getElementById('input_no_of_teachers').value;
        document.getElementById('confirmEnrollees').innerText = document.getElementById('input_no_of_enrollees').value;
        document.getElementById('confirmClassrooms').innerText = document.getElementById('input_no_of_classrooms').value;
        document.getElementById('confirmToilets').innerText = document.getElementById('input_no_of_toilets').value;
        
        let hazard = document.getElementById('hazard_type').value;
        if(hazard === 'Others') hazard = document.getElementById('hazard_others').value || 'Unspecified';
        const severity = document.getElementById('hazard_level').value;
        document.getElementById('confirmHazardSummary').innerText = `${hazard} — Severity: ${severity}`;
        
        document.getElementById('verificationModal').classList.remove('hidden');
    }

    function closeVerification() { document.getElementById('verificationModal').classList.add('hidden'); }
    function submitOfficialForm() { document.getElementById('editSchoolForm').submit(); }

    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = parseFloat(document.getElementById('lat').value) || 6.9214;
        const initialLng = parseFloat(document.getElementById('lng').value) || 122.0739;
        const miniMap = L.map('miniMap', { zoomControl: false, dragging: false }).setView([initialLat, initialLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);
        L.marker([initialLat, initialLng]).addTo(miniMap);
        updateRatios();
    });
</script>

@include('admin.partials.map_modal')
@endsection