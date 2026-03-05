@extends('layouts.public')

@section('content')
<div class="max-w-5xl mx-auto px-6">
    <div class="mb-6">
        <a href="{{ route('public.schools') }}" class="text-slate-500 hover:text-red-800 font-bold flex items-center gap-2 transition-colors">
            <span>←</span> BACK TO SCHOOL DIRECTORY
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
        <div style="background-color: #a52a2a;" class="p-10 text-white text-center">
            <h1 class="text-4xl font-black uppercase tracking-tighter">{{ $school->name }}</h1>
            <div class="mt-3 inline-block bg-black/20 px-4 py-1 rounded-full text-sm font-mono border border-white/10">
                SCHOOL ID: {{ $school->school_id }}
            </div>
        </div>

        <div class="p-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Teachers</p>
                <p class="text-5xl font-black text-slate-800">{{ number_format($school->no_of_teachers) }}</p>
            </div>
            <div class="text-center">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Enrollees</p>
                <p class="text-5xl font-black text-slate-800">{{ number_format($school->no_of_enrollees) }}</p>
            </div>
            <div class="text-center">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Classrooms</p>
                <p class="text-5xl font-black text-slate-800">{{ number_format($school->no_of_classrooms) }}</p>
            </div>
            <div class="text-center">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Toilets</p>
                <p class="text-5xl font-black text-slate-800">{{ number_format($school->no_of_toilets) }}</p>
            </div>
        </div>

        <div class="bg-slate-50 border-t border-slate-100 p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="flex items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200">
                <div class="h-12 w-1.5 bg-red-700 rounded-full"></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Student-Teacher Ratio</p>
                    <p class="text-2xl font-bold text-slate-800">
                        1:{{ $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : '0' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200">
                <div class="h-12 w-1.5 bg-red-700 rounded-full"></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Student-Classroom Ratio</p>
                    <p class="text-2xl font-bold text-slate-800">
                        1:{{ $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : '0' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection