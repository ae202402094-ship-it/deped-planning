@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    {{-- Header Section --}}
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter">Review Registry Sync</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Institutional Verification | Pre-Commit Protocol</p>
        </div>
        <a href="{{ route('admin.schools') }}" class="text-[10px] font-black text-slate-400 hover:text-red-800 uppercase tracking-widest">
            ← Cancel Import
        </a>
    </div>

    {{-- 📊 Summary Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-3xl flex flex-col justify-center">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">New Schools Found</p>
            <p class="text-3xl font-black text-emerald-800 tabular-nums">{{ $newCount }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-100 p-6 rounded-3xl flex flex-col justify-center">
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Existing Updates</p>
            <p class="text-3xl font-black text-blue-800 tabular-nums">{{ $updateCount }}</p>
        </div>
        <div class="bg-slate-50 border border-slate-200 p-6 rounded-3xl flex flex-col justify-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Queue</p>
            <p class="text-3xl font-black text-slate-800 tabular-nums">{{ count($importData) }}</p>
        </div>
    </div>

    {{-- 📋 Data Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden mb-8">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="p-4 border-r border-slate-200">Protocol</th> {{-- Status Column --}}
                    <th class="p-4 border-r border-slate-200">School ID</th>
                    <th class="p-4 border-r border-slate-200">Name</th>
                    <th class="p-4 border-r border-slate-200 text-center">Tchrs</th>
                    <th class="p-4 border-r border-slate-200 text-center">Enrll</th>
                    <th class="p-4 border-r border-slate-200 text-center">Rooms</th>
                    <th class="p-4 border-r border-slate-200 text-center">Toilets</th>
                    <th class="p-4 text-center">Coord</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium">
                @foreach($importData as $row)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="p-4 border-r border-slate-100">
                            @if($row['status'] === 'new')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[9px] font-black uppercase rounded-full border border-emerald-200">
                                    NEW RECORD
                                </span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[9px] font-black uppercase rounded-full border border-blue-200">
                                    UPDATE DATA
                                </span>
                            @endif
                        </td>
                        <td class="p-4 border-r border-slate-100 font-mono text-xs text-slate-500">{{ $row['school_id'] }}</td>
                        <td class="p-4 border-r border-slate-100 font-bold uppercase text-slate-700">{{ $row['name'] }}</td>
                        <td class="p-4 border-r border-slate-100 text-center tabular-nums">{{ $row['no_of_teachers'] }}</td>
                        <td class="p-4 border-r border-slate-100 text-center tabular-nums">{{ $row['no_of_enrollees'] }}</td>
                        <td class="p-4 border-r border-slate-100 text-center tabular-nums">{{ $row['no_of_classrooms'] }}</td>
                        <td class="p-4 border-r border-slate-100 text-center tabular-nums">{{ $row['no_of_toilets'] }}</td>
                        <td class="p-4 text-center text-[10px] text-slate-400">
                            {{ round($row['latitude'], 4) }}, {{ round($row['longitude'], 4) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Final Authorization Button --}}
    <form action="{{ route('schools.confirm_import') }}" method="POST" id="confirmForm">
        @csrf
        <button type="submit" id="submitBtn" class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-xs tracking-[0.3em] hover:bg-black transition-all shadow-xl flex items-center justify-center gap-3">
            <span id="btnText">Authorize Registry Synchronization ({{ count($importData) }} Records)</span>
        </button>
    </form>
</div>

{{-- Add the spinner script to prevent double-submits --}}
<script>
    document.getElementById('confirmForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const text = document.getElementById('btnText');
        
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        text.innerHTML = 'Synchronizing... Please do not close browser';
    });
</script>
@endsection