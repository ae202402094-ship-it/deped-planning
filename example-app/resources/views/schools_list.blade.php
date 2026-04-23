@extends('layouts.public')

@section('content')
@php
    /**
     * DETECT EMBED STATUS
     * We use the session persistence and the URL parameter.
     */
    $isEmbed = session('is_embedded', false) || request()->query('embed') === 'true';
@endphp

{{-- 
    1. EMBED NAVIGATION SWITCHER
    This replaces the main header when embedded.
--}}
@if($isEmbed)
<div class="bg-white border-b border-slate-100 px-4 py-3 mb-6 no-print">
    <div class="max-w-7xl mx-auto flex items-center justify-center gap-6">
        <a href="{{ route('public.map') }}" 
           class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.map') ? 'text-[#a52a2a]' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="bi bi-map-fill"></i> Interactive Map
        </a>
        
        <div class="h-4 w-px bg-slate-200"></div>

        <a href="{{ route('public.schools') }}" 
           class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.schools') ? 'text-[#a52a2a]' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="bi bi-list-ul"></i> School Directories
        </a>
    </div>
</div>
@endif

<div class="max-w-5xl mx-auto px-4 mb-20 {{ $isEmbed ? 'mt-4' : '' }}">
    
    {{-- 2. DYNAMIC HEADER --}}
    @if(!$isEmbed)
    <div class="text-center mb-10">
        <h2 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">Official School Directory</h2>
        <p class="text-slate-500 font-mono text-xs uppercase tracking-[0.3em] mt-2">Division of Zamboanga City</p>
    </div>
    @endif

    {{-- 3. SEARCH FORM --}}
    {{-- Notice: We add a hidden input for 'embed' so searching doesn't reveal the header --}}
    <form action="{{ route('public.schools') }}" method="GET" class="mb-8 bg-slate-100 p-2 rounded-2xl flex flex-col md:flex-row gap-2">
        @if($isEmbed)
            <input type="hidden" name="embed" value="true">
        @endif

        <div class="relative flex-grow">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Search school name or ID..." 
                   class="block w-full pl-11 pr-4 py-4 bg-white border-none rounded-xl text-sm font-bold uppercase tracking-wider focus:ring-2 focus:ring-red-800 transition-all shadow-sm"
            >
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-slate-800 hover:bg-red-800 text-white px-6 py-4 rounded-xl transition-all shadow-sm">
                <span class="md:hidden text-[10px] font-black uppercase tracking-widest">Search</span>
                <svg class="hidden md:block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>

            @if(request()->filled('search'))
                <a href="{{ route('public.schools', $isEmbed ? ['embed' => 'true'] : []) }}" 
                   class="bg-slate-200 hover:bg-slate-300 text-slate-600 px-4 py-4 rounded-xl flex items-center justify-center transition-all shadow-sm" title="Clear Filters">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            @endif
        </div>
    </form>

    {{-- 4. SCHOOLS LIST --}}
    <div class="flex flex-col gap-3">
        @forelse($schools as $school)
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-slate-200 overflow-hidden">
                <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    
                    <div class="flex items-center gap-5">
                        <div class="hidden md:block shrink-0">
                            <span class="bg-slate-50 text-slate-400 text-[9px] font-black px-3 py-4 rounded-xl uppercase tracking-widest border border-slate-100 block">
                                {{ $school->school_id }}
                            </span>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-black text-slate-800 uppercase leading-tight group-hover:text-red-800 transition-colors">
                                {{ $school->name }}
                            </h3>
                            <div class="flex items-center gap-2 text-slate-400 mt-0.5">
                                <i class="bi bi-geo-alt"></i>
                                <span class="text-[9px] font-bold uppercase tracking-wider">Division of Zamboanga City</span>
                                <span class="md:hidden text-[9px] font-black text-slate-400 ml-2">ID: {{ $school->school_id }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <a href="{{ route('public.view', ['id' => $school->id]) }}" 
                           class="inline-flex items-center justify-center gap-2 w-full md:w-auto bg-slate-50 hover:bg-red-800 group/btn text-slate-600 hover:text-white text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-xl transition-all border border-slate-200 hover:border-red-800">
                            View Details
                            <svg class="w-3 h-3 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">No matching schools found.</p>
            </div>
        @endforelse
    </div>

    {{-- 5. PAGINATION --}}
    <div class="mt-8">
        {{ $schools->appends(request()->query())->links() }}
    </div>
</div>
@endsection