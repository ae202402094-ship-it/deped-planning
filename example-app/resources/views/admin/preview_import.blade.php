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

    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden mb-8">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="p-4 border-r border-slate-200">School ID</th>
                    <th class="p-4 border-r border-slate-200">Name</th>
                    <th class="p-4 border-r border-slate-200 text-center">Tchrs</th>
                    <th class="p-4 border-r border-slate-200 text-center">Enrll</th>
                    <th class="p-4 text-center">Coord</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium">
                @foreach($importData as $row)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="p-4 border-r border-slate-100 font-mono">{{ $row['school_id'] }}</td>
                        <td class="p-4 border-r border-slate-100 font-bold uppercase">{{ $row['name'] }}</td>
                        <td class="p-4 border-r border-slate-100 text-center tabular-nums">{{ $row['no_of_teachers'] }}</td>
                        <td class="p-4 border-r border-slate-100 text-center tabular-nums">{{ $row['no_of_enrollees'] }}</td>
                        <td class="p-4 text-center text-[10px] text-slate-400">
                            {{ round($row['latitude'], 2) }}, {{ round($row['longitude'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form action="{{ route('schools.confirm_import') }}" method="POST">
        @csrf
        <button type="submit" class="w-full py-4 bg-red-800 text-white rounded-2xl font-black uppercase text-xs tracking-[0.3em] hover:bg-black transition-all shadow-xl">
            Authorize Registry Synchronization ({{ count($importData) }} Records)
        </button>
    </form>
</div>
@endsection