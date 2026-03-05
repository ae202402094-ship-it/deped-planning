@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <nav class="flex mb-8 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
        <a href="{{ route('admin.schools') }}" class="hover:text-red-700 transition">Registry</a>
        <span class="mx-3">/</span>
        <span class="text-slate-900">Update Profile</span>
    </nav>

    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200">
        <div style="background-color: #a52a2a;" class="p-10 text-white relative">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 relative z-10 text-center md:text-left">
                <div>
                    <span class="bg-white/20 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest border border-white/10">ID: {{ $school->school_id }}</span>
                    <h1 class="text-4xl font-black uppercase mt-3 tracking-tighter leading-none">{{ $school->name }}</h1>
                </div>
                <div class="md:text-right border-t md:border-t-0 md:border-l border-white/20 pt-4 md:pt-0 md:pl-8">
                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Last Audit Date</p>
                    <p class="font-mono text-sm">{{ $school->updated_at->format('M d, Y | h:i A') }}</p>
                </div>
            </div>
        </div>

        <form id="updateForm" action="{{ route('schools.update', $school->id) }}" method="POST" class="p-10 space-y-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b pb-2">Human Resources</h3>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Total Teachers</label>
                        <input type="number" name="no_of_teachers" id="new_teachers" value="{{ $school->no_of_teachers }}" data-old="{{ $school->no_of_teachers }}" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:border-red-700 focus:bg-white transition-all outline-none text-xl font-black shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Total Enrollees</label>
                        <input type="number" name="no_of_enrollees" id="new_enrollees" value="{{ $school->no_of_enrollees }}" data-old="{{ $school->no_of_enrollees }}" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:border-red-700 focus:bg-white transition-all outline-none text-xl font-black shadow-sm">
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b pb-2">Physical Infrastructure</h3>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Classrooms</label>
                        <input type="number" name="no_of_classrooms" id="new_classrooms" value="{{ $school->no_of_classrooms }}" data-old="{{ $school->no_of_classrooms }}" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:border-red-700 focus:bg-white transition-all outline-none text-xl font-black shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Toilets</label>
                        <input type="number" name="no_of_toilets" id="new_toilets" value="{{ $school->no_of_toilets }}" data-old="{{ $school->no_of_toilets }}" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:border-red-700 focus:bg-white transition-all outline-none text-xl font-black shadow-sm">
                    </div>
                </div>
            </div>

            <button type="button" onclick="openReviewModal()" class="w-full py-6 rounded-3xl text-white font-black uppercase tracking-[0.3em] shadow-2xl hover:scale-[1.01] transition-all flex items-center justify-center gap-3" style="background-color: #a52a2a;">
                Review & Commit Changes
            </button>
        </form>
    </div>
</div>

<div id="reviewModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl border border-white/20">
        <div class="p-8 text-center bg-slate-50 border-b">
            <h2 class="text-2xl font-black text-slate-800 uppercase italic">Confirm Audit Update</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Review the changes for {{ $school->name }}</p>
        </div>
        
        <div class="p-8 space-y-4" id="modalContent">
            </div>

        <div class="p-8 pt-0 grid grid-cols-2 gap-4">
            <button onclick="closeReviewModal()" class="py-4 rounded-2xl bg-slate-100 text-slate-500 font-black uppercase text-[10px] tracking-widest hover:bg-slate-200 transition">Cancel</button>
            <button onclick="submitForm()" class="py-4 rounded-2xl text-white font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-200 hover:bg-red-900 transition" style="background-color: #a52a2a;">Confirm Save</button>
        </div>
    </div>
</div>

<script>
function openReviewModal() {
    const fields = [
        { label: 'Teachers', old: '{{ $school->no_of_teachers }}', new: document.getElementById('new_teachers').value },
        { label: 'Enrollees', old: '{{ $school->no_of_enrollees }}', new: document.getElementById('new_enrollees').value },
        { label: 'Classrooms', old: '{{ $school->no_of_classrooms }}', new: document.getElementById('new_classrooms').value },
        { label: 'Toilets', old: '{{ $school->no_of_toilets }}', new: document.getElementById('new_toilets').value }
    ];

    let html = '';
    fields.forEach(f => {
        const hasChanged = f.old != f.new;
        html += `
            <div class="flex justify-between items-center p-4 rounded-2xl ${hasChanged ? 'bg-red-50 border border-red-100' : 'bg-slate-50 opacity-50'}">
                <span class="text-[10px] font-black uppercase text-slate-400">${f.label}</span>
                <div class="text-right">
                    <span class="text-xs font-bold line-through text-slate-300 mr-2">${f.old}</span>
                    <span class="text-lg font-black ${hasChanged ? 'text-red-700' : 'text-slate-700'}">${f.new}</span>
                </div>
            </div>
        `;
    });

    document.getElementById('modalContent').innerHTML = html;
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() { document.getElementById('reviewModal').classList.add('hidden'); }
function submitForm() { document.getElementById('updateForm').submit(); }
</script>
@endsection