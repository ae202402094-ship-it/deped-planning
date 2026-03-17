@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto p-4 no-print flex justify-between items-center mb-8 bg-slate-100 rounded-2xl p-6">
    <div>
        <h4 class="font-black uppercase text-xs text-slate-500">Document Preview</h4>
        <p class="text-xs text-slate-400">Institutional Report Card Protocol</p>
    </div>
    <button onclick="window.print()" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-red-800 transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Official Copy
    </button>
</div>

<div class="max-w-4xl mx-auto bg-white shadow-2xl p-16 print:shadow-none print:p-0" id="report-card">
    {{-- 1. INSTITUTIONAL HEADER --}}
    <div class="text-center border-b-4 border-double border-slate-900 pb-8 mb-10">
        <h3 class="text-sm font-bold uppercase tracking-widest">Republic of the Philippines</h3>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-red-900">Department of Education</h1>
        <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-500 mt-1">Division of Zamboanga City</p>
        <div class="mt-8">
            <h2 class="inline-block px-6 py-2 border-2 border-slate-900 text-xl font-black uppercase tracking-tighter">
                Institutional Report Card
            </h2>
        </div>
    </div>

    {{-- 2. SCHOOL IDENTITY --}}
    <div class="grid grid-cols-2 gap-12 mb-12">
        <div>
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Official School Name</label>
            <p class="text-2xl font-black text-slate-900 uppercase leading-none">{{ $school->name }}</p>
        </div>
        <div class="text-right">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Institutional ID</label>
            <p class="text-2xl font-mono font-bold text-slate-900">{{ $school->school_id }}</p>
        </div>
    </div>

    {{-- 3. KEY METRICS GRID --}}
    <div class="grid grid-cols-4 gap-4 mb-12">
        @php
            $metrics = [
                ['label' => 'Total Enrollees', 'value' => number_format($school->no_of_enrollees)],
                ['label' => 'Teaching Personnel', 'value' => $school->no_of_teachers],
                ['label' => 'Instructional Rooms', 'value' => $school->no_of_classrooms],
                ['label' => 'Sanitation Units', 'value' => $school->no_of_toilets],
            ];
        @endphp
        @foreach($metrics as $m)
            <div class="border border-slate-200 p-4 rounded-xl text-center">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $m['label'] }}</p>
                <p class="text-xl font-black text-slate-900">{{ $m['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- 4. ANALYTICAL RATIOS --}}
    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 mb-12">
        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Efficiency Indices</h4>
        <div class="grid grid-cols-2 gap-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full border-2 border-red-800 flex items-center justify-center text-red-800 font-black">1:{{ $ratios['teacher'] }}</div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-tight text-slate-400">Teacher-Learner Ratio</p>
                    <p class="text-xs font-bold {{ $ratios['teacher'] > 45 ? 'text-red-600' : 'text-slate-700' }}">
                        {{ $ratios['teacher'] > 45 ? 'Critical Shortage' : 'Standard Compliance' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full border-2 border-slate-800 flex items-center justify-center text-slate-800 font-black">1:{{ $ratios['classroom'] }}</div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-tight text-slate-400">Classroom-Learner Ratio</p>
                    <p class="text-xs font-bold {{ $ratios['classroom'] > 40 ? 'text-red-600' : 'text-slate-700' }}">
                        {{ $ratios['classroom'] > 40 ? 'Congested' : 'Optimal Capacity' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. SIGNATORY SECTION --}}
    <div class="mt-20 flex justify-between items-end">
        <div class="w-48 text-center">
            <div class="h-24 w-24 bg-slate-100 border border-slate-200 mb-2 mx-auto flex items-center justify-center">
                <p class="text-[8px] text-slate-400 uppercase font-black tracking-tighter">System Generated<br>QR Verification</p>
            </div>
            <p class="text-[8px] font-mono text-slate-400 italic">Verify at: depedzambo.ph/verify</p>
        </div>
        <div class="text-center border-t border-slate-900 pt-2 px-12">
            <p class="text-xs font-black uppercase">Admin Officer</p>
            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Division Planning Officer</p>
        </div>
    </div>

    <div class="mt-12 text-[8px] text-slate-400 font-mono flex justify-between border-t border-slate-100 pt-4">
        <p>TIMESTAMP: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>PAGE: 01 / 01</p>
        <p>REF: IRC-{{ strtoupper(Str::random(10)) }}</p>
    </div>
</div>
@endsection