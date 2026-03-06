@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200">
        <div style="background-color: #a52a2a;" class="p-10 text-white">
            <h1 class="text-3xl font-black uppercase tracking-tighter">{{ $school->name }}</h1>
            <p class="opacity-70 font-mono text-sm uppercase">Verification & Location Update</p>
        </div>

        <form id="updateForm" action="{{ route('schools.update', $school->id) }}" method="POST" class="p-10 space-y-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b pb-2">Physical Inventory</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="no_of_teachers" id="new_teachers" value="{{ $school->no_of_teachers }}" class="bg-slate-50 p-4 rounded-2xl font-bold border-none outline-none">
                        <input type="number" name="no_of_enrollees" id="new_enrollees" value="{{ $school->no_of_enrollees }}" class="bg-slate-50 p-4 rounded-2xl font-bold border-none outline-none">
                        <input type="number" name="no_of_classrooms" id="new_classrooms" value="{{ $school->no_of_classrooms }}" class="bg-slate-50 p-4 rounded-2xl font-bold border-none outline-none">
                        <input type="number" name="no_of_toilets" id="new_toilets" value="{{ $school->no_of_toilets }}" class="bg-slate-50 p-4 rounded-2xl font-bold border-none outline-none">
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b pb-2">Coordinates</h3>
                    <div class="flex gap-4 mb-4">
                        <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" readonly class="w-full bg-slate-100 p-3 rounded-xl text-xs font-mono border-none outline-none">
                        <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" readonly class="w-full bg-slate-100 p-3 rounded-xl text-xs font-mono border-none outline-none">
                    </div>
                    
                    <div class="p-6 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 text-center">Map Intelligence</p>
                        <button type="button" onclick="openMapPopup('lat', 'lng', document.getElementById('lat').value, document.getElementById('lng').value)" 
                                class="w-full py-4 rounded-2xl bg-slate-800 text-white font-black uppercase text-[10px] tracking-widest hover:bg-black transition shadow-lg">
                            Launch Full-Screen Map Picker
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" onclick="openReviewModal()" class="w-full py-6 rounded-3xl text-white font-black uppercase tracking-widest shadow-xl hover:bg-red-900 transition" style="background-color: #a52a2a;">
                Review & Commit Changes
            </button>
        </form>
    </div>
</div>

{{-- Pulls in the logic for the popup --}}
@include('admin.partials.map_modal')

<div id="reviewModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl">
        <div class="p-8 text-center bg-slate-50 border-b">
            <h2 class="text-2xl font-black text-slate-800 uppercase italic">Confirm Audit Update</h2>
        </div>
        <div class="p-8 space-y-2 text-sm font-bold text-slate-600" id="modalContent"></div>
        <div class="p-8 pt-0 grid grid-cols-2 gap-4">
            <button type="button" onclick="closeReviewModal()" class="py-4 rounded-2xl bg-slate-100 font-bold uppercase text-[10px]">Cancel</button>
            <button type="button" onclick="document.getElementById('updateForm').submit()" style="background-color: #a52a2a;" class="py-4 rounded-2xl text-white font-bold uppercase text-[10px]">Confirm Save</button>
        </div>
    </div>
</div>

<script>
function openReviewModal() {
    const teachers = document.getElementById('new_teachers').value;
    const enrollees = document.getElementById('new_enrollees').value;
    const classrooms = document.getElementById('new_classrooms').value;
    const toilets = document.getElementById('new_toilets').value;
    const lat = document.getElementById('lat').value;
    const lng = document.getElementById('lng').value;

    document.getElementById('modalContent').innerHTML = `
        <div class="flex justify-between border-b pb-1"><span>Teachers:</span> <span>${teachers}</span></div>
        <div class="flex justify-between border-b pb-1"><span>Enrollees:</span> <span>${enrollees}</span></div>
        <div class="flex justify-between border-b pb-1"><span>Classrooms:</span> <span>${classrooms}</span></div>
        <div class="flex justify-between border-b pb-1"><span>Toilets:</span> <span>${toilets}</span></div>
        <p class="text-[10px] font-mono text-slate-400 mt-4 text-center">Geolocation: ${lat}, ${lng}</p>
    `;
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}
</script>
@endsection