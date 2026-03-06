@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">School Management</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Division of Zamboanga City</p>
        </div>

        <div class="flex gap-4 items-center">
            {{-- NEW: Link to the separate registration page --}}
            <a href="{{ route('schools.create') }}" style="background-color: #a52a2a;" class="text-white px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-900 transition shadow-lg">
                + Register School
            </a>
            
            <form action="{{ route('admin.schools') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID/Name..." 
                       class="w-64 border border-slate-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none shadow-sm">
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl font-bold uppercase text-[10px] tracking-widest">
                    Find
                </button>
            </form>
        </div>
    </div>
    
    {{-- SCHOOLS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($schools as $school)
            <a href="{{ route('schools.edit', $school->id) }}" class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">ID: {{ $school->school_id }}</span>
                        <span class="text-red-700 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-black uppercase tracking-widest">Edit Profile →</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase leading-tight mb-6 group-hover:text-red-800 transition-colors">{{ $school->name }}</h3>
                </div>
                <div class="mt-auto bg-slate-50 p-4 text-center border-t border-slate-100 group-hover:bg-red-50 transition-colors">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] group-hover:text-red-700">Open Official Census Data</span>
                </div>
            </a>
        @endforeach
    </div>
</div>

{{-- Shared Map Modal --}}
@include('admin.partials.map_modal')

@endsection