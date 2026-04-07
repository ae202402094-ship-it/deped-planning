@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    {{-- Branding & Header --}}
    <div class="flex flex-col gap-6 mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Audit Protocol Logs</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest italic">System Activity & Modification Registry</p>
        </div>

        {{-- Filter Bar --}}
        <form action="{{ route('admin.history') }}" method="GET" class="search-form bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-[9px] font-black uppercase text-slate-400 mb-1 block">Search Logs</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#a52a2a]"
                       placeholder="Action, School, or Admin...">
            </div>
            <div>
                <label class="text-[9px] font-black uppercase text-slate-400 mb-1 block">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="text-[9px] font-black uppercase text-slate-400 mb-1 block">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-[#a52a2a] transition-all">
                Filter Registry
            </button>
        </form>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="p-5 border-r border-slate-100">Timestamp</th>
                    <th class="p-5 border-r border-slate-100">Administrator</th>
                    <th class="p-5 border-r border-slate-100">Action Type</th>
                    <th class="p-5 border-r border-slate-100">Target Entity</th>
                    <th class="p-5">Trace Data</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($logs as $log)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/20 transition-colors">
                        <td class="p-5 border-r border-slate-50 font-mono text-[10px] text-slate-500">
                            <span class="font-bold text-slate-700">{{ $log->created_at->format('M d, Y') }}</span><br>
                            {{ $log->created_at->format('H:i:s') }}
                        </td>
                        <td class="p-5 border-r border-slate-50 font-bold text-slate-700">
                            {{ $log->user->name }}
                        </td>
                        <td class="p-5 border-r border-slate-50">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest 
                                {{ str_contains($log->action, 'Delete') ? 'bg-red-100 text-red-700' : 
                                   (str_contains($log->action, 'Created') ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="p-5 border-r border-slate-50 font-black text-slate-800 uppercase tracking-tight text-xs">
                            {{ $log->target_name }}
                        </td>
                        <td class="p-5" x-data="{ expanded: false }">
                            @if(isset($log->changes['before']) && isset($log->changes['after']))
                                @php
                                    $diff = collect($log->changes['after'])->filter(fn($val, $key) => isset($log->changes['before'][$key]) && $log->changes['before'][$key] !== $val);
                                @endphp

                                @if($diff->isNotEmpty())
                                    <button @click="expanded = !expanded" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-[#a52a2a] outline-none">
                                        <i class="bi" :class="expanded ? 'bi-dash-circle' : 'bi-plus-circle'"></i>
                                        <span x-text="expanded ? 'Close Trace' : 'Inspect {{ $diff->count() }} Updates'"></span>
                                    </button>

                                    <div x-show="expanded" x-cloak class="mt-4 space-y-3">
                                        @foreach($diff as $key => $value)
                                            <div class="flex flex-col border-l-2 border-[#a52a2a] pl-3 py-1 bg-slate-50 rounded-r-lg">
                                                <span class="text-[8px] font-black uppercase text-slate-400">{{ str_replace('_', ' ', $key) }}</span>
                                                <div class="flex items-center gap-2 text-[10px]">
                                                    <span class="text-red-400 line-through bg-red-50 px-1 rounded">{{ $log->changes['before'][$key] ?? 'NULL' }}</span>
                                                    <i class="bi bi-arrow-right text-slate-300"></i>
                                                    <span class="text-emerald-600 font-bold bg-emerald-50 px-1 rounded">{{ $value }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] font-bold text-slate-300 italic tracking-widest uppercase">Verified No Change</span>
                                @endif
                            @elseif(isset($log->changes['after']))
                                <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-[0.2em]">Initial Creation Registry</span>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 italic">No Trace Available</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-20 text-center font-black text-slate-300 uppercase tracking-[0.5em] text-xs">No logs recorded</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <div class="p-6 bg-slate-50 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection