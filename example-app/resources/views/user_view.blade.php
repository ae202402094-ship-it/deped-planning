@extends('layouts.public')

@section('content')
@php
    // Calculate Ratios
    $teacherRatio = $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : 0;
    $classroomRatio = $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : 0;
@endphp

<div class="max-w-6xl mx-auto px-6">
    {{-- Navigation --}}
    <div class="mb-8">
        <a href="{{ route('public.schools') }}" class="group inline-flex items-center gap-3 text-slate-400 hover:text-red-800 transition-all">
            <div class="p-2 rounded-full group-hover:bg-red-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Return to Interactive Map</span>
        </a>
    </div>

    {{-- Main Profile Card --}}
    <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200">
        
        {{-- Header Banner --}}
        <div style="background-color: #a52a2a;" class="p-12 text-white text-center relative overflow-hidden">
            {{-- Subtle Background Pattern --}}
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <svg width="100%" height="100%"><rect width="100%" height="100%" fill="url(#grid)"/><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/></pattern></defs></svg>
            </div>

            <div class="relative z-10">
                <h1 class="text-5xl font-black uppercase tracking-tighter leading-none mb-4 drop-shadow-lg">
                    {{ $school->name }}
                </h1>
                <div class="inline-flex items-center gap-2 bg-black/30 backdrop-blur-md px-6 py-2 rounded-full border border-white/20">
                    <span class="text-[10px] font-black uppercase tracking-widest text-red-300">Official Registry</span>
                    <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                    <span class="text-sm font-mono font-bold italic tracking-tighter">ID: {{ $school->school_id }}</span>
                </div>
            </div>
        </div>

        {{-- Primary Metrics Grid --}}
        <div class="p-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            @php
                $metrics = [
                    ['label' => 'Teachers', 'value' => $school->no_of_teachers, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['label' => 'Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l9-5-9-5-9 5 9 5zm0 0v6.5L7 20v-6.5l5 3.5 5-3.5z'],
                    ['label' => 'Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 4h1m4 0h1m-5 4h1m4 0h1'],
                    ['label' => 'Toilets', 'value' => $school->no_of_toilets, 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ];
            @endphp

            @foreach($metrics as $m)
                <div class="text-center group">
                    <div class="mb-4 flex justify-center">
                        <div class="p-4 bg-slate-50 rounded-2xl group-hover:bg-red-50 group-hover:text-red-800 text-slate-400 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}"/></svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $m['label'] }}</p>
                    <p class="text-5xl font-black text-slate-800 tabular-nums">{{ number_format($m['value']) }}</p>
                </div>
            @endforeach
        </div>

        {{-- Analytical Insights (Ratios) --}}
        <div class="bg-slate-50 p-10 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100">
            
            {{-- Student-Teacher Card --}}
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="h-14 w-2 {{ $teacherRatio > 45 ? 'bg-red-600' : 'bg-green-500' }} rounded-full shadow-lg"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Student-Teacher Ratio</p>
                        <p class="text-4xl font-black text-slate-800">1:{{ $teacherRatio }}</p>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <span class="text-[9px] font-black uppercase {{ $teacherRatio > 45 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $teacherRatio > 45 ? 'Critical Density' : 'Ideal Capacity' }}
                    </span>
                </div>
            </div>

            {{-- Student-Classroom Card --}}
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="h-14 w-2 {{ $classroomRatio > 40 ? 'bg-red-600' : 'bg-green-500' }} rounded-full shadow-lg"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Student-Classroom Ratio</p>
                        <p class="text-4xl font-black text-slate-800">1:{{ $classroomRatio }}</p>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <span class="text-[9px] font-black uppercase {{ $classroomRatio > 40 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $classroomRatio > 40 ? 'Overcrowded' : 'Standard' }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    {{-- Footer Branding --}}
    <div class="mt-12 text-center pb-20">
        <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.5em]">Division of Zamboanga City Data Analytics</p>
    </div>
</div>
@endsection