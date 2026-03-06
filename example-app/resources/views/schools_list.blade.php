@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">Official School Directory</h2>
        <p class="text-slate-500 font-mono text-xs uppercase tracking-[0.3em] mt-2">Division of Zamboanga City</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($schools as $school)
            <div class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
                            ID: {{ $school->school_id }}
                        </span>
                    </div>
                    
                    <h3 class="text-2xl font-black text-slate-800 uppercase leading-tight mb-4 group-hover:text-red-800 transition-colors">
                        {{ $school->name }}
                    </h3>
                    
                    <div class="flex items-center gap-2 text-slate-400 mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-bold uppercase tracking-wider">Zamboanga City</span>
                    </div>
                </div>

                <div class="mt-auto p-2">
                    <a href="{{ route('public.view', ['id' => $school->id]) }}" class="...">
    View Census Data
</a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="inline-block p-6 bg-slate-100 rounded-full mb-4">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 4h1m4 0h1m-5 4h1m4 0h1"/></svg>
                </div>
                <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">No schools found in the registry.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection