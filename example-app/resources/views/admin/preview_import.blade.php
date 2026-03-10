@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter">Review Registry Sync</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Institutional Verification | Pre-Commit Protocol</p>
        </div>
        <a href="{{ route('admin.schools') }}" class="text-[10px] font-black text-slate-400 hover:text-red-800 uppercase tracking-widest">
            ← Cancel Import
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-3xl">
            <p class="text-[10px] font-black text-emerald-600 uppercase mb-1">New</p>
            <p class="text-3xl font-black text-emerald-800">{{ $newCount }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-100 p-6 rounded-3xl">
            <p class="text-[10px] font-black text-blue-600 uppercase mb-1">Updates</p>
            <p class="text-3xl font-black text-blue-800">{{ $updateCount }}</p>
        </div>
        <div class="{{ $conflictCount > 0 ? 'bg-red-100 border-red-200' : 'bg-slate-50 border-slate-200' }} p-6 rounded-3xl">
            <p class="text-[10px] font-black {{ $conflictCount > 0 ? 'text-red-600' : 'text-slate-400' }} uppercase mb-1">Conflicts</p>
            <p class="text-3xl font-black {{ $conflictCount > 0 ? 'text-red-800' : 'text-slate-800' }}">{{ $conflictCount }}</p>
        </div>
        <div class="bg-slate-900 p-6 rounded-3xl">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Total</p>
            <p class="text-3xl font-black text-white">{{ count($formattedData) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden mb-8">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="p-4 border-r border-slate-200">Status</th>
                    <th class="p-4 border-r border-slate-200">School ID</th>
                    <th class="p-4 border-r border-slate-200">Name</th>
                    <th class="p-4 border-r border-slate-200 text-center">Tchrs</th>
                    <th class="p-4 border-r border-slate-200 text-center">Enrll</th>
                    <th class="p-4 border-r border-slate-200 text-center">Rooms</th>
                    <th class="p-4 border-r border-slate-200 text-center">Toilets</th>
                    <th class="p-4 text-center">Note</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium">
                @foreach($formattedData as $row)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 {{ $row['status'] === 'conflict' ? 'bg-red-50' : '' }}">
                        <td class="p-4 border-r border-slate-100 text-center">
                            @if($row['status'] === 'conflict')
                                <span class="px-2 py-1 bg-red-600 text-white text-[8px] font-black uppercase rounded-full animate-pulse">CONFLICT</span>
                            @elseif($row['status'] === 'update')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[8px] font-black uppercase rounded-full">UPDATE</span>
                            @else
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[8px] font-black uppercase rounded-full">NEW</span>
                            @endif
                        </td>
                        <td class="p-4 border-r border-slate-100 font-mono text-xs text-slate-500">{{ $row['school_id'] }}</td>
                        <td class="p-4 border-r border-slate-100 font-bold uppercase text-slate-700">{{ $row['name'] }}</td>
                        
                        <x-sync-cell :value="$row['no_of_teachers']" field="no_of_teachers" :row="$row" />
                        <x-sync-cell :value="$row['no_of_enrollees']" field="no_of_enrollees" :row="$row" />
                        <x-sync-cell :value="$row['no_of_classrooms']" field="no_of_classrooms" :row="$row" />
                        <x-sync-cell :value="$row['no_of_toilets']" field="no_of_toilets" :row="$row" />

                        <td class="p-4 text-center text-[9px] font-bold uppercase italic">
                            {{ $row['exists_in_db'] ? 'Matches Registry' : 'Fresh Entry' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Final Authorize Button --}}
    @php $hasConflicts = $conflictCount > 0; @endphp
    <form action="{{ route('schools.confirm_import') }}" method="POST">
        @csrf
        <button type="submit" 
            @if($hasConflicts) disabled @endif
            class="w-full py-4 rounded-2xl font-black uppercase text-xs tracking-[0.3em] transition-all 
            {{ $hasConflicts ? 'bg-slate-300 cursor-not-allowed' : 'bg-red-800 hover:bg-black text-white shadow-xl' }}">
            {{ $hasConflicts ? 'Blocked: Duplicate IDs Found' : 'Authorize Registry Synchronization' }}
        </button>
    </form>
</div>
@endsection