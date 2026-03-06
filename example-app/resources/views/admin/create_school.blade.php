@extends('layouts.admin')

@section('content')
<style>
    /* Custom Official Document Styling */
    .line-input {
        border-bottom: 2px solid #e2e8f0;
        transition: border-color 0.3s ease;
    }
    .line-input:focus-within {
        border-color: #a52a2a;
    }
    .input-field {
        width: 100%;
        padding: 0.5rem 0;
        background: transparent;
        outline: none;
        border: none;
    }
    .underglow {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        background-color: #a52a2a;
        width: 0;
        transition: width 0.5s ease;
    }
    .group:focus-within .underglow {
        width: 100%;
    }
</style>

<div class="max-w-4xl mx-auto px-4 pb-20">
    <div class="mb-8">
        <a href="{{ route('admin.schools') }}" class="text-slate-400 hover:text-red-800 font-bold text-[10px] uppercase tracking-widest transition">
            ← Back to Registry List
        </a>
        <h1 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic mt-4">New School Registration</h1>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-200 overflow-hidden">
        {{-- Header Protocol --}}
        <div class="bg-slate-800 p-8 text-white flex justify-between items-center">
            <div>
                <p class="text-red-500 font-black uppercase text-[10px] tracking-widest">Entry Protocol</p>
                <h2 class="text-xl font-bold uppercase tracking-tight">School Information System</h2>
            </div>
            <div class="h-12 w-12 bg-red-800 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
        </div>

        <form action="{{ route('schools.store') }}" method="POST" id="registrationForm" class="p-10 space-y-12">
            @csrf
            
            {{-- 01. Identification --}}
            <section>
                <div class="flex items-center gap-4 mb-8">
                    <span class="text-xs font-black text-slate-300 font-mono text-[10px]">01</span>
                    <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em]">Identification Protocol</h3>
                    <div class="h-px flex-1 bg-slate-100"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="relative group line-input">
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">Official School ID</label>
                        <input type="text" name="school_id" required class="input-field font-mono font-bold text-slate-700">
                        <div class="underglow"></div>
                    </div>

                    <div class="relative group line-input">
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">Institutional Name</label>
                        <input type="text" name="name" required class="input-field font-black text-slate-800 uppercase tracking-tight">
                        <div class="underglow"></div>
                    </div>
                </div>
            </section>

            {{-- 02. Inventory --}}
            <section>
                <div class="flex items-center gap-4 mb-8">
                    <span class="text-xs font-black text-slate-300 font-mono text-[10px]">02</span>
                    <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em]">Initial Census Data</h3>
                    <div class="h-px flex-1 bg-slate-100"></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-10">
                    @foreach(['teachers', 'enrollees', 'classrooms', 'toilets'] as $field)
                    <div class="relative group line-input">
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">{{ $field }}</label>
                        <input type="number" name="no_of_{{ $field }}" value="0" required class="input-field text-2xl font-black text-slate-800 tabular-nums">
                        <div class="underglow"></div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- 03. Coordination --}}
            <div class="bg-slate-50 p-10 rounded-[2.5rem] border border-slate-100 relative overflow-hidden">
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"></div>
                
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-6 text-center">Satellite Coordination</h3>
                
                <div class="flex gap-4 mb-8">
                    <input type="text" name="latitude" id="reg_lat" value="6.9214" readonly class="flex-1 bg-white border border-slate-200 rounded-xl p-4 text-xs font-mono text-center shadow-sm transition-all outline-none">
                    <input type="text" name="longitude" id="reg_lng" value="122.0739" readonly class="flex-1 bg-white border border-slate-200 rounded-xl p-4 text-xs font-mono text-center shadow-sm transition-all outline-none">
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="openMapPopup('reg_lat', 'reg_lng', '6.9214', '122.0739')" 
                            class="flex-1 py-4 bg-slate-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all shadow-lg">
                        Map Picker
                    </button>
                    <button type="button" onclick="toggleManualEntry()" 
                            class="px-8 py-4 bg-white border border-slate-200 text-slate-400 rounded-2xl font-black uppercase text-[9px] tracking-widest hover:border-red-800 hover:text-red-800 transition-all">
                        Manual Type
                    </button>
                </div>
            </div>

            <button type="button" onclick="triggerVerification()" style="background-color: #a52a2a;" 
                    class="w-full py-6 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.3em] shadow-2xl shadow-red-900/30 hover:scale-[1.02] active:scale-95 transition-all">
                Commit Registration
            </button>
        </form>
    </div>
</div>

{{-- MODAL COMPONENT --}}
<div id="verificationModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200">
        <div class="bg-slate-800 p-8 text-center text-white">
            <h3 class="font-black uppercase tracking-widest text-sm">Official Data Verification</h3>
        </div>
        <div class="p-10 space-y-8">
            <p class="text-[10px] font-bold text-slate-400 uppercase text-center tracking-widest">Verify the entry for <span id="confirmName" class="text-slate-800 font-black"></span></p>
            <div class="grid grid-cols-4 gap-4 border-y border-slate-100 py-8">
                <div class="text-center"><p class="text-[8px] font-black text-slate-400 uppercase">Teachers</p><p id="confirmTeachers" class="text-xl font-black text-slate-800">0</p></div>
                <div class="text-center"><p class="text-[8px] font-black text-slate-400 uppercase">Enrollees</p><p id="confirmEnrollees" class="text-xl font-black text-slate-800">0</p></div>
                <div class="text-center"><p class="text-[8px] font-black text-slate-400 uppercase">Rooms</p><p id="confirmClassrooms" class="text-xl font-black text-slate-800">0</p></div>
                <div class="text-center"><p class="text-[8px] font-black text-slate-400 uppercase">Toilets</p><p id="confirmToilets" class="text-xl font-black text-slate-800">0</p></div>
            </div>
            <div class="flex flex-col gap-3">
                <button type="button" onclick="submitOfficialForm()" class="w-full py-5 bg-red-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all">Confirm & Save</button>
                <button type="button" onclick="closeVerification()" class="w-full py-3 text-slate-400 font-bold uppercase text-[9px] tracking-widest">Go Back</button>
            </div>
        </div>
    </div>
</div>
<div id="toast" class="fixed top-6 right-6 z-[3000] transform translate-x-12 opacity-0 transition-all duration-500 pointer-events-none">
    <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-slate-700">
        <div id="toastIcon" class="p-2 bg-red-800 rounded-lg">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p id="toastTitle" class="text-[10px] font-black uppercase tracking-widest text-red-500">System Notice</p>
            <p id="toastMessage" class="text-xs font-bold text-slate-200 mt-0.5">Manual Entry Protocol Enabled</p>
        </div>
    </div>
</div>
<script>
    function showToast(message, isWarning = false) {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMessage');
    
    toastMsg.innerText = message;
    
    // Show Toast
    toast.classList.remove('opacity-0', 'translate-x-12', 'pointer-events-none');
    toast.classList.add('opacity-100', 'translate-x-0');

    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-12', 'pointer-events-none');
        toast.classList.remove('opacity-100', 'translate-x-0');
    }, 3000);
}

function toggleManualEntry() {
    const lat = document.getElementById('reg_lat');
    const lng = document.getElementById('reg_lng');
    
    if (lat.readOnly) {
        lat.readOnly = false; 
        lng.readOnly = false;
        lat.classList.add('ring-2', 'ring-red-500/20', 'border-red-200');
        lng.classList.add('ring-2', 'ring-red-500/20', 'border-red-200');
        
        // REPLACED ALERT WITH TOAST
        showToast("Manual GPS Entry Protocol: Enabled");
    } else {
        lat.readOnly = true; 
        lng.readOnly = true;
        lat.classList.remove('ring-2', 'ring-red-500/20', 'border-red-200');
        lng.classList.remove('ring-2', 'ring-red-500/20', 'border-red-200');
        
        showToast("Map Intelligence: Restored");
    }
}
</script>

@include('admin.partials.map_modal')
@endsection