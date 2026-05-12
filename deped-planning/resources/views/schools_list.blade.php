@extends('layouts.public')

@section('content')
@php
    /**
     * DETECT EMBED STATUS
     */
    $isEmbed = session('is_embedded', false) || request()->query('embed') === 'true';
@endphp

{{-- EMBED NAVIGATION SWITCHER --}}
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

<div class="max-w-5xl mx-auto px-4 mb-20 {{ $isEmbed ? 'mt-4' : 'mt-10' }}">
    
    {{-- DYNAMIC HEADER --}}
    @if(!$isEmbed)
    <div class="text-center mb-10">
        <h2 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">Official School Directory</h2>
        <p class="text-slate-500 font-mono text-xs uppercase tracking-[0.3em] mt-2">Division of Zamboanga City</p>
    </div>
    @endif

    {{-- SEARCH & FILTER FORM --}}
    <form action="{{ route('public.schools') }}" method="GET" class="mb-8 bg-slate-100 p-3 rounded-2xl flex flex-col gap-3">
        @if($isEmbed)
            <input type="hidden" name="embed" value="true">
        @endif

        {{-- Text Search --}}
        <div class="relative w-full">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search school name or ID..." 
                   class="block w-full pl-11 pr-4 py-4 bg-white border-none rounded-xl text-sm font-bold uppercase tracking-wider focus:ring-2 focus:ring-red-800 transition-all shadow-sm">
        </div>

        {{-- Advanced Filters Row --}}
        <div class="flex flex-col md:flex-row gap-3">

            <select name="level" class="flex-1 bg-white border-none rounded-xl py-3.5 px-4 text-xs font-bold uppercase tracking-widest text-slate-600 focus:ring-2 focus:ring-red-800 shadow-sm outline-none cursor-pointer">
                <option value="">All Levels</option>
                <option value="Primary" {{ request('level') == 'Primary' ? 'selected' : '' }}>Primary (Elem)</option>
                <option value="Secondary" {{ request('level') == 'Secondary' ? 'selected' : '' }}>Secondary (HS)</option>
            </select>

            <select name="district" class="flex-1 bg-white border-none rounded-xl py-3.5 px-4 text-xs font-bold uppercase tracking-widest text-slate-600 focus:ring-2 focus:ring-red-800 shadow-sm outline-none cursor-pointer">
                <option value="">All Districts</option>
                @php 
                    $uniqueDistricts = \App\Models\School::select('district')->distinct()->whereNotNull('district')->pluck('district')->sort(); 
                @endphp
                @foreach($uniqueDistricts as $dist)
                    <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 shrink-0">
                <button type="submit" class="bg-slate-800 hover:bg-red-800 text-white px-8 py-3.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                    <svg class="hidden md:block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">Filter</span>
                </button>

                @if(request()->filled('search') || request()->filled('level') || request()->filled('district'))
                    <a href="{{ route('public.schools', $isEmbed ? ['embed' => 'true'] : []) }}" class="bg-slate-200 hover:bg-slate-300 text-slate-600 px-5 py-3.5 rounded-xl flex items-center justify-center transition-all shadow-sm" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- SCHOOLS LIST --}}
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
                            
                            {{-- NEW: CLASSIFICATION BADGES --}}
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                
                                
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 text-[8px] font-black uppercase tracking-widest rounded">
                                    {{ $school->school_level ?? 'Unclassified' }}
                                </span>
                                
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 text-[8px] font-black uppercase tracking-widest rounded">
                                    <i class="bi bi-geo-alt-fill"></i> {{ $school->district ?? 'No District' }}
                                </span>
                            </div>

                            <div class="md:hidden flex items-center gap-2 text-slate-400 mt-2">
                                <span class="text-[9px] font-black text-slate-400">ID: {{ $school->school_id }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 mt-2 md:mt-0">
                        <a href="{{ route('public.view', ['id' => $school->id]) }}{{ $isEmbed ? '?embed=true' : '' }}" 
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

    {{-- PAGINATION --}}
    <div class="mt-8">
        {{ $schools->appends(request()->query())->links() }}
    </div>
</div>
@endsection