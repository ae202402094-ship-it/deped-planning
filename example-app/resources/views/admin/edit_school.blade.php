@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-sans leading-tight">
    {{-- Header Ribbon: Auburn (#a52a2a) Structure --}}
    <div class="flex justify-between items-stretch bg-white border-4 border-[#a52a2a] mb-8 shadow-[8px_8px_0px_0px_rgba(165,42,42,1)]">
        <div class="flex items-center">
            {{-- Auburn Protocol Box --}}
            <div class="bg-[#a52a2a] text-white px-6 py-4 text-xs font-black uppercase tracking-tighter border-r-4 border-[#a52a2a]">
                Registry Edit Protocol
            </div>
            <h1 class="px-6 text-sm font-black text-[#a52a2a] uppercase tracking-tight">{{ $school->name }}</h1>
        </div>
        <div class="flex border-l-4 border-[#a52a2a]">
            {{-- Purge Button in Auburn --}}
            <button type="button" onclick="openDeleteModal()" class="px-6 py-4 text-[10px] font-black uppercase text-[#a52a2a] hover:bg-red-50 transition-colors border-r-4 border-[#a52a2a]">
                Purge Record
            </button>
            <a href="{{ route('admin.schools') }}" class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 hover:text-[#a52a2a] transition-colors bg-white flex items-center">
                Close Window
            </a>
        </div>
    </div>

    <form action="{{ route('schools.update', $school->id) }}" method="POST" id="editSchoolForm">
        @csrf
        @method('PUT')

        {{-- Vertical Form Container - Maximized Borders --}}
        <div class="flex flex-col border-4 border-black shadow-2xl bg-white">
            
            {{-- SECTION 1: IDENTIFICATION --}}
            <div class="flex flex-col border-b-4 border-black">
                <div class="bg-[#fdf2f2] border-b-4 border-black p-4 text-[10px] font-black text-[#a52a2a] uppercase tracking-[0.2em]">
                    01 // Identification & Core Metrics
                </div>
                <div class="p-8 space-y-5 border-b-2 border-slate-100 bg-white">
                    <div>
                        <label class="block text-[8px] font-black text-slate-400 uppercase mb-1 tracking-widest">School Reference ID</label>
                        <input type="text" name="school_id" value="{{ $school->school_id }}" 
                               class="w-full bg-white border-2 border-slate-200 p-3 font-mono text-xs font-bold focus:outline-none focus:border-[#a52a2a] transition-all">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-slate-400 uppercase mb-1 tracking-widest">Institutional Nomenclature</label>
                        <input type="text" name="name" value="{{ $school->name }}" 
                               class="w-full bg-white border-2 border-slate-200 p-3 text-xs font-black uppercase focus:outline-none focus:border-[#a52a2a] transition-all">
                    </div>
                </div>
                <div class="p-8 bg-slate-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach([['name' => 'no_of_teachers', 'label' => 'Faculty'], ['name' => 'no_of_enrollees', 'label' => 'Learners'], ['name' => 'no_of_classrooms', 'label' => 'Spaces'], ['name' => 'no_of_chairs', 'label' => 'Chairs'], ['name' => 'no_of_toilets', 'label' => 'Sanitary']] as $field)
                        <div class="border-b-2 border-slate-200 pb-2">
                            <label class="block text-[7px] font-black text-slate-400 uppercase mb-1 tracking-widest">{{ $field['label'] }}</label>
                            <input type="number" name="{{ $field['name'] }}" id="input_{{ $field['name'] }}" value="{{ $school->{$field['name']} }}" 
                                   class="w-full bg-transparent font-mono text-base font-black text-slate-800 outline-none">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SECTION 2: UTILITIES & DEFICITS --}}
            <div class="flex flex-col border-b-4 border-black">
                <div class="bg-[#fdf2f2] border-b-4 border-black p-4 text-[10px] font-black text-[#a52a2a] uppercase tracking-[0.2em]">
                    02 // Resource & Shortage Audit
                </div>
                <div class="p-8 space-y-6 border-b-2 border-slate-100 bg-white">
                    <h3 class="text-[8px] font-black text-slate-400 uppercase mb-2">Utility Connectivity</h3>
                    
                    {{-- Power Supply Dropdown --}}
                    <div class="border-2 border-slate-200 p-3 bg-white">
                        <span class="text-[9px] font-bold text-slate-600 uppercase block mb-2">Power Supply Type</span>
                        <select name="with_electricity" class="w-full bg-[#a52a2a] text-white text-[10px] font-black uppercase px-3 py-3 outline-none cursor-pointer hover:bg-black transition-colors">
                            <option value="None" {{ $school->with_electricity == 'None' ? 'selected' : '' }}>No Electricity (Off-Grid)</option>
                            <option value="Grid Connection" {{ $school->with_electricity == 'Grid Connection' ? 'selected' : '' }}>Direct Grid Connection</option>
                            <option value="Solar Powered" {{ $school->with_electricity == 'Solar Powered' ? 'selected' : '' }}>Solar / Renewable (Off-Grid)</option>
                            <option value="Generator" {{ $school->with_electricity == 'Generator' ? 'selected' : '' }}>Generator Set Only</option>
                            <option value="Hybrid" {{ $school->with_electricity == 'Hybrid' ? 'selected' : '' }}>Hybrid (Grid + Solar)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        @foreach([['name' => 'with_potable_water', 'label' => 'Water Resource'], ['name' => 'with_internet', 'label' => 'Data Connectivity']] as $util)
                        <div class="flex items-center justify-between border-2 border-slate-200 p-3 bg-white">
                            <span class="text-[9px] font-bold text-slate-600 uppercase">{{ $util['label'] }}</span>
                            <select name="{{ $util['name'] }}" class="bg-[#a52a2a] text-white text-[9px] font-black uppercase px-3 py-2 outline-none cursor-pointer hover:bg-black transition-colors">
                                <option value="1" {{ $school->{$util['name']} ? 'selected' : '' }}>Functional</option>
                                <option value="0" {{ !$school->{$util['name']} ? 'selected' : '' }}>Non-Functional</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="p-8 bg-slate-50/30 flex-grow">
                    <h3 class="text-[8px] font-black text-slate-400 uppercase mb-4 tracking-widest">Calculated Deficits</h3>
                    <div class="space-y-4 max-w-lg">
                        @foreach([['name' => 'classroom_shortage', 'label' => 'Classroom Deficit'], ['name' => 'chair_shortage', 'label' => 'Furniture Deficit'], ['name' => 'toilet_shortage', 'label' => 'Sanitation Deficit']] as $short)
                        <div class="flex items-center justify-between border-b-2 border-slate-200 pb-3">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">{{ $short['label'] }}</span>
                            <input type="number" name="{{ $short['name'] }}" id="input_{{ $short['name'] }}" value="{{ $school->{$short['name']} ?? 0 }}" 
                                   class="w-32 bg-white border-2 border-slate-200 font-mono text-sm font-black p-2 text-right focus:border-[#a52a2a] outline-none text-[#a52a2a]">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SECTION 3: GEOSPATIAL & HAZARDS --}}
            <div class="flex flex-col bg-white">
                <div class="bg-[#fdf2f2] border-b-4 border-black p-4 text-[10px] font-black text-[#a52a2a] uppercase tracking-[0.2em]">
                    03 // Geospatial & Technical
                </div>
                <div class="p-8 border-b-2 border-slate-100 bg-white grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div id="schoolMap" class="h-[300px] w-full border-4 border-[#a52a2a] shadow-inner mb-4"></div>
                        <button type="button" onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" 
                                class="w-full bg-[#a52a2a] text-white text-[10px] font-black uppercase tracking-widest p-4 hover:bg-black transition-all">
                            Recalibrate GIS Data
                        </button>
                    </div>
                    <div class="flex flex-col justify-center space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white border-2 border-slate-200 p-4">
                                <span class="block text-[8px] font-black text-slate-400 uppercase text-center mb-2">Lat</span>
                                <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" class="w-full bg-transparent font-mono text-sm text-center font-bold outline-none" readonly>
                            </div>
                            <div class="bg-white border-2 border-slate-200 p-4">
                                <span class="block text-[8px] font-black text-slate-400 uppercase text-center mb-2">Lng</span>
                                <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" class="w-full bg-transparent font-mono text-sm text-center font-bold outline-none" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-slate-50/30 flex-grow space-y-4">
                    <div>
                        <label class="block text-[8px] font-black text-slate-400 uppercase mb-2 tracking-widest">Risk Category</label>
                        <select id="hazard_selector" name="hazard_type" onchange="toggleHazardInput(this.value)" 
                                class="w-full bg-[#a52a2a] text-white border-4 border-[#a52a2a] p-4 text-xs font-bold uppercase focus:outline-none hover:bg-black transition-colors cursor-pointer">
                            <option value="None" {{ $school->hazard_type == 'None' ? 'selected' : '' }}>No Significant Hazards</option>
                            <option value="Flood Prone" {{ $school->hazard_type == 'Flood Prone' ? 'selected' : '' }}>Flood Prone Area</option>
                            <option value="Landslide Risk" {{ $school->hazard_type == 'Landslide Risk' ? 'selected' : '' }}>Landslide Risk</option>
                            <option value="Seismic Zone" {{ $school->hazard_type == 'Seismic Zone' ? 'selected' : '' }}>Active Seismic Zone</option>
                            <option value="Others" {{ !in_array($school->hazard_type, ['None', 'Flood Prone', 'Landslide Risk', 'Seismic Zone']) ? 'selected' : '' }}>Specify Other...</option>
                        </select>
                    </div>

                    <div id="other_hazard_container" class="{{ in_array($school->hazard_type, ['None', 'Flood Prone', 'Landslide Risk', 'Seismic Zone']) ? 'hidden' : '' }}">
                        <label class="block text-[8px] font-black text-slate-400 uppercase mb-2 tracking-widest mt-6">Technical Remarks</label>
                        <textarea name="hazards" id="hazard_textarea" rows="4" 
                                  class="w-full p-4 border-2 border-slate-200 font-mono text-xs font-bold uppercase focus:outline-none focus:border-4 focus:border-[#a52a2a] focus:bg-white transition-all">{{ $school->hazards }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Action Button --}}
        <div class="mt-8 flex justify-end">
            <button type="button" onclick="triggerVerification()" 
                    class="bg-[#a52a2a] text-white px-16 py-6 text-sm font-black uppercase tracking-[0.3em] hover:bg-black transition-all shadow-[8px_8px_0px_0px_rgba(165,42,42,1)] active:translate-x-1 active:translate-y-1 active:shadow-none w-full md:w-auto text-center border-4 border-[#a52a2a] hover:border-black">
                Execute Modification Protocol
            </button>
        </div>
    </form>
</div>

{{-- Verification Modal (Save) --}}
<div id="verificationModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-white border-4 border-[#a52a2a] shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-[#a52a2a] text-white p-4 text-xs font-black uppercase tracking-widest flex justify-between items-center">
            <span>Audit Verification Required</span>
            <span class="text-[8px] opacity-60 font-mono tracking-tighter italic">SYS_AUTH_MOD</span>
        </div>
        <div class="p-8 bg-white">
            <div class="border-4 border-slate-100 p-6 font-mono text-xs mb-8">
                <div class="flex justify-between border-b-2 border-slate-100 pb-2 mb-4">
                    <span class="text-slate-400 uppercase italic">Institutional Target</span>
                    <span id="confirmName" class="font-black text-black"></span>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center mb-4">
                    <div><p class="text-[6px] text-slate-400 uppercase">TCH</p><p id="confirmTCH" class="font-black text-black"></p></div>
                    <div><p class="text-[6px] text-slate-400 uppercase">ENR</p><p id="confirmENR" class="font-black text-black"></p></div>
                    <div><p class="text-[6px] text-slate-400 uppercase">CLS</p><p id="confirmCLS" class="font-black text-black"></p></div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center border-t-2 border-slate-100 pt-4">
                    <div><p class="text-[6px] text-slate-400 uppercase">CHR_DEF</p><p id="confirmCHR_DEF" class="font-black"></p></div>
                    <div><p class="text-[6px] text-slate-400 uppercase">CLS_DEF</p><p id="confirmCLS_DEF" class="font-black"></p></div>
                    <div><p class="text-[6px] text-slate-400 uppercase">TLT_DEF</p><p id="confirmTLT_DEF" class="font-black"></p></div>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="button" id="confirmSaveBtn" onclick="submitOfficialForm()" class="flex-1 bg-[#a52a2a] text-white py-4 text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all flex items-center justify-center gap-2 border-2 border-[#a52a2a] hover:border-black">
                    <svg id="saveBtnSpinner" class="hidden animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span id="saveBtnText">Confirm Save</span>
                </button>
                <button type="button" onclick="document.getElementById('verificationModal').classList.add('hidden')" class="flex-1 border-4 border-[#a52a2a] text-[#a52a2a] py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">Abort</button>
            </div>
        </div>
    </div>
</div>

{{-- PURGE CONFIRMATION MODAL --}}
<div id="deleteModal" class="fixed inset-0 z-[3000] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-white border-4 border-black shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-black text-white p-4 text-xs font-black uppercase tracking-widest flex justify-between items-center">
            <span class="text-red-500"><i class="bi bi-exclamation-triangle-fill mr-2"></i> Critical Warning</span>
            <span class="text-[8px] opacity-60 font-mono tracking-tighter italic">SYS_DEL_AUTH</span>
        </div>
        <div class="p-8 bg-white">
            <p class="text-sm font-bold text-slate-800 mb-2">You are about to purge this institutional record.</p>
            <p class="text-xs text-slate-500 font-mono mb-8">Target: <span class="font-black text-[#a52a2a]">{{ $school->name }}</span></p>
            
            <form action="{{ route('schools.destroy', $school->id) }}" method="POST" class="flex gap-4">
                @csrf 
                @method('DELETE')
                <button type="submit" class="flex-1 bg-black text-white py-4 text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-colors border-2 border-black">
                    Confirm Purge
                </button>
                <button type="button" onclick="closeDeleteModal()" class="flex-1 border-4 border-slate-200 text-slate-500 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Delete Modal Logic
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Save Verification Logic
    function triggerVerification() {
        const name = document.querySelector('input[name="name"]').value.toUpperCase();
        const tch = document.getElementById('input_no_of_teachers').value;
        const enr = document.getElementById('input_no_of_enrollees').value;
        const cls = document.getElementById('input_no_of_classrooms').value;
        const chrDef = document.getElementById('input_chair_shortage').value;
        const clsDef = document.getElementById('input_classroom_shortage').value;
        const tltDef = document.getElementById('input_toilet_shortage').value;

        document.getElementById('confirmName').innerText = name;
        document.getElementById('confirmTCH').innerText = tch;
        document.getElementById('confirmENR').innerText = enr;
        document.getElementById('confirmCLS').innerText = cls;

        updateIndicator('confirmCHR_DEF', chrDef);
        updateIndicator('confirmCLS_DEF', clsDef);
        updateIndicator('confirmTLT_DEF', tltDef);

        document.getElementById('verificationModal').classList.remove('hidden');
    }

    function updateIndicator(id, value) {
        const el = document.getElementById(id);
        el.innerText = value;
        el.style.color = parseInt(value) > 0 ? '#a52a2a' : '#059669'; // Auburn if shortage, Green if OK
    }

    function submitOfficialForm() {
        const btn = document.getElementById('confirmSaveBtn');
        const spinner = document.getElementById('saveBtnSpinner');
        const btnText = document.getElementById('saveBtnText');
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.innerText = "Synchronizing...";
        document.getElementById('editSchoolForm').submit();
    }

    // Map Logic
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('schoolMap', { scrollWheelZoom: false, zoomControl: false, dragging: false }).setView([{{ $school->latitude }}, {{ $school->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([{{ $school->latitude }}, {{ $school->longitude }}], {
            icon: L.divIcon({ html: `<div class="bg-[#a52a2a] w-4 h-4 border-2 border-white shadow-lg"></div>` })
        }).addTo(map);
    });

    function toggleHazardInput(val) {
        const container = document.getElementById('other_hazard_container');
        if (val === 'Others') {
            container.classList.remove('hidden');
            document.getElementById('hazard_textarea').focus();
        } else {
            container.classList.add('hidden');
        }
    }
</script>
@endsection