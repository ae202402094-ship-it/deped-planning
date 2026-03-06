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
                <div class="flex items-center gap-4 mb-8">
                    <span class="text-xs font-black text-slate-300 font-mono">02</span>
                    <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em]">Physical Inventory Metrics</h3>
                    <div class="h-px flex-1 bg-slate-100"></div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-10">
                    @foreach([
                        'teachers' => ['label' => 'Teachers', 'ratio' => "1:$teacherRatio"],
                        'enrollees' => ['label' => 'Enrollees', 'ratio' => 'Total Count'],
                        'classrooms' => ['label' => 'Classrooms', 'ratio' => "1:$classroomRatio"],
                        'toilets' => ['label' => 'Toilets', 'ratio' => 'Sanitation']
                    ] as $field => $meta)
                    <div class="relative group">
                        <label class="block text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">{{ $meta['label'] }}</label>
                        <input type="number" name="no_of_{{ $field }}" value="{{ $school->{"no_of_$field"} }}" 
                               class="w-full py-2 bg-transparent text-3xl font-black text-slate-800 outline-none border-b border-slate-200 focus:border-transparent transition-all tabular-nums">
                        <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-800 transition-all duration-500 group-focus-within:w-full"></div>
                        <p class="mt-2 text-[8px] font-black {{ str_contains($meta['ratio'], '1:') ? 'text-red-700' : 'text-slate-300' }} uppercase tracking-widest italic">
                            {{ $meta['ratio'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- Right Column: Side Actions & GPS --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-slate-50 rounded-[2.5rem] p-10 border border-slate-100 shadow-sm relative overflow-hidden">
                {{-- Decorative GPS Grid --}}
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"></div>

                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-4 text-center">
    Coordinates
</h3>
                
                <div class="space-y-6 mb-10 font-mono">
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <span class="text-[9px] font-black text-slate-300 uppercase">Latitude</span>
                        <input type="text" name="latitude" id="lat" value="{{ $school->latitude }}" readonly class="bg-transparent text-right text-xs font-bold text-slate-700 outline-none border-none cursor-default">
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <span class="text-[9px] font-black text-slate-300 uppercase">Longitude</span>
                        <input type="text" name="longitude" id="lng" value="{{ $school->longitude }}" readonly class="bg-transparent text-right text-xs font-bold text-slate-700 outline-none border-none cursor-default">
                    </div>
                </div>

                <button type="button" onclick="openMapPopup('lat', 'lng', '{{ $school->latitude }}', '{{ $school->longitude }}')" 
                        class="w-full py-4 bg-white border border-slate-200 text-slate-500 rounded-2xl font-black uppercase text-[9px] tracking-widest hover:border-red-800 hover:text-red-800 transition-all shadow-sm">
                    Re-Pin School Location
                </button>
            </div>

            {{-- resources/views/admin/edit_school.blade.php --}}

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
</script>

@include('admin.partials.map_modal')
@endsection