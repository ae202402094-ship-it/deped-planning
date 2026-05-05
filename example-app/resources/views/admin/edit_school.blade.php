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
                <div class="p-6 md:p-8 space-y-6 bg-white border-r-2 border-black">
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
                
                {{-- Deficits Right Column (Manual Input Only) --}}
                <div class="p-6 md:p-8 bg-slate-50/50">
                    <h3 class="text-sm font-black text-slate-800 uppercase border-b-2 border-slate-200 pb-2 mb-6">Manual Ratio & Shortage Input</h3>
                    <div class="space-y-4">
                        @foreach([
                            ['name' => 'teacher', 'label' => 'Teacher'], 
                            ['name' => 'classroom', 'label' => 'Classroom'], 
                            ['name' => 'chair', 'label' => 'Chair'], 
                            ['name' => 'toilet', 'label' => 'Toilet']
                        ] as $short)
                        <div class="bg-white border-2 border-black p-4 rounded-sm shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <span class="text-sm font-black text-slate-700 uppercase tracking-tight">{{ $short['label'] }} Status</span>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Manual Ratio</label>
                                        <input type="text" name="{{ $short['name'] }}_ratio" value="{{ $school->{$short['name'].'_ratio'} ?? '' }}" 
                                               placeholder="e.g. 1:45"
                                               class="w-full md:w-28 bg-slate-50 border-2 border-slate-300 font-mono text-xs font-bold p-2 text-center focus:border-black outline-none transition-colors rounded-sm">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Shortage Unit</label>
                                        <input type="number" name="{{ $short['name'] }}_shortage" value="{{ $school->{$short['name'].'_shortage'} ?? 0 }}" 
                                               class="w-full md:w-24 bg-slate-50 border-2 border-slate-300 font-mono text-xs font-black p-2 text-center focus:border-[#a52a2a] outline-none text-[#a52a2a] transition-colors rounded-sm">
                                    </div>
                                </div>
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
                            <input type="number" step="any" name="latitude" id="lat" value="{{ $school->latitude }}" 
                                   oninput="updateMapFromInputs()" 
                                   class="w-full bg-white border-2 border-slate-300 p-2 font-mono text-sm text-center font-bold outline-none focus:border-black rounded-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Longitude</label>
                            <input type="number" step="any" name="longitude" id="lng" value="{{ $school->longitude }}" 
                                   oninput="updateMapFromInputs()" 
                                   class="w-full bg-white border-2 border-slate-300 p-2 font-mono text-sm text-center font-bold outline-none focus:border-black rounded-sm">
                        </div>
                    </div>

                    <button type="button" onclick="openGisModal()" 
                            class="w-full bg-white border-2 border-black text-black text-sm font-black uppercase tracking-widest p-3 hover:bg-black hover:text-white transition-colors rounded-sm flex justify-center items-center gap-2 relative z-10">
                        <i class="bi bi-geo-alt-fill"></i> Execute GIS Recalibration
                    </button>
                </div>

                <div class="p-6 md:p-8 bg-slate-50/50 flex flex-col">
                    <label class="block text-xs font-black text-slate-500 uppercase mb-4 tracking-widest">Risk Categories</label>
                    
                    @php
                        $currentHazards = is_array($school->hazard_type) ? $school->hazard_type : (json_decode($school->hazard_type, true) ?? [$school->hazard_type]);
                        if (!is_array($currentHazards)) $currentHazards = [];
                        $defaultHazardsList = ['Flood Prone', 'Landslide Risk', 'Seismic Zone', 'Coastal Surge / Tsunami'];
                        $customHazards = array_diff($currentHazards, $defaultHazardsList);
                        $customHazards = array_filter($customHazards, fn($h) => !in_array($h, ['None', 'Others', '']));
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                        @foreach($defaultHazardsList as $hazard)
                        <label class="flex items-center gap-3 p-4 border-2 border-slate-300 cursor-pointer hover:border-black hover:bg-slate-50 transition-all bg-white has-[:checked]:border-[#a52a2a] has-[:checked]:shadow-[2px_2px_0px_0px_rgba(165,42,42,1)] group">
                            <input type="checkbox" name="hazard_type[]" value="{{ $hazard }}" {{ in_array($hazard, $currentHazards) ? 'checked' : '' }} 
                                   class="w-5 h-5 accent-[#a52a2a] text-[#a52a2a] bg-white border-2 border-black rounded-none focus:ring-[#a52a2a] focus:ring-offset-0 cursor-pointer">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tight group-hover:text-black has-[:checked]:text-[#a52a2a] transition-colors">{{ $hazard }}</span>
                        </label>
                        @endforeach
                    </div>

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
                <div class="p-2 bg-white border border-slate-200 rounded text-center">
                    <p class="text-xs text-slate-500 uppercase mb-2">Authorized update for registry cycle 2026</p>
                    <p class="text-xs font-bold text-black italic">Ensure all manual shortage and ratio data are validated against division audits before syncing.</p>
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

<link href='https://unpkg.com/leaflet.fullscreen@1.0.2/dist/leaflet.fullscreen.css' rel='stylesheet' />
<script src='https://unpkg.com/leaflet.fullscreen@1.0.2/dist/Leaflet.fullscreen.min.js'></script>

<script>
    let editMarker;
    let editMap;

    function updateMapFromInputs() {
        const lat = parseFloat(document.getElementById('lat').value);
        const lng = parseFloat(document.getElementById('lng').value);

        if (!isNaN(lat) && !isNaN(lng)) {
            const newPos = [lat, lng];
            editMap.setView(newPos, editMap.getZoom());
            if (editMarker) {
                editMarker.setLatLng(newPos);
            } else {
                editMarker = L.marker(newPos).addTo(editMap);
            }
        }
    }

    function openGisModal() {
        document.getElementById('gisGatewayModal').classList.remove('hidden');
    }

    function closeGisGateway() {
        document.getElementById('gisGatewayModal').classList.add('hidden');
    }

    function startRecalibration() {
        closeGisGateway();
        document.getElementById('lat').classList.add('ring-2', 'ring-[#a52a2a]', 'ring-offset-2');
        document.getElementById('lng').classList.add('ring-2', 'ring-[#a52a2a]', 'ring-offset-2');
        document.getElementById('schoolMap').scrollIntoView({ behavior: 'smooth' });

        editMap.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;

            if (editMarker) {
                editMarker.setLatLng(e.latlng);
            } else {
                editMarker = L.marker(e.latlng).addTo(editMap);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            editMap = L.map('schoolMap', { 
                scrollWheelZoom: false, 
                zoomControl: true, 
                dragging: true,
                fullscreenControl: true,
                fullscreenControlOptions: { position: 'topleft' }
            }).setView([{{ $school->latitude }}, {{ $school->longitude }}], 15);
            
            const streetView = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' });
            const satelliteView = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Tiles &copy; Esri' });

            streetView.addTo(editMap);
            L.control.layers({ "Street Map": streetView, "Satellite View": satelliteView }).addTo(editMap);
            
            editMarker = L.marker([{{ $school->latitude }}, {{ $school->longitude }}], {
                icon: L.divIcon({ 
                    html: `<div class="bg-[#a52a2a] w-4 h-4 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded-full"></div>` 
                })
            }).addTo(editMap);
        }, 300);
    });

    function openDeleteModal() { document.getElementById('deleteModal').classList.remove('hidden'); }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }

    function triggerVerification() {
        const name = document.querySelector('input[name="name"]').value.toUpperCase();
        document.getElementById('confirmName').innerText = name;
        document.getElementById('verificationModal').classList.remove('hidden');
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
        div.innerHTML = `<input type="text" name="custom_hazards[]" class="flex-1 border-2 border-black bg-white text-xs font-bold text-black uppercase p-3" placeholder="E.g., Wildfire Zone">
                         <button type="button" onclick="this.parentElement.remove()" class="px-4 py-3 bg-white text-red-600 border-2 border-black">Remove</button>`;
        wrapper.appendChild(div);
    }
</script>

<div id="gisGatewayModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-md overflow-hidden rounded-sm">
        <div class="bg-[#a52a2a] text-white p-4 text-sm font-black uppercase tracking-widest flex justify-between items-center border-b-2 border-black">
            <span><i class="bi bi-geo-fill mr-2"></i> GIS Protocol</span>
            <span class="text-[10px] opacity-80 font-mono">SYS_GEO_INIT</span>
        </div>
        <div class="p-6 md:p-8 bg-white">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-red-50 p-4 border-2 border-[#a52a2a] text-[#a52a2a]">
                    <i class="bi bi-pin-map-fill text-3xl"></i>
                </div>
                <div>
                    <p class="text-lg font-black text-slate-800 leading-tight uppercase">Enter Recalibration Mode?</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Manual GPS override requested</p>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeGisGateway()" class="flex-1 bg-white border-2 border-slate-300 text-slate-600 py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">Abort</button>
                <button type="button" onclick="startRecalibration()" class="flex-[1.5] bg-black text-white py-3 text-xs font-black uppercase tracking-widest hover:bg-[#a52a2a] transition-colors border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] active:translate-x-1 active:translate-y-1 active:shadow-none">Initialize Map</button>
            </div>
        </div>
    </div>
</div>
@include('admin.partials.map_modal')
@endsection