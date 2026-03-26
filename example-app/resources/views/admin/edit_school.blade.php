@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-sans leading-tight">
    {{-- Header Ribbon --}}
    <div class="flex justify-between items-stretch bg-white border-2 border-slate-800 mb-8 shadow-[4px_4px_0px_0px_rgba(30,41,59,1)]">
        <div class="flex items-center">
            <div class="bg-slate-800 text-white px-6 py-4 text-xs font-black uppercase tracking-tighter border-r-2 border-slate-800">
                Registry Edit Protocol
            </div>
            <h1 class="px-6 text-sm font-black text-slate-800 uppercase tracking-tight">{{ $school->name }}</h1>
        </div>
        <div class="flex border-l-2 border-slate-800">
            <button type="button" onclick="openDeleteModal()" class="px-6 py-4 text-[10px] font-black uppercase text-red-600 hover:bg-red-50 transition-colors border-r-2 border-slate-800">
                Purge Record
            </button>
            <a href="{{ route('admin.schools') }}" class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 hover:text-slate-800 transition-colors bg-slate-50">
                Close Window
            </a>
        </div>
    </div>

    <form action="{{ route('schools.update', $school->id) }}" method="POST" id="editSchoolForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-12 border-t-2 border-l-2 border-slate-800 shadow-xl bg-white">
            
            {{-- COLUMN 1: IDENTIFICATION & INVENTORY --}}
            <div class="col-span-12 lg:col-span-4 flex flex-col border-r-2 border-slate-800">
                <div class="bg-slate-100 border-b-2 border-slate-800 p-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    01 // Identification & Core Metrics
                </div>
                <div class="p-6 space-y-5 border-b-2 border-slate-800 flex-grow">
                    <div>
                        <label class="block text-[8px] font-black text-slate-400 uppercase mb-1 tracking-widest">School Reference ID</label>
                        <input type="text" name="school_id" value="{{ $school->school_id }}" 
                               class="w-full bg-slate-50 border border-slate-200 p-2 font-mono text-xs font-bold focus:outline-none focus:border-slate-800 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-slate-400 uppercase mb-1 tracking-widest">Institutional Nomenclature</label>
                        <input type="text" name="name" value="{{ $school->name }}" 
                               class="w-full bg-slate-50 border border-slate-200 p-2 text-xs font-black uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-all">
                    </div>
                </div>
                <div class="p-6 bg-slate-50/50 border-b-2 border-slate-800">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Added IDs to these inputs so JavaScript can find them --}}
                        <div class="border-b border-slate-200 pb-2">
                            <label class="block text-[7px] font-black text-slate-400 uppercase mb-1 tracking-widest">Faculty</label>
                            <input type="number" name="no_of_teachers" id="input_no_of_teachers" value="{{ $school->no_of_teachers }}" 
                                   class="w-full bg-transparent font-mono text-sm font-black text-slate-800 text-right outline-none">
                        </div>
                        <div class="border-b border-slate-200 pb-2">
                            <label class="block text-[7px] font-black text-slate-400 uppercase mb-1 tracking-widest">Learners</label>
                            <input type="number" name="no_of_enrollees" id="input_no_of_enrollees" value="{{ $school->no_of_enrollees }}" 
                                   class="w-full bg-transparent font-mono text-sm font-black text-slate-800 text-right outline-none">
                        </div>
                        <div class="border-b border-slate-200 pb-2">
                            <label class="block text-[7px] font-black text-slate-400 uppercase mb-1 tracking-widest">Spaces</label>
                            <input type="number" name="no_of_classrooms" id="input_no_of_classrooms" value="{{ $school->no_of_classrooms }}" 
                                   class="w-full bg-transparent font-mono text-sm font-black text-slate-800 text-right outline-none">
                        </div>
                        <div class="border-b border-slate-200 pb-2">
                            <label class="block text-[7px] font-black text-slate-400 uppercase mb-1 tracking-widest">Sanitary</label>
                            <input type="number" name="no_of_toilets" id="input_no_of_toilets" value="{{ $school->no_of_toilets }}" 
                                   class="w-full bg-transparent font-mono text-sm font-black text-slate-800 text-right outline-none">
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMN 2: FACILITY & SHORTAGE AUDIT --}}
            <div class="col-span-12 lg:col-span-4 flex flex-col border-r-2 border-slate-800">
                <div class="bg-slate-100 border-b-2 border-slate-800 p-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    02 // Institutional Resource Audit
                </div>
                <div class="p-6 space-y-4 border-b-2 border-slate-800">
                    <h3 class="text-[8px] font-black text-slate-400 uppercase mb-2">Utility Connectivity</h3>
                    @foreach([
                        ['name' => 'with_electricity', 'label' => 'Electricity Service', 'val' => $school->with_electricity],
                        ['name' => 'with_potable_water', 'label' => 'Water Resource', 'val' => $school->with_potable_water],
                        ['name' => 'with_internet', 'label' => 'Data Connectivity', 'val' => $school->with_internet]
                    ] as $util)
                    <div class="flex items-center justify-between border border-slate-200 p-2 bg-white">
                        <span class="text-[9px] font-bold text-slate-600 uppercase">{{ $util['label'] }}</span>
                        <select name="{{ $util['name'] }}" class="bg-slate-800 text-white text-[8px] font-black uppercase px-2 py-1 outline-none cursor-pointer">
                            <option value="1" {{ $util['val'] ? 'selected' : '' }}>Functional</option>
                            <option value="0" {{ !$util['val'] ? 'selected' : '' }}>Non-Functional</option>
                        </select>
                    </div>
                    @endforeach
                </div>
                <div class="p-6 bg-slate-50/50 border-b-2 border-slate-800 flex-grow">
                    <h3 class="text-[8px] font-black text-slate-400 uppercase mb-4 tracking-widest">Calculated Shortage Audit</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between group">
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tight">Classroom Deficit</span>
                            <input type="number" name="classroom_shortage" value="{{ $school->classroom_shortage ?? 0 }}" class="w-16 bg-white border border-slate-200 font-mono text-xs font-bold p-1 text-right focus:border-slate-800 outline-none">
                        </div>
                        <div class="flex items-center justify-between group">
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tight">Furniture Deficit</span>
                            <input type="number" name="chair_shortage" value="{{ $school->chair_shortage ?? 0 }}" class="w-16 bg-white border border-slate-200 font-mono text-xs font-bold p-1 text-right focus:border-slate-800 outline-none">
                        </div>
                        <div class="flex items-center justify-between group">
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tight">Sanitation Deficit</span>
                            <input type="number" name="toilet_shortage" value="{{ $school->toilet_shortage ?? 0 }}" class="w-16 bg-white border border-slate-200 font-mono text-xs font-bold p-1 text-right focus:border-slate-800 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMN 3: GEOSPATIAL & REMARKS --}}
            <div class="col-span-12 lg:col-span-4 flex flex-col border-r-2 border-slate-800">
                <div class="bg-slate-100 border-b-2 border-slate-800 p-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    03 // Geospatial & Technical Remarks
                </div>
                <div class="p-6 border-b-2 border-slate-800">
                    <div id="schoolMap" class="h-[200px] w-full border-2 border-slate-800 grayscale shadow-inner mb-4"></div>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div class="bg-slate-50 border border-slate-200 p-2 text-center">
                            <span class="block text-[7px] font-black text-slate-300 uppercase">Lat</span>
                            <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" class="w-full bg-transparent font-mono text-[10px] font-bold text-center outline-none" readonly>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-2 text-center">
                            <span class="block text-[7px] font-black text-slate-300 uppercase">Lng</span>
                            <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" class="w-full bg-transparent font-mono text-[10px] font-bold text-center outline-none" readonly>
                        </div>
                    </div>
                    <button type="button" onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" 
                            class="w-full bg-slate-800 text-white text-[9px] font-black uppercase tracking-widest p-3 hover:bg-black transition-all">
                        Recalibrate GIS Data
                    </button>
                </div>
                <div class="p-6 bg-slate-50/50 border-b-2 border-slate-800 flex-grow">
                    <label class="block text-[8px] font-black text-slate-400 uppercase mb-2 tracking-widest">Environmental Risk Analysis</label>
                    <textarea name="hazards" rows="5" 
                              class="w-full p-4 border border-slate-200 font-mono text-[10px] font-bold uppercase leading-relaxed focus:outline-none focus:border-slate-800 focus:bg-white transition-all">{{ $school->hazards }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="button" onclick="triggerVerification()" class="bg-slate-800 text-white px-12 py-5 text-xs font-black uppercase tracking-[0.3em] hover:bg-slate-900 transition-all shadow-[6px_6px_0px_0px_rgba(203,213,225,1)] active:translate-x-1 active:translate-y-1 active:shadow-none">
                Execute Modification Protocol
            </button>
        </div>
    </form>
</div>

{{-- Verification Modal --}}
<div id="verificationModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
    <div class="bg-white border-4 border-slate-800 shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-slate-800 text-white p-4 text-xs font-black uppercase tracking-widest flex justify-between items-center">
            <span>Audit Verification Required</span>
            <span class="text-[8px] opacity-50 font-mono">SYS_AUTH_MOD</span>
        </div>
        <div class="p-8">
            <div class="border-2 border-slate-100 p-6 font-mono text-xs mb-8">
                <div class="flex justify-between border-b border-slate-100 pb-2 mb-4">
                    <span class="text-slate-400 uppercase">Institutional Target</span>
                    <span id="confirmName" class="font-black text-slate-800"></span>
                </div>
                <div class="grid grid-cols-4 gap-4 text-center">
                    <div><p class="text-[7px] text-slate-400 uppercase mb-1">Faculty</p><p id="confirmTCH" class="text-sm font-black text-slate-800"></p></div>
                    <div><p class="text-[7px] text-slate-400 uppercase mb-1">Learners</p><p id="confirmENR" class="text-sm font-black text-slate-800"></p></div>
                    <div><p class="text-[7px] text-slate-400 uppercase mb-1">Spaces</p><p id="confirmCLS" class="text-sm font-black text-slate-800"></p></div>
                    <div><p class="text-[7px] text-slate-400 uppercase mb-1">Sanitary</p><p id="confirmTLT" class="text-sm font-black text-slate-800"></p></div>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="button" id="confirmSaveBtn" onclick="submitOfficialForm()" 
        class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all flex items-center justify-center gap-3">
    
    {{-- Hidden Spinner --}}
    <svg id="saveBtnSpinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    <span id="saveBtnText">Confirm Save</span>
</button>
                <button type="button" onclick="document.getElementById('verificationModal').classList.add('hidden')" class="flex-1 border-2 border-slate-800 text-slate-800 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">Abort</button>
            </div>
        </div>
    </div>
</div>

{{-- Decommission Protocol --}}
<form action="{{ route('schools.destroy', $school->id) }}" method="POST" id="decommissionForm" class="hidden">
    @csrf
    @method('DELETE')
</form>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function triggerVerification() {
        // Find inputs by name or newly added IDs
        document.getElementById('confirmName').innerText = document.querySelector('input[name="name"]').value.toUpperCase();
        document.getElementById('confirmTCH').innerText = document.getElementById('input_no_of_teachers').value;
        document.getElementById('confirmENR').innerText = document.getElementById('input_no_of_enrollees').value;
        document.getElementById('confirmCLS').innerText = document.getElementById('input_no_of_classrooms').value;
        document.getElementById('confirmTLT').innerText = document.getElementById('input_no_of_toilets').value;
        
        document.getElementById('verificationModal').classList.remove('hidden');
    }

    function submitOfficialForm() {
    const btn = document.getElementById('confirmSaveBtn');
    const spinner = document.getElementById('saveBtnSpinner');
    const btnText = document.getElementById('saveBtnText');

    // 1. Disable button to prevent double clicks
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');

    // 2. Show loading visual
    spinner.classList.remove('hidden');
    btnText.innerText = "Processing Update...";

    // 3. Submit the form
    document.getElementById('editSchoolForm').submit();
}
    
    function openDeleteModal() { 
        if(confirm('WARNING: PERMANENT DECOMMISSION PROTOCOL. PROCEED?')) {
            document.getElementById('decommissionForm').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('schoolMap', { scrollWheelZoom: false, zoomControl: false, dragging: false }).setView([{{ $school->latitude }}, {{ $school->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([{{ $school->latitude }}, {{ $school->longitude }}], {
            icon: L.divIcon({ html: `<div class="bg-slate-800 w-4 h-4 border-2 border-white shadow-lg"></div>` })
        }).addTo(map);
    });
</script>

@include('admin.partials.map_modal')
@endsection