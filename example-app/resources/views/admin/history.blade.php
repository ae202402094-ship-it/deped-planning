@extends('layouts.admin')

@section('content')

{{-- 00. COMPACT PRINT HEADER (Institutional Ledger Style) --}}
<div class="hidden print:block border-b-2 border-black pb-2 mb-4">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-xl font-black uppercase tracking-tighter">Registry Audit Ledger</h1>
            <p class="text-[8px] font-bold uppercase tracking-widest text-slate-500">DepEd Division of Zamboanga City</p>
        </div>
        <div class="text-right text-[8px] font-mono uppercase">
            <p>ID: LOG-{{ strtoupper(Str::random(8)) }}</p>
            <p>DATE: {{ now()->format('m/d/Y H:i') }}</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4">
    {{-- 01. INTERFACE HEADER & MULTI-FILTER BAR --}}
    <div class="mb-8 no-print">
        <div class="flex flex-col xl:flex-row justify-between items-end gap-6">
            <div>
                <span class="text-[10px] font-black text-red-800 uppercase tracking-[0.4em] mb-1 block">Audit System</span>
                <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tighter italic leading-none">Activity History</h1>
            </div>

            <form action="{{ route('admin.history') }}" method="GET" class="w-full xl:w-auto no-print">
    <div class="flex flex-wrap items-center gap-4 bg-white p-2 pl-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
        
        {{-- Omni-Search Input --}}
        <div class="flex items-center gap-3 flex-grow min-w-[240px] relative group">
            <svg class="w-4 h-4 text-slate-300 group-focus-within:text-red-800 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search Admin, School, or Action..." 
                   class="w-full bg-transparent border-none py-2 text-xs font-bold text-slate-700 outline-none placeholder:text-slate-300 placeholder:font-medium">
            
            @if(request('search'))
                <a href="{{ route('admin.history') }}" class="pr-2 text-slate-300 hover:text-red-800 transition-colors" title="Clear Search">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>

        {{-- Divider --}}
        <div class="h-8 w-px bg-slate-100 hidden lg:block"></div>

        {{-- Date Range Group --}}
        <div class="flex items-center gap-2">
            {{-- From --}}
            <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl border border-transparent focus-within:border-slate-200 transition-all">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">From</span>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                       class="bg-transparent border-none text-[10px] font-black text-slate-600 outline-none cursor-pointer">
            </div>

            <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5-5 5M6 7l5 5-5 5"/>
            </svg>

            {{-- To --}}
            <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl border border-transparent focus-within:border-slate-200 transition-all">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">To</span>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                       class="bg-transparent border-none text-[10px] font-black text-slate-600 outline-none cursor-pointer">
            </div>
        </div>


        {{-- Global Reset (Only shows if any filter is active) --}}
        @if(request()->anyFilled(['from_date', 'to_date']))
            <a href="{{ route('admin.history') }}" class="p-2 mr-2 text-slate-300 hover:text-red-800 transition-all" title="Reset All Filters">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        @endif
        
        {{-- Action Button --}}
        <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-black uppercase text-[10px] tracking-[0.15em] hover:bg-red-800 transition-all shadow-lg shadow-slate-900/10 active:scale-95">
            Filter
        </button>
    </div>
</form>

            <button onclick="window.print()" class="bg-white border border-slate-300 text-slate-600 px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:border-slate-800 transition-all flex items-center gap-2 shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Export Ledger
            </button>
        </div>
    </div>

    {{-- 02. ACTIVITY LEDGER TABLE --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden print:border-none print:shadow-none print:bg-transparent">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200 print:bg-transparent print:border-b-2 print:border-black">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest print:text-black print:text-[8px]">
                    <th class="p-5 print:p-2 print:w-24">Date/Time</th>
                    <th class="p-5 print:p-2">Admin</th>
                    <th class="p-5 print:p-2">Institution</th>
                    <th class="p-5 print:p-2 text-right">Modifications</th>
                </tr>
            </thead>
            <tbody class="text-xs font-bold divide-y divide-slate-100 print:divide-slate-300">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/50 transition-colors page-break-avoid">
                    <td class="p-5 print:p-2 text-slate-400 font-mono text-[11px] print:text-[9px] print:text-black">
                        {{ $log->created_at->format('m/d/y') }}
                        <span class="block text-[9px] opacity-60 print:inline print:ml-1">{{ $log->created_at->format('H:i') }}</span>
                    </td>
                    <td class="p-5 print:p-2 uppercase text-slate-800 print:text-[9px]">{{ $log->user->name }}</td>
                    <td class="p-5 print:p-2 uppercase text-slate-800 tracking-tighter print:text-[9px]">{{ $log->target_name }}</td>
                    <td class="p-5 print:p-2 text-right">
                        {{-- SCREEN VIEW DROPDOWN --}}
                        <details class="group cursor-pointer no-print">
                            <summary class="list-none text-[9px] font-black text-red-800 uppercase tracking-widest hover:text-black transition-colors">View Shift [+]</summary>
                            <div class="mt-2 p-3 bg-slate-50 rounded-xl text-left border border-slate-200">
                                @include('admin.partials.history_print', ['changes' => $log->changes])
                            </div>
                        </details>

                        {{-- PRINT VIEW (Concise Delta Line) --}}
                        <div class="hidden print:block text-[8px] text-right font-mono">
                            @php $first = true; @endphp
                            @foreach($log->changes['before'] as $key => $oldValue)
                                @php $newValue = $log->changes['after'][$key] ?? $oldValue; @endphp
                                @if((string)$oldValue !== (string)$newValue)
                                    @if(!$first) | @endif
                                    <span class="inline-block uppercase">
                                        {{ str_replace('_', ' ', $key) }}: {{ $oldValue }} → {{ $newValue }}
                                    </span>
                                    @php $first = false; @endphp
                                @endif
                            @endforeach
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-20 text-center text-slate-300 uppercase font-black tracking-[0.5em] text-xs">
                        No activity found for selected criteria
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        @if($logs->hasPages())
            <div class="p-6 bg-slate-50 border-t border-slate-200 no-print">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection