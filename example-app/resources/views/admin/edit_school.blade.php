@extends('layouts.admin')

@section('content')
@php
    // Pre-calculate ratios for administrative insight
    $teacherRatio = $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : 0;
    $classroomRatio = $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : 0;
@endphp

<div class="max-w-6xl mx-auto px-6 py-4">
    {{-- Top Navigation & Title --}}
    <div class="mb-12 flex justify-between items-end border-b border-slate-100 pb-8">
        <div>
            <span class="text-[10px] font-black text-red-800 uppercase tracking-[0.4em] mb-2 block">System Protocol: Edit</span>
            <h1 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">{{ $school->name }}</h1>
        </div>
        <a href="{{ route('admin.schools') }}" class="group flex items-center gap-2 text-[10px] font-black text-slate-400 hover:text-red-800 transition-all uppercase tracking-widest">
            <span class="group-hover:-translate-x-1 transition-transform">←</span> Return to Registry
        </a>
    </div>

{{-- resources/views/admin/edit_school.blade.php --}}

@if ($errors->any())
    <div class="mb-8 p-6 bg-red-50 border-l-4 border-red-800 rounded-2xl shadow-sm animate-pulse">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h4 class="text-[10px] font-black text-red-800 uppercase tracking-widest">Update Blocked</h4>
        </div>
        <ul class="list-none">
            @foreach ($errors->all() as $error)
                <li class="text-[11px] font-bold text-red-600 uppercase tracking-tight italic">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form action="{{ route('schools.update', $school->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-16">
        @csrf
        @method('PUT')

        {{-- Left Column: Information Fields --}}
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
                    <th class="p-4 border-r border-slate-200">Teachers</th>
                    <th class="p-4 border-r border-slate-200">Enrollees</th>
                    <th class="p-4 border-r border-slate-200">Classrooms</th>
                    <th class="p-4">Toilets</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="p-0 border-r border-slate-100">
                        <input type="number" name="no_of_teachers" value="{{ $school->no_of_teachers }}" 
                               class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums focus:bg-red-50/30 transition-all text-center">
                    </td>
                    <td class="p-0 border-r border-slate-100">
                        <input type="number" name="no_of_enrollees" value="{{ $school->no_of_enrollees }}" 
                               class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums focus:bg-red-50/30 transition-all text-center">
                    </td>
                    <td class="p-0 border-r border-slate-100">
                        <input type="number" name="no_of_classrooms" value="{{ $school->no_of_classrooms }}" 
                               class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums focus:bg-red-50/30 transition-all text-center">
                    </td>
                    <td class="p-0">
                        <input type="number" name="no_of_toilets" value="{{ $school->no_of_toilets }}" 
                               class="w-full p-4 bg-transparent outline-none font-black text-xl tabular-nums focus:bg-red-50/30 transition-all text-center">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
        </div>

        {{-- Right Column: Side Actions & GPS --}}
        <div class="lg:col-span-4 space-y-8">

<div class="bg-slate-50 rounded-[2.5rem] p-10 border border-slate-100 shadow-sm relative overflow-hidden">
    {{-- Decorative GPS Grid --}}
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"></div>

    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-6 text-center">Satellite Coordination</h3>
    
    <div class="space-y-6 mb-4 font-mono">
        {{-- Latitude Field --}}
        <div class="relative flex justify-between items-center border-b border-slate-200 pb-2">
            <span id="lat_status" class="text-[9px] font-black text-slate-300 uppercase tracking-widest transition-colors">Locked by GPS</span>
            <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" readonly 
                   class="bg-transparent text-right text-xs font-bold text-slate-700 outline-none border-none cursor-not-allowed opacity-60 transition-all">
        </div>
        
        {{-- Longitude Field --}}
        <div class="relative flex justify-between items-center border-b border-slate-200 pb-2">
            <span id="lng_status" class="text-[9px] font-black text-slate-300 uppercase tracking-widest transition-colors">Locked by GPS</span>
            <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" readonly 
                   class="bg-transparent text-right text-xs font-bold text-slate-700 outline-none border-none cursor-not-allowed opacity-60 transition-all">
        </div>
    </div>

    {{-- Instructional Hint --}}
    <p id="coord_hint" class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-8 text-center italic">
        Coordinates are locked to Map Data. Click 'Manual Type' to override.
    </p>

    <div class="flex gap-4">
    {{--repin--}}

<button type="button" 
        onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" 
        class="flex-1 py-4 bg-slate-800 text-white rounded-2xl font-black">
    Re-Pin
</button>

    {{-- Manual Type Button --}}
    <button type="button" 
            onclick="toggleManualEntry()" 
            class="flex-1 py-4 bg-white border border-slate-200 text-slate-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:border-red-800 hover:text-red-800 transition-all shadow-sm">
        Manual Type
    </button>
</div>
</div>

<button type="button" onclick="triggerVerification()" style="background-color: #a52a2a;" 
        class="group w-full py-6 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.2em] shadow-2xl shadow-red-900/30 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3">
    <span class="group-hover:tracking-[0.3em] transition-all">Commit Registry Changes</span>
</button>
            
            <p class="text-center text-[8px] font-bold text-slate-300 uppercase tracking-widest">
                Last Synchronized: {{ $school->updated_at->format('M d, Y | H:i') }}
            </p>
        </div>
    </form>
</div>

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
        
        <div class="p-10 space-y-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase text-center tracking-widest leading-relaxed">
                Please verify the inventory counts for <br> 
                <span id="confirmName" class="text-slate-800 font-black"></span>
            </p>

            {{-- 4-Column Grid for All Metrics --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 border-y border-slate-100 py-8">
                <div class="text-center">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Teachers</p>
                    <p id="confirmTeachers" class="text-xl font-black text-slate-800 tabular-nums">0</p>
                </div>
                <div class="text-center">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Enrollees</p>
                    <p id="confirmEnrollees" class="text-xl font-black text-slate-800 tabular-nums">0</p>
                </div>
                <div class="text-center">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Classrooms</p>
                    <p id="confirmClassrooms" class="text-xl font-black text-slate-800 tabular-nums">0</p>
                </div>
                <div class="text-center">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Toilets</p>
                    <p id="confirmToilets" class="text-xl font-black text-slate-800 tabular-nums">0</p>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="button" onclick="submitOfficialForm()" class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-colors shadow-lg shadow-red-900/20">
                    Confirm & Save Registry
                </button>
                <button type="button" onclick="closeVerification()" class="w-full py-3 text-slate-400 font-bold uppercase text-[9px] tracking-widest hover:text-slate-600 transition-colors">
                    Go Back & Edit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function triggerVerification() {
        // Grab current values from the form inputs
        const schoolName = document.querySelector('input[name="name"]').value;
        const teachers = document.querySelector('input[name="no_of_teachers"]').value;
        const enrollees = document.querySelector('input[name="no_of_enrollees"]').value;
        const classrooms = document.querySelector('input[name="no_of_classrooms"]').value;
        const toilets = document.querySelector('input[name="no_of_toilets"]').value;

        // Inject text and formatted numbers into modal
        document.getElementById('confirmName').innerText = schoolName.toUpperCase();
        document.getElementById('confirmTeachers').innerText = Number(teachers).toLocaleString();
        document.getElementById('confirmEnrollees').innerText = Number(enrollees).toLocaleString();
        document.getElementById('confirmClassrooms').innerText = Number(classrooms).toLocaleString();
        document.getElementById('confirmToilets').innerText = Number(toilets).toLocaleString();

        // Show Modal
        document.getElementById('verificationModal').classList.remove('hidden');
    }

    function closeVerification() {
        document.getElementById('verificationModal').classList.add('hidden');
    }

    function submitOfficialForm() {
        document.querySelector('form').submit();
    }
   /**
 * Live Duplicate Check for Edit Page
 */
async function performEditDuplicateCheck(inputElement, fieldName, displayName) {
    const value = inputElement.value.trim();
    if (!value) return;

    try {
        const response = await fetch("{{ route('schools.check') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                field: fieldName, 
                value: value,
                exclude_id: "{{ $school->id }}" // Tells server to ignore THIS record
            })
        });

        const data = await response.json();

        if (data.exists) {
            inputElement.classList.add('text-red-600', 'border-red-500');
            showToast(`Conflict: The ${displayName} "${value}" is already used by another school.`, true);
        } else {
            inputElement.classList.remove('text-red-600', 'border-red-500');
        }
    } catch (error) {
        console.error('Check failed:', error);
    }
}

// Attach listeners
document.querySelector('input[name="school_id"]').addEventListener('blur', function() {
    performEditDuplicateCheck(this, 'school_id', 'School ID');
});
document.querySelector('input[name="name"]').addEventListener('blur', function() {
    performEditDuplicateCheck(this, 'name', 'Institutional Name');
});

/**
 * Manual GPS Override Toggle
 */
function toggleManualEntry() {
    const lat = document.getElementById('lat');
    const lng = document.getElementById('lng');
    const latStatus = document.getElementById('lat_status');
    const lngStatus = document.getElementById('lng_status');
    const hint = document.getElementById('coord_hint');
    
    if (lat.readOnly) {
        lat.readOnly = false; 
        lng.readOnly = false;
        [lat, lng].forEach(el => {
            el.classList.remove('cursor-not-allowed', 'opacity-60');
            el.classList.add('text-red-600', 'font-black');
        });
        latStatus.innerText = "Manual Entry Active";
        latStatus.classList.replace('text-slate-300', 'text-red-600');
        lngStatus.innerText = "Manual Entry Active";
        lngStatus.classList.replace('text-slate-300', 'text-red-600');
        hint.innerText = "Protocol Warning: Manual coordinates active. Please verify accuracy.";
        hint.classList.replace('text-slate-400', 'text-red-500');
        showToast("Manual Override Protocol: Enabled");
    } else {
        lat.readOnly = true; 
        lng.readOnly = true;
        [lat, lng].forEach(el => {
            el.classList.add('cursor-not-allowed', 'opacity-60');
            el.classList.remove('text-red-600', 'font-black');
        });
        latStatus.innerText = "Locked by GPS";
        latStatus.classList.replace('text-red-600', 'text-slate-300');
        lngStatus.innerText = "Locked by GPS";
        lngStatus.classList.replace('text-red-600', 'text-slate-300');
        hint.innerText = "Coordinates are locked to Map Data. Click 'Manual Type' to override.";
        hint.classList.replace('text-red-500', 'text-slate-400');
        showToast("Map Intelligence: Restored");
    }
}

</script>
<div class="mt-20 pt-10 border-t border-slate-100">
    <div class="flex flex-col items-center">
        <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4">Danger Zone</p>
        <button type="button" onclick="openDeleteModal()" 
                class="px-10 py-3 border border-red-200 text-red-800 rounded-2xl font-black uppercase text-[9px] tracking-widest hover:bg-red-800 hover:text-white transition-all">
            Decommission School Record
        </button>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div id="deleteModal" class="fixed inset-0 z-[3000] hidden flex items-center justify-center bg-slate-900/80 backdrop-blur-md p-4">
    <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl overflow-hidden border border-red-100">
        <div class="bg-red-800 p-8 text-center text-white">
            <h3 class="font-black uppercase tracking-widest text-sm italic">Irreversible Protocol</h3>
        </div>
        <div class="p-10 text-center space-y-8">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">
                You are about to permanently delete <br>
                <span class="text-slate-900 font-black underline">{{ $school->name }}</span> <br>
                This action cannot be undone.
            </p>

            <form action="{{ route('schools.destroy', $school->id) }}" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all">
                        Confirm Deletion
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="w-full py-3 text-slate-400 font-bold uppercase text-[9px] tracking-widest">
                        Abort Protocol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@include('admin.partials.map_modal')
@endsection