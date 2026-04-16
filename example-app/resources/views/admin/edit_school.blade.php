@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-sans leading-tight text-base">
    {{-- Header Ribbon --}}
    <div class="flex justify-between items-stretch bg-white border-4 border-[#a52a2a] mb-8 shadow-[8px_8px_0px_0px_rgba(165,42,42,1)]">
        <div class="flex items-center">
            <div class="bg-[#a52a2a] text-white px-6 py-4 text-sm font-black uppercase tracking-wider border-r-4 border-[#a52a2a]">
                Registry Edit Protocol
            </div>
            <h1 class="px-6 text-lg font-black text-[#a52a2a] uppercase tracking-tight">{{ $school->name }}</h1>
        </div>
        <div class="flex border-l-4 border-[#a52a2a]">
            <button type="button" onclick="openDeleteModal()" class="px-6 py-4 text-sm font-black uppercase text-[#a52a2a] hover:bg-red-50 transition-colors border-r-4 border-[#a52a2a]">
                Purge Record
            </button>
            <a href="{{ route('admin.schools') }}" class="px-6 py-4 text-sm font-black uppercase text-slate-500 hover:text-[#a52a2a] transition-colors bg-white flex items-center">
                Close Window
            </a>
        </div>
    </div>

    <form action="{{ route('schools.update', $school->id) }}" method="POST" id="editSchoolForm">
        @csrf
        @method('PUT')

        <div class="flex flex-col border-4 border-black shadow-2xl bg-white">
            
            {{-- SECTION 1: IDENTIFICATION --}}
            <div class="flex flex-col border-b-4 border-black">
                <div class="bg-[#fdf2f2] border-b-4 border-black p-4 text-sm font-black text-[#a52a2a] uppercase tracking-widest">
                    01 // Identification & Core Metrics
                </div>
                <div class="p-8 space-y-5 border-b-2 border-slate-100 bg-white">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase mb-1 tracking-widest">School Reference ID</label>
                        <input type="text" name="school_id" value="{{ $school->school_id }}" 
                               class="w-full bg-white border-2 border-slate-200 p-3 font-mono text-base font-bold focus:outline-none focus:border-[#a52a2a] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase mb-1 tracking-widest">Institutional Nomenclature</label>
                        <input type="text" name="name" value="{{ $school->name }}" 
                               class="w-full bg-white border-2 border-slate-200 p-3 text-base font-black uppercase focus:outline-none focus:border-[#a52a2a] transition-all">
                    </div>
                </div>
                <div class="p-8 bg-slate-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach([['name' => 'no_of_teachers', 'label' => 'Faculty'], ['name' => 'no_of_enrollees', 'label' => 'Learners'], ['name' => 'no_of_classrooms', 'label' => 'Spaces'], ['name' => 'no_of_chairs', 'label' => 'Chairs'], ['name' => 'no_of_toilets', 'label' => 'Sanitary']] as $field)
                        <div class="border-b-2 border-slate-200 pb-2">
                            <label class="block text-xs font-black text-slate-500 uppercase mb-1 tracking-widest">{{ $field['label'] }}</label>
                            <input type="number" name="{{ $field['name'] }}" id="input_{{ $field['name'] }}" value="{{ $school->{$field['name']} }}" 
                                   class="w-full bg-transparent font-mono text-lg font-black text-slate-800 outline-none">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SECTION 2: UTILITIES & DEFICITS --}}
            <div class="flex flex-col border-b-4 border-black bg-white">
                <div class="bg-[#fdf2f2] border-b-4 border-black p-4 text-[10px] font-black text-[#a52a2a] uppercase tracking-[0.2em]">
                    02 // Resource & Shortage Audit
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    
                    {{-- LEFT COLUMN: UTILITIES --}}
                    <div class="p-8 border-b-2 lg:border-b-0 lg:border-r-2 border-slate-100">
                        <h3 class="text-[8px] font-black text-slate-400 uppercase mb-4 tracking-widest">Utility Connectivity</h3>
                        
                        {{-- Power Supply Dropdown --}}
                        <div class="border-2 border-slate-200 p-3 bg-white mb-4">
                            <span class="text-[9px] font-bold text-slate-600 uppercase block mb-2">Power Supply Type</span>
                            <select name="with_electricity" class="w-full bg-[#a52a2a] text-white text-[10px] font-black uppercase px-3 py-2 outline-none cursor-pointer hover:bg-black transition-colors">
                                <option value="None" {{ $school->with_electricity == 'None' ? 'selected' : '' }}>No Electricity</option>
                                <option value="Grid Connection" {{ $school->with_electricity == 'Grid Connection' ? 'selected' : '' }}>Direct Grid Connection</option>
                                <option value="Off-grid + Solar/Genset" {{ $school->with_electricity == 'Off-grid + Solar/Genset' ? 'selected' : '' }}>Off-grid + Solar/Genset</option>
                                <option value="Hybrid" {{ $school->with_electricity == 'Hybrid' ? 'selected' : '' }}>Hybrid (Grid + Solar)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Water --}}
                            <div class="flex items-center justify-between border-2 border-slate-200 p-3 bg-white">
                                <span class="text-[9px] font-bold text-slate-600 uppercase">Potable Water</span>
                                <select name="with_potable_water" class="bg-[#a52a2a] text-white text-[9px] font-black uppercase px-2 py-1.5 outline-none cursor-pointer hover:bg-black transition-colors w-24 text-center">
                                    <option value="1" {{ $school->with_potable_water ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !$school->with_potable_water ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            {{-- Internet --}}
                            <div class="flex items-center justify-between border-2 border-slate-200 p-3 bg-white">
                                <span class="text-[9px] font-bold text-slate-600 uppercase">Internet</span>
                                <select name="with_internet" class="bg-[#a52a2a] text-white text-[9px] font-black uppercase px-2 py-1.5 outline-none cursor-pointer hover:bg-black transition-colors w-24 text-center">
                                    <option value="1" {{ $school->with_internet ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !$school->with_internet ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    {{-- RIGHT COLUMN: DEFICITS --}}
                    <div class="p-8 bg-slate-50/50">
                        <h3 class="text-[8px] font-black text-slate-400 uppercase mb-4 tracking-widest">Calculated Deficits</h3>
                        <div class="space-y-3"> {{-- Aggressively tightened gap --}}
                            @foreach([
                                ['name' => 'teacher_shortage', 'label' => 'Faculty Deficit'], 
                                ['name' => 'classroom_shortage', 'label' => 'Classroom Deficit'], 
                                ['name' => 'chair_shortage', 'label' => 'Furniture Deficit'], 
                                ['name' => 'toilet_shortage', 'label' => 'Sanitation Deficit']
                            ] as $short)
                            <div class="flex flex-col border-b-2 border-slate-200 pb-2 relative">
                                <div class="flex items-center justify-between z-10 relative">
                                    <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight">{{ $short['label'] }}</span>
                                    <input type="number" name="{{ $short['name'] }}" id="input_{{ $short['name'] }}" value="{{ $school->{$short['name']} ?? 0 }}" 
                                           class="w-24 bg-white border-2 border-slate-200 font-mono text-sm font-black p-1.5 text-right focus:border-[#a52a2a] outline-none text-[#a52a2a] transition-all hover:border-[#a52a2a]/50">
                                </div>
                                {{-- Intelligent Suggestion Text --}}
                                <div class="flex justify-end mt-1 z-0 relative">
                                    <span id="suggestion_{{ $short['name'] }}" class="text-[8px] text-slate-400 font-bold uppercase tracking-widest text-right flex items-center gap-1 transition-colors">
                                        <i class="bi bi-robot"></i> System Suggestion: Calculating...
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: GEOSPATIAL & HAZARDS --}}
            <div class="flex flex-col bg-white">
                <div class="bg-[#fdf2f2] border-b-4 border-black p-4 text-sm font-black text-[#a52a2a] uppercase tracking-widest">
                    03 // Geospatial & Technical
                </div>
                <div class="p-8 border-b-2 border-slate-100 bg-white grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div id="schoolMap" class="h-[300px] w-full border-4 border-[#a52a2a] shadow-inner mb-4"></div>
                        <button type="button" onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" 
                                class="w-full bg-[#a52a2a] text-white text-sm font-black uppercase tracking-widest p-4 hover:bg-black transition-all">
                            Recalibrate GIS Data
                        </button>
                    </div>
                    <div class="flex flex-col justify-center space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white border-2 border-slate-200 p-4">
                                <span class="block text-xs font-black text-slate-500 uppercase text-center mb-2">Lat</span>
                                <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" class="w-full bg-transparent font-mono text-base text-center font-bold outline-none" readonly>
                            </div>
                            <div class="bg-white border-2 border-slate-200 p-4">
                                <span class="block text-xs font-black text-slate-500 uppercase text-center mb-2">Lng</span>
                                <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" class="w-full bg-transparent font-mono text-base text-center font-bold outline-none" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-slate-50/30 flex-grow space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase mb-2 tracking-widest">Risk Category</label>
                       <div>
    <label class="block text-[8px] font-black text-slate-400 uppercase mb-3 tracking-widest">Risk Categories (Select all that apply)</label>
    
    @php
        // Extract current hazards safely
        $currentHazards = is_array($school->hazard_type) ? $school->hazard_type : (json_decode($school->hazard_type, true) ?? [$school->hazard_type]);
        if (!is_array($currentHazards)) $currentHazards = [];
        
        // Separate default hazards from custom ones
        $defaultHazardsList = ['Flood Prone', 'Landslide Risk', 'Seismic Zone', 'Coastal Surge / Tsunami'];
        $customHazards = array_diff($currentHazards, $defaultHazardsList);
        
        // Remove the literal word "None" or "Others" if they are stuck in legacy data
        $customHazards = array_filter($customHazards, fn($h) => !in_array($h, ['None', 'Others', '']));
    @endphp

    {{-- 1. The Default Checkbox Grid (Notice there is no "Others" checkbox here anymore) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
        @foreach($defaultHazardsList as $hazard)
        <label class="flex items-center gap-3 p-4 border-2 border-slate-200 cursor-pointer hover:border-[#a52a2a] transition-all bg-white has-[:checked]:border-[#a52a2a] has-[:checked]:bg-red-50/20 group">
            <input type="checkbox" name="hazard_type[]" value="{{ $hazard }}" {{ in_array($hazard, $currentHazards) ? 'checked' : '' }} 
                   class="w-5 h-5 text-[#a52a2a] bg-slate-100 border-slate-300 rounded focus:ring-[#a52a2a]">
            <span class="text-xs font-black text-slate-700 uppercase tracking-tight group-hover:text-[#a52a2a] transition-colors">{{ $hazard }}</span>
        </label>
        @endforeach
    </div>

    {{-- 2. Dynamic Custom Hazards Section --}}
    <div class="border-t-2 border-slate-100 pt-5">
        <div class="flex justify-between items-center mb-4">
            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest">Additional Custom Hazards</label>
            <button type="button" onclick="addCustomHazardField()" class="text-[9px] font-black uppercase tracking-widest text-[#a52a2a] hover:text-black transition-colors flex items-center gap-1 bg-[#a52a2a]/10 px-3 py-1.5 rounded hover:bg-slate-200">
                <i class="bi bi-plus-circle-fill"></i> Add Custom Risk
            </button>
        </div>
        
        <div id="custom_hazards_wrapper" class="space-y-3">
            {{-- Render existing custom hazards from the database --}}
            @foreach($customHazards as $custom)
            <div class="flex items-center gap-3">
                <input type="text" name="custom_hazards[]" value="{{ $custom }}" class="flex-1 border-2 border-slate-200 bg-white text-xs font-bold text-[#a52a2a] uppercase focus:outline-none focus:border-[#a52a2a] p-3 transition-all" placeholder="E.g., Wildfire Zone">
                <button type="button" onclick="this.parentElement.remove()" class="px-4 py-3 flex items-center justify-center gap-2 bg-white text-slate-500 border-2 border-slate-200 hover:bg-slate-100 hover:text-[#a52a2a] hover:border-[#a52a2a] transition-all rounded shrink-0 text-[10px] font-black uppercase tracking-widest">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    Remove
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="button" onclick="triggerVerification()" 
                    class="bg-[#a52a2a] text-white px-16 py-6 text-base font-black uppercase tracking-widest hover:bg-black transition-all shadow-[8px_8px_0px_0px_rgba(165,42,42,1)] active:translate-x-1 active:translate-y-1 active:shadow-none w-full md:w-auto text-center border-4 border-[#a52a2a] hover:border-black">
                Execute Modification Protocol
            </button>
        </div>
    </form>
</div>

{{-- Verification Modal --}}
<div id="verificationModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-white border-4 border-[#a52a2a] shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-[#a52a2a] text-white p-4 text-sm font-black uppercase tracking-widest flex justify-between items-center">
            <span>Audit Verification Required</span>
            <span class="text-xs opacity-60 font-mono tracking-tighter italic">SYS_AUTH_MOD</span>
        </div>
        <div class="p-8 bg-white text-base">
            <div class="border-4 border-slate-100 p-6 font-mono text-sm mb-8">
                <div class="flex justify-between border-b-2 border-slate-100 pb-2 mb-4">
                    <span class="text-slate-500 uppercase italic">Institutional Target</span>
                    <span id="confirmName" class="font-black text-black"></span>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center mb-4">
                    <div><p class="text-xs text-slate-500 uppercase">TCH</p><p id="confirmTCH" class="font-black text-black text-base"></p></div>
                    <div><p class="text-xs text-slate-500 uppercase">ENR</p><p id="confirmENR" class="font-black text-black text-base"></p></div>
                    <div><p class="text-xs text-slate-500 uppercase">CLS</p><p id="confirmCLS" class="font-black text-black text-base"></p></div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center border-t-2 border-slate-100 pt-4">
                    <div><p class="text-xs text-slate-500 uppercase">CHR_DEF</p><p id="confirmCHR_DEF" class="font-black text-base"></p></div>
                    <div><p class="text-xs text-slate-500 uppercase">CLS_DEF</p><p id="confirmCLS_DEF" class="font-black text-base"></p></div>
                    <div><p class="text-xs text-slate-500 uppercase">TLT_DEF</p><p id="confirmTLT_DEF" class="font-black text-base"></p></div>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="button" id="confirmSaveBtn" onclick="submitOfficialForm()" class="flex-1 bg-[#a52a2a] text-white py-4 text-sm font-black uppercase tracking-widest hover:bg-black transition-all flex items-center justify-center gap-2 border-2 border-[#a52a2a] hover:border-black">
                    <span id="saveBtnText">Confirm Save</span>
                </button>
                <button type="button" onclick="document.getElementById('verificationModal').classList.add('hidden')" class="flex-1 border-4 border-[#a52a2a] text-[#a52a2a] py-4 text-sm font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">Abort</button>
            </div>
        </div>
    </div>
</div>

{{-- Purge Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[3000] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-white border-4 border-black shadow-2xl w-full max-w-md overflow-hidden text-base">
        <div class="bg-black text-white p-4 text-sm font-black uppercase tracking-widest flex justify-between items-center">
            <span class="text-red-500"><i class="bi bi-exclamation-triangle-fill mr-2"></i> Critical Warning</span>
            <span class="text-xs opacity-60 font-mono tracking-tighter italic">SYS_DEL_AUTH</span>
        </div>
        <div class="p-8 bg-white">
            <p class="text-base font-bold text-slate-800 mb-2">You are about to purge this institutional record.</p>
            <p class="text-sm text-slate-500 font-mono mb-8">Target: <span class="font-black text-[#a52a2a]">{{ $school->name }}</span></p>
            
            <form action="{{ route('schools.destroy', $school->id) }}" method="POST" class="flex gap-4">
                @csrf 
                @method('DELETE')
                <button type="submit" class="flex-1 bg-black text-white py-4 text-sm font-black uppercase tracking-widest hover:bg-red-600 transition-colors border-2 border-black">
                    Confirm Purge
                </button>
                <button type="button" onclick="closeDeleteModal()" class="flex-1 border-4 border-slate-200 text-slate-500 py-4 text-sm font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let overrides = { teacher: false, classroom: false, chair: false, toilet: false };

    function getRatios() {
        return {
            teacher: parseInt(localStorage.getItem('deped_ratio_teacher')) || 45,
            classroom: parseInt(localStorage.getItem('deped_ratio_classroom')) || 40,
            chair: parseInt(localStorage.getItem('deped_ratio_chair')) || 1,
            toilet: parseInt(localStorage.getItem('deped_ratio_toilet')) || 50
        };
    }

    function initOverrides() {
        const ratios = getRatios();
        const enrollees = parseInt({{ $school->no_of_enrollees ?? 0 }});
        const teachers = parseInt({{ $school->no_of_teachers ?? 0 }});
        const classrooms = parseInt({{ $school->no_of_classrooms ?? 0 }});
        const chairs = parseInt({{ $school->no_of_chairs ?? 0 }});
        const toilets = parseInt({{ $school->no_of_toilets ?? 0 }});

        const calcTeach = Math.max(0, Math.ceil(enrollees / ratios.teacher) - teachers);
        const calcClass = Math.max(0, Math.ceil(enrollees / ratios.classroom) - classrooms);
        const calcChair = Math.max(0, Math.ceil(enrollees / ratios.chair) - chairs);
        const calcToilet = Math.max(0, Math.ceil(enrollees / ratios.toilet) - toilets);

        if (parseInt(document.getElementById('input_teacher_shortage').value || 0) !== calcTeach) overrides.teacher = true;
        if (parseInt(document.getElementById('input_classroom_shortage').value || 0) !== calcClass) overrides.classroom = true;
        if (parseInt(document.getElementById('input_chair_shortage').value || 0) !== calcChair) overrides.chair = true;
        if (parseInt(document.getElementById('input_toilet_shortage').value || 0) !== calcToilet) overrides.toilet = true;
    }

    function calculateGuidedSuggestions() {
        const ratios = getRatios();
        const enrollees = parseInt(document.getElementById('input_no_of_enrollees').value) || 0;
        const teachers = parseInt(document.getElementById('input_no_of_teachers').value) || 0;
        const classrooms = parseInt(document.getElementById('input_no_of_classrooms').value) || 0;
        const chairs = parseInt(document.getElementById('input_no_of_chairs').value) || 0;
        const toilets = parseInt(document.getElementById('input_no_of_toilets').value) || 0;

        const teacherShortage = Math.max(0, Math.ceil(enrollees / ratios.teacher) - teachers);
        const classShortage = Math.max(0, Math.ceil(enrollees / ratios.classroom) - classrooms);
        const chairShortage = Math.max(0, Math.ceil(enrollees / ratios.chair) - chairs);
        const toiletShortage = Math.max(0, Math.ceil(enrollees / ratios.toilet) - toilets);

        if (!overrides.teacher) document.getElementById('input_teacher_shortage').value = teacherShortage;
        if (!overrides.classroom) document.getElementById('input_classroom_shortage').value = classShortage;
        if (!overrides.chair) document.getElementById('input_chair_shortage').value = chairShortage;
        if (!overrides.toilet) document.getElementById('input_toilet_shortage').value = toiletShortage;

        const updateSuggestionUI = (type, calculatedValue, ratioText) => {
            const el = document.getElementById(`suggestion_${type}_shortage`);
            if(!el) return;
            const manualInput = parseInt(document.getElementById(`input_${type}_shortage`).value) || 0;
            
            let statusBadge = overrides[type] 
                ? `<span class="bg-[#a52a2a] text-white px-1.5 py-0.5 rounded text-[6px] ml-2 uppercase">Manual Override</span>` 
                : `<span class="bg-emerald-500 text-white px-1.5 py-0.5 rounded text-[6px] ml-2 uppercase">Auto-Synced</span>`;
            
            el.innerHTML = `<i class="bi bi-robot"></i> System Suggestion: ${calculatedValue} (Based on ${ratioText}) ${statusBadge}`;
            
            if(manualInput !== calculatedValue) {
                el.classList.add('text-[#a52a2a]'); el.classList.remove('text-slate-400');
            } else {
                el.classList.remove('text-[#a52a2a]'); el.classList.add('text-slate-400');
            }
        };

        updateSuggestionUI('teacher', teacherShortage, `1:${ratios.teacher} ratio`);
        updateSuggestionUI('classroom', classShortage, `1:${ratios.classroom} ratio`);
        updateSuggestionUI('chair', chairShortage, `1:${ratios.chair} ratio`);
        updateSuggestionUI('toilet', toiletShortage, `1:${ratios.toilet} ratio`);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initOverrides();
        calculateGuidedSuggestions();

        // Include teachers in the trigger watch array
        const baseInputs = ['input_no_of_enrollees', 'input_no_of_teachers', 'input_no_of_classrooms', 'input_no_of_chairs', 'input_no_of_toilets'];
        baseInputs.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.addEventListener('input', calculateGuidedSuggestions);
        });

        // Watch for manual overrides
        ['teacher', 'classroom', 'chair', 'toilet'].forEach(type => {
            const el = document.getElementById(`input_${type}_shortage`);
            if(el) {
                el.addEventListener('input', () => {
                    overrides[type] = true;
                    calculateGuidedSuggestions(); 
                });
            }
        });

        const map = L.map('schoolMap', { scrollWheelZoom: false, zoomControl: false, dragging: false }).setView([{{ $school->latitude }}, {{ $school->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([{{ $school->latitude }}, {{ $school->longitude }}], {
            icon: L.divIcon({ html: `<div class="bg-[#a52a2a] w-4 h-4 border-2 border-white shadow-lg"></div>` })
        }).addTo(map);
    });

    function openDeleteModal() { document.getElementById('deleteModal').classList.remove('hidden'); }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }

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
        el.style.color = parseInt(value) > 0 ? '#a52a2a' : '#059669'; 
    }

    function submitOfficialForm() {
        const btn = document.getElementById('confirmSaveBtn');
        document.getElementById('saveBtnText').innerText = "Synchronizing...";
        btn.disabled = true; 
        document.getElementById('editSchoolForm').submit();
    }

    // Toggle the specific hazard text box when "Others" is checked
    function toggleHazardInput() {
        const othersCheckbox = document.getElementById('hazard_others');
        const container = document.getElementById('other_hazard_container');
        
        if (othersCheckbox && othersCheckbox.checked) {
            container.classList.remove('hidden');
            document.getElementById('hazard_textarea').focus();
        } else {
            container.classList.add('hidden');
        }
    }
    // Dynamically add new input fields for custom hazards
    function addCustomHazardField() {
        const wrapper = document.getElementById('custom_hazards_wrapper');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 hide-animation';
        
        div.innerHTML = `
            <input type="text" name="custom_hazards[]" class="flex-1 border-2 border-slate-200 bg-white text-xs font-bold text-[#a52a2a] uppercase focus:outline-none focus:border-[#a52a2a] p-3 transition-all" placeholder="E.g., Wildfire Zone">
            <button type="button" onclick="this.parentElement.remove()" class="px-4 py-3 flex items-center justify-center gap-2 bg-white text-slate-500 border-2 border-slate-200 hover:bg-slate-100 hover:text-[#a52a2a] hover:border-[#a52a2a] transition-all rounded shrink-0 text-[10px] font-black uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                Remove
            </button>
        `;
        wrapper.appendChild(div);
    }
</script>
@endsection