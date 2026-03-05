@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">School Management</h2>
        
        <form action="{{ route('admin.schools') }}" method="GET" class="w-full md:w-96 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or ID..." 
                   class="w-full border border-slate-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none shadow-sm">
            <button type="submit" style="background-color: #a52a2a;" class="text-white px-6 py-2 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-red-900 transition shadow-md">
                Search
            </button>
        </form>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl p-8 mb-10 border border-slate-200">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-2 h-6 bg-red-800 rounded-full"></div>
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em]">Register New School Profile</h3>
        </div>
        
        <form action="{{ route('schools.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @csrf
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">School ID</label>
                <input type="text" name="school_id" placeholder="e.g. 123456" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm focus:bg-white transition-all outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">School Name</label>
                <input type="text" name="name" placeholder="Official School Name" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm focus:bg-white transition-all outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Teachers</label>
                <input type="number" name="no_of_teachers" value="0" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm focus:bg-white transition-all outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Enrollees</label>
                <input type="number" name="no_of_enrollees" value="0" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm focus:bg-white transition-all outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Classrooms</label>
                <input type="number" name="no_of_classrooms" value="0" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm focus:bg-white transition-all outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Toilets</label>
                <input type="number" name="no_of_toilets" value="0" required class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-sm focus:bg-white transition-all outline-none">
            </div>
            
            <button type="submit" style="background-color: #a52a2a;" class="text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-red-900 transition lg:col-span-full py-4 mt-2 shadow-lg shadow-red-200">
                Add School to Registry
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($schools as $school)
            <a href="{{ route('schools.edit', $school->id) }}" class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">ID: {{ $school->school_id }}</span>
                        <span class="text-red-700 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-black uppercase tracking-widest">Edit Profile →</span>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 uppercase leading-tight mb-6 group-hover:text-red-800 transition-colors">{{ $school->name }}</h3>
                    
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-50 pt-6">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Teachers</span>
                            <span class="text-lg font-black text-slate-700">{{ $school->no_of_teachers }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Enrollees</span>
                            <span class="text-lg font-black text-slate-700">{{ $school->no_of_enrollees }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-auto bg-slate-50 p-4 text-center border-t border-slate-100 group-hover:bg-red-50 transition-colors">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] group-hover:text-red-700">Open Official Census Data</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection