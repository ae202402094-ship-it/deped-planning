@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-sans leading-tight text-slate-800 pb-32">
    
    {{-- Header Ribbon --}}
    <div class="flex flex-col md:flex-row justify-between items-stretch bg-white border-2 border-black mb-8 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-sm overflow-hidden">
        <div class="flex items-center flex-grow">
            <div class="bg-[#a52a2a] text-white px-6 py-4 text-sm font-black uppercase tracking-wider border-r-2 border-black flex items-center h-full">
                Registry Edit Protocol
            </div>
            <h1 class="px-6 text-xl font-black text-[#a52a2a] uppercase tracking-tight">{{ $school->name }}</h1>
        </div>
        <div class="flex border-t-2 md:border-t-0 md:border-l-2 border-black bg-slate-50">
            <button type="button" onclick="openDeleteModal()" class="flex-1 md:flex-none px-6 py-4 text-sm font-black uppercase text-red-600 hover:bg-red-100 transition-colors border-r-2 border-black flex items-center justify-center gap-2">
                <i class="bi bi-trash-fill"></i> Purge
            </button>
            <a href="{{ route('admin.schools') }}" class="flex-1 md:flex-none px-6 py-4 text-sm font-black uppercase text-slate-600 hover:text-black hover:bg-slate-200 transition-colors flex items-center justify-center gap-2">
                <i class="bi bi-x-square-fill"></i> Close
            </a>
        </div>
    </div>

    <form action="{{ route('schools.update', $school->id) }}" method="POST" id="editSchoolForm" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- SECTION 1: IDENTIFICATION --}}
        <div class="bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-sm">
            <div class="bg-slate-100 border-b-2 border-black p-4 text-sm font-black text-[#a52a2a] uppercase tracking-widest flex items-center gap-3">
                <span class="bg-[#a52a2a] text-white px-2 py-1 text-xs rounded-sm">01</span> Identification & Core Metrics
            </div>
            
            <div class="p-6 md:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase mb-2 tracking-widest">School Reference ID</label>
                        <input type="text" name="school_id" value="{{ $school->school_id }}" 
                               class="w-full bg-white border-2 border-slate-300 p-3 font-mono text-base font-bold focus:outline-none focus:border-black focus:bg-[#fdf2f2] transition-colors rounded-sm shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase mb-2 tracking-widest">Institutional Nomenclature</label>
                        <input type="text" name="name" value="{{ $school->name }}" 
                               class="w-full bg-white border-2 border-slate-300 p-3 text-base font-bold uppercase focus:outline-none focus:border-black focus:bg-[#fdf2f2] transition-colors rounded-sm shadow-inner">
                    </div>
                </div>

                <div class="bg-slate-50 border-2 border-slate-200 p-6 rounded-sm mt-6">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                        @foreach([['name' => 'no_of_teachers', 'label' => 'Teachers'], ['name' => 'no_of_enrollees', 'label' => 'Enrollees'], ['name' => 'no_of_classrooms', 'label' => 'Classrooms'], ['name' => 'no_of_chairs', 'label' => 'Chairs'], ['name' => 'no_of_toilets', 'label' => 'Toilets']] as $field)
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase mb-2 tracking-widest">{{ $field['label'] }}</label>
                            <input type="number" name="{{ $field['name'] }}" id="input_{{ $field['name'] }}" value="{{ $school->{$field['name']} }}" 
                                   class="w-full bg-white border-2 border-slate-300 p-2 font-mono text-xl font-black text-center focus:outline-none focus:border-[#a52a2a] focus:text-[#a52a2a] transition-colors rounded-sm">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: UTILITIES & DEFICITS --}}
        <div class="bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-sm">
            <div class="bg-slate-100 border-b-2 border-black p-4 text-sm font-black text-[#a52a2a] uppercase tracking-widest flex items-center gap-3">
                <span class="bg-[#a52a2a] text-white px-2 py-1 text-xs rounded-sm">02</span> Resource & Shortage Audit
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 divide-y-2 lg:divide-y-0 lg:divide-x-2 divide-slate-100">
                {{-- Utilities Left Column --}}
                <div class="p-6 md:p-8 space-y-6 bg-white">
                    <h3 class="text-sm font-black text-slate-800 uppercase border-b-2 border-slate-100 pb-2">Utility Connectivity</h3>
                    
                    <div>
                        <label class="text-xs font-black text-slate-500 uppercase block mb-2">Power Supply Type</label>
                        <select name="with_electricity" class="w-full bg-slate-50 border-2 border-slate-300 text-slate-800 text-sm font-bold uppercase p-3 outline-none focus:border-black focus:bg-white cursor-pointer transition-colors rounded-sm">
                            <option value="None" {{ $school->with_electricity == 'None' ? 'selected' : '' }}>No Electricity (Off-Grid)</option>
                            <option value="Grid Connection" {{ $school->with_electricity == 'Grid Connection' ? 'selected' : '' }}>Direct Grid Connection</option>
                            <option value="Solar Powered" {{ $school->with_electricity == 'Solar Powered' ? 'selected' : '' }}>Solar / Renewable (Off-Grid)</option>
                            <option value="Generator" {{ $school->with_electricity == 'Generator' ? 'selected' : '' }}>Generator Set Only</option>
                            <option value="Hybrid" {{ $school->with_electricity == 'Hybrid' ? 'selected' : '' }}>Hybrid (Grid + Solar)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-black text-slate-500 uppercase block mb-2">Potable Water</label>
                            <select name="with_potable_water" class="w-full bg-slate-50 border-2 border-slate-300 text-slate-800 text-sm font-bold uppercase p-3 outline-none focus:border-black focus:bg-white cursor-pointer transition-colors rounded-sm">
                                <option value="1" {{ $school->with_potable_water ? 'selected' : '' }}>YES</option>
                                <option value="0" {{ !$school->with_potable_water ? 'selected' : '' }}>NO</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-black text-slate-500 uppercase block mb-2">Internet</label>
                            <select name="with_internet" class="w-full bg-slate-50 border-2 border-slate-300 text-slate-800 text-sm font-bold uppercase p-3 outline-none focus:border-black focus:bg-white cursor-pointer transition-colors rounded-sm">
                                <option value="1" {{ $school->with_internet ? 'selected' : '' }}>YES</option>
                                <option value="0" {{ !$school->with_internet ? 'selected' : '' }}>NO</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                {{-- Deficits Right Column --}}
                <div class="p-6 md:p-8 bg-slate-50/50">
                    <h3 class="text-sm font-black text-slate-800 uppercase border-b-2 border-slate-200 pb-2 mb-6">Calculated Deficits</h3>
                    <div class="space-y-5">
                        @foreach([
                            ['name' => 'teacher_shortage', 'label' => 'Faculty Deficit'], 
                            ['name' => 'classroom_shortage', 'label' => 'Classroom Shortage'], 
                            ['name' => 'chair_shortage', 'label' => 'Chair Shortage'], 
                            ['name' => 'toilet_shortage', 'label' => 'Sanitation Shortage']
                        ] as $short)
                        <div class="flex flex-col bg-white border-2 border-slate-200 p-4 rounded-sm shadow-sm relative">
                            <div class="flex items-center justify-between z-10 relative">
                                <span class="text-sm font-bold text-slate-700 uppercase tracking-tight">{{ $short['label'] }}</span>
                                <input type="number" name="{{ $short['name'] }}" id="input_{{ $short['name'] }}" value="{{ $school->{$short['name']} ?? 0 }}" 
                                       class="w-24 bg-slate-50 border-2 border-slate-300 font-mono text-base font-black p-2 text-center focus:border-[#a52a2a] outline-none text-[#a52a2a] transition-colors rounded-sm">
                            </div>
                            <div class="flex justify-between items-center mt-3 pt-3 border-t-2 border-dashed border-slate-100 z-0 relative">
                                <span id="suggestion_{{ $short['name'] }}" class="text-[11px] text-slate-500 font-bold uppercase tracking-widest flex items-center gap-1.5 transition-colors">
                                    <i class="bi bi-robot text-[#a52a2a]"></i> Calc...
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: GEOSPATIAL & HAZARDS --}}
        <div class="bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-sm">
            <div class="bg-slate-100 border-b-2 border-black p-4 text-sm font-black text-[#a52a2a] uppercase tracking-widest flex items-center gap-3">
                <span class="bg-[#a52a2a] text-white px-2 py-1 text-xs rounded-sm">03</span> Geospatial & Technical Limits
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 divide-y-2 lg:divide-y-0 lg:divide-x-2 divide-slate-100">
                <div class="p-6 md:p-8">
                    <div id="schoolMap" class="h-[250px] w-full border-2 border-black shadow-inner mb-4 rounded-sm z-0 relative"></div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Latitude</label>
                            <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" class="w-full bg-slate-50 border-2 border-slate-200 p-2 font-mono text-sm text-center font-bold outline-none rounded-sm" readonly>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Longitude</label>
                            <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" class="w-full bg-slate-50 border-2 border-slate-200 p-2 font-mono text-sm text-center font-bold outline-none rounded-sm" readonly>
                        </div>
                    </div>

                    <button type="button" onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" 
                            class="w-full bg-white border-2 border-black text-black text-sm font-black uppercase tracking-widest p-3 hover:bg-black hover:text-white transition-colors rounded-sm flex justify-center items-center gap-2">
                        <i class="bi bi-geo-alt-fill"></i> Recalibrate GIS Data
                    </button>
                </div>

                <div class="p-6 md:p-8 bg-slate-50/50 flex flex-col">
                    <label class="block text-xs font-black text-slate-500 uppercase mb-4 tracking-widest">Risk Categories</label>
                    
                    @php
                        // Extract current hazards safely
                        $currentHazards = is_array($school->hazard_type) ? $school->hazard_type : (json_decode($school->hazard_type, true) ?? [$school->hazard_type]);
                        if (!is_array($currentHazards)) $currentHazards = [];
                        
                        // Separate default hazards from custom ones
                        $defaultHazardsList = ['Flood Prone', 'Landslide Risk', 'Seismic Zone', 'Coastal Surge / Tsunami'];
                        $customHazards = array_diff($currentHazards, $defaultHazardsList);
                        
                        // Clean up legacy artifacts
                        $customHazards = array_filter($customHazards, fn($h) => !in_array($h, ['None', 'Others', '']));
                    @endphp

                  {{-- Brutalist Checkbox Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                        @foreach($defaultHazardsList as $hazard)
                        <label class="flex items-center gap-3 p-4 border-2 border-slate-300 cursor-pointer hover:border-black hover:bg-slate-50 transition-all bg-white has-[:checked]:border-[#a52a2a] has-[:checked]:shadow-[2px_2px_0px_0px_rgba(165,42,42,1)] group">
                            <input type="checkbox" name="hazard_type[]" value="{{ $hazard }}" {{ in_array($hazard, $currentHazards) ? 'checked' : '' }} 
                                   class="w-5 h-5 accent-[#a52a2a] text-[#a52a2a] bg-white border-2 border-black rounded-none focus:ring-[#a52a2a] focus:ring-offset-0 cursor-pointer">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tight group-hover:text-black has-[:checked]:text-[#a52a2a] transition-colors">{{ $hazard }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Dynamic Custom Hazards Section --}}
                    <div class="border-t-2 border-black pt-5">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Additional Custom Hazards</label>
                            <button type="button" onclick="addCustomHazardField()" class="text-[10px] font-black uppercase tracking-widest text-[#a52a2a] hover:text-black transition-colors flex items-center gap-1 bg-slate-100 border-2 border-black px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none hover:bg-slate-200">
                                <i class="bi bi-plus-lg"></i> Add Custom Risk
                            </button>
                        </div>
                        
                        <div id="custom_hazards_wrapper" class="space-y-3">
                            @foreach($customHazards as $custom)
                            <div class="flex items-center gap-3">
                                <input type="text" name="custom_hazards[]" value="{{ $custom }}" class="flex-1 border-2 border-black bg-white text-xs font-bold text-black uppercase focus:outline-none focus:bg-[#fdf2f2] p-3 transition-all" placeholder="E.g., Wildfire Zone">
                                <button type="button" onclick="this.parentElement.remove()" class="px-4 py-3 flex items-center justify-center gap-2 bg-white text-red-600 border-2 border-black hover:bg-red-600 hover:text-white transition-all shrink-0 text-[10px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                                    <i class="bi bi-x-lg"></i> Remove
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Action Bar --}}
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t-4 border-black p-4 shadow-[0px_-4px_15px_rgba(0,0,0,0.1)] flex justify-end px-4 md:px-8">
            <div class="w-full max-w-7xl mx-auto flex justify-end">
                <button type="button" onclick="triggerVerification()" 
                        class="bg-[#a52a2a] text-white px-8 md:px-16 py-4 text-base font-black uppercase tracking-widest hover:bg-black transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none w-full md:w-auto text-center border-2 border-black rounded-sm flex items-center justify-center gap-3">
                    <i class="bi bi-cloud-arrow-up-fill text-xl"></i> Execute Modification Protocol
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Verification Modal --}}
<div id="verificationModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-opacity">
    <div class="bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-lg overflow-hidden rounded-sm transform transition-transform scale-100">
        <div class="bg-[#a52a2a] text-white p-4 text-sm font-black uppercase tracking-widest flex justify-between items-center border-b-2 border-black">
            <span><i class="bi bi-shield-lock-fill mr-2"></i> Audit Verification</span>
            <span class="text-[10px] opacity-80 font-mono tracking-tighter bg-black/30 px-2 py-1 rounded">SYS_AUTH_MOD</span>
        </div>
        <div class="p-6 md:p-8 bg-white text-base">
            <div class="border-2 border-slate-200 bg-slate-50 p-6 font-mono text-sm mb-8 rounded-sm">
                <div class="flex flex-col border-b-2 border-slate-200 pb-3 mb-4">
                    <span class="text-xs text-slate-500 uppercase tracking-widest mb-1">Target Institution</span>
                    <span id="confirmName" class="font-black text-lg text-black leading-tight"></span>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center mb-4">
                    <div class="bg-white border border-slate-200 p-2 rounded"><p class="text-[10px] text-slate-500 uppercase">Teacher Shortage</p><p id="confirmTCH" class="font-black text-black text-lg"></p></div>
                    <div class="bg-white border border-slate-200 p-2 rounded"><p class="text-[10px] text-slate-500 uppercase">Enrollees</p><p id="confirmENR" class="font-black text-black text-lg"></p></div>
                    <div class="bg-white border border-slate-200 p-2 rounded"><p class="text-[10px] text-slate-500 uppercase">Classroom Shortage</p><p id="confirmCLS" class="font-black text-black text-lg"></p></div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center border-t-2 border-slate-200 pt-4">
                    <div class="bg-white border border-slate-200 p-2 rounded"><p class="text-[10px] text-slate-500 uppercase">Chair Shortage</p><p id="confirmCHR_DEF" class="font-black text-lg"></p></div>
                    <div class="bg-white border border-slate-200 p-2 rounded"><p class="text-[10px] text-slate-500 uppercase">Classroom Shortage</p><p id="confirmCLS_DEF" class="font-black text-lg"></p></div>
                    <div class="bg-white border border-slate-200 p-2 rounded"><p class="text-[10px] text-slate-500 uppercase">Toilet Shortage</p><p id="confirmTLT_DEF" class="font-black text-lg"></p></div>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="document.getElementById('verificationModal').classList.add('hidden')" class="flex-1 bg-slate-100 border-2 border-slate-300 text-slate-600 py-3 text-sm font-black uppercase tracking-widest hover:bg-slate-200 transition-colors rounded-sm">Abort</button>
                <button type="button" id="confirmSaveBtn" onclick="submitOfficialForm()" class="flex-[2] bg-[#a52a2a] text-white py-3 text-sm font-black uppercase tracking-widest hover:bg-black transition-all border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-sm">
                    <span id="saveBtnText">Confirm Sync</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Purge Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[3000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-md overflow-hidden rounded-sm">
        <div class="bg-red-600 text-white p-4 text-sm font-black uppercase tracking-widest flex justify-between items-center border-b-2 border-black">
            <span><i class="bi bi-exclamation-triangle-fill mr-2"></i> Critical Warning</span>
            <span class="text-[10px] opacity-80 font-mono tracking-tighter bg-black/20 px-2 py-1 rounded">SYS_DEL_AUTH</span>
        </div>
        <div class="p-6 md:p-8 bg-white">
            <p class="text-lg font-black text-slate-800 mb-2 leading-tight">Authorize Permanent Purge?</p>
            <p class="text-sm text-slate-600 mb-6">This action will eradicate the following institutional record from the active database:</p>
            <div class="bg-red-50 border border-red-200 p-3 rounded-sm mb-8">
                <span class="font-black text-red-700 uppercase tracking-tight">{{ $school->name }}</span>
            </div>
            
            <form action="{{ route('schools.destroy', $school->id) }}" method="POST" class="flex gap-4">
                @csrf 
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-white border-2 border-slate-300 text-slate-600 py-3 text-sm font-black uppercase tracking-widest hover:bg-slate-50 transition-colors rounded-sm">
                    Cancel
                </button>
                <button type="submit" class="flex-[1.5] bg-red-600 text-white py-3 text-sm font-black uppercase tracking-widest hover:bg-black transition-colors border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-sm">
                    Confirm Purge
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
                ? `<span class="bg-[#a52a2a] text-white px-1.5 py-0.5 rounded-sm text-[9px] ml-auto uppercase border border-black">Override</span>` 
                : `<span class="bg-emerald-600 text-white px-1.5 py-0.5 rounded-sm text-[9px] ml-auto uppercase border border-black">Synced</span>`;
            
            el.innerHTML = `<i class="bi bi-info-circle-fill text-slate-400"></i> Suggestion: ${calculatedValue} <span class="text-[9px] font-normal lowercase tracking-normal">(${ratioText})</span> ${statusBadge}`;
            
            if(manualInput !== calculatedValue) {
                el.classList.add('text-[#a52a2a]');
                el.classList.remove('text-slate-500');
            } else {
                el.classList.remove('text-[#a52a2a]');
                el.classList.add('text-slate-500');
            }
        };

        updateSuggestionUI('teacher', teacherShortage, `1:${ratios.teacher}`);
        updateSuggestionUI('classroom', classShortage, `1:${ratios.classroom}`);
        updateSuggestionUI('chair', chairShortage, `1:${ratios.chair}`);
        updateSuggestionUI('toilet', toiletShortage, `1:${ratios.toilet}`);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initOverrides();
        calculateGuidedSuggestions();

        const baseInputs = ['input_no_of_enrollees', 'input_no_of_teachers', 'input_no_of_classrooms', 'input_no_of_chairs', 'input_no_of_toilets'];
        baseInputs.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.addEventListener('input', calculateGuidedSuggestions);
        });

        ['teacher', 'classroom', 'chair', 'toilet'].forEach(type => {
            const el = document.getElementById(`input_${type}_shortage`);
            if(el) {
                el.addEventListener('input', () => {
                    overrides[type] = true;
                    calculateGuidedSuggestions(); 
                });
            }
        });

        setTimeout(() => {
            const map = L.map('schoolMap', { scrollWheelZoom: false, zoomControl: true, dragging: true }).setView([{{ $school->latitude }}, {{ $school->longitude }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([{{ $school->latitude }}, {{ $school->longitude }}], {
                icon: L.divIcon({ html: `<div class="bg-[#a52a2a] w-4 h-4 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded-full"></div>` })
            }).addTo(map);
        }, 300);
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
        document.getElementById('saveBtnText').innerHTML = "<i class='bi bi-arrow-repeat animate-spin'></i> Syncing...";
        btn.disabled = true; 
        document.getElementById('editSchoolForm').submit();
    }

    function addCustomHazardField() {
        const wrapper = document.getElementById('custom_hazards_wrapper');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3';
        
        div.innerHTML = `
            <input type="text" name="custom_hazards[]" class="flex-1 border-2 border-black bg-white text-xs font-bold text-black uppercase focus:outline-none focus:bg-[#fdf2f2] p-3 transition-all" placeholder="E.g., Wildfire Zone">
            <button type="button" onclick="this.parentElement.remove()" class="px-4 py-3 flex items-center justify-center gap-2 bg-white text-red-600 border-2 border-black hover:bg-red-600 hover:text-white transition-all shrink-0 text-[10px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                <i class="bi bi-x-lg"></i> Remove
            </button>
        `;
        wrapper.appendChild(div);
    }
</script>
@endsection