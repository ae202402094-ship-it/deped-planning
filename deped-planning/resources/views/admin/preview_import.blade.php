@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-sans leading-tight">
    {{-- Header Ribbon: Black Borders with Auburn Accent --}}
    <div class="flex justify-between items-stretch bg-white border-2 border-black mb-8 shadow-[4px_4px_0px_0px_rgba(165,42,42,1)]">
        <div class="flex items-center">
            <div class="bg-[#a52a2a] text-white px-6 py-4 text-xs font-black uppercase tracking-tighter border-r-2 border-black">
                Registry Synchronization Preview
            </div>
            <h1 class="px-6 text-sm font-black text-black uppercase tracking-tight">Technical Audit Batch: {{ count($formattedData) }} Entities</h1>
        </div>
        <div class="flex border-l-2 border-black">
            <a href="{{ route('admin.schools') }}" class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 hover:text-black transition-colors bg-slate-50">
                Abort Protocol
            </a>
        </div>
    </div>

    {{-- Comprehensive Status Ledger: Neutral Borders --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="border-2 border-black p-4 bg-white shadow-sm">
            <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-1">New Registrations</p>
            <p class="text-2xl font-black text-emerald-600">{{ $newCount }}</p>
        </div>
        <div class="border-2 border-black p-4 bg-white shadow-sm">
            <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-1">Record Updates</p>
            <p class="text-2xl font-black text-[#a52a2a]">{{ $updateCount }}</p>
        </div>
        <div class="border-2 border-black p-4 bg-white shadow-sm">
            <p class="text-[7px] font-black text-orange-400 uppercase tracking-widest mb-1">Nomenclature Mismatch</p>
            <p class="text-2xl font-black text-orange-500">{{ $nameMismatchCount }}</p>
        </div>
        <div class="border-2 border-black p-4 bg-white shadow-sm">
            <p class="text-[7px] font-black text-red-400 uppercase tracking-widest mb-1">Primary Key Conflicts</p>
            <p class="text-2xl font-black text-red-600">{{ $conflictCount }}</p>
        </div>
        <div class="border-2 border-black p-4 bg-slate-50 shadow-sm">
            <p class="text-[7px] font-black text-slate-900 uppercase tracking-widest mb-1">Batch Total</p>
            <p class="text-2xl font-black text-slate-900">{{ count($formattedData) }}</p>
        </div>
    </div>

    {{-- High-Density Technical Table: Black Borders --}}
    <div class="overflow-hidden border-2 border-black bg-white shadow-2xl overflow-x-auto">
        <table class="w-full text-left border-collapse table-auto">
            <thead>
                <tr class="bg-black text-white">
                    <th class="p-3 text-[8px] font-black uppercase tracking-widest border-r border-white/20">Protocol</th>
                    <th class="p-3 text-[8px] font-black uppercase tracking-widest border-r border-white/20">Identification</th>
                    <th class="p-3 text-[8px] font-black uppercase tracking-widest border-r border-white/20">Inventory Matrix</th>
                    <th class="p-3 text-[8px] font-black uppercase tracking-widest border-r border-white/20">Utility Audit</th>
                    <th class="p-3 text-[8px] font-black uppercase tracking-widest border-r border-white/20">Deficit Audit</th>
                    <th class="p-3 text-[8px] font-black uppercase tracking-widest">Technical Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/10">
                @foreach($formattedData as $row)
                <tr class="hover:bg-slate-50 transition-colors {{ $row['status'] === 'conflict' ? 'bg-red-50' : '' }}">
                    {{-- Protocol Status --}}
                    <td class="p-3 border-r border-black/10 text-center align-top">
                        <div class="flex flex-col gap-1">
                            @if($row['status'] == 'new')
                                <span class="text-[7px] bg-emerald-600 text-white px-2 py-0.5 font-black uppercase rounded-sm">New Data</span>
                            @elseif($row['status'] == 'update')
                                <span class="text-[7px] bg-blue-600 text-white px-2 py-0.5 font-black uppercase rounded-sm">Overwrite</span>
                            @else
                                <span class="text-[7px] bg-red-600 text-white px-2 py-0.5 font-black uppercase rounded-sm">Conflict</span>
                            @endif
                        </div>
                    </td>

                    {{-- Identification --}}
                    <td class="p-3 border-r border-black/10 align-top">
                        <p class="text-[10px] font-black text-slate-800 uppercase leading-none mb-1">{{ $row['name'] }}</p>
                        <p class="text-[8px] font-mono font-bold text-slate-400 tracking-tighter italic">ID: {{ $row['school_id'] }}</p>
                    </td>
                    
                    {{-- Inventory Matrix --}}
                    <td class="p-3 border-r border-black/10 align-top">
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 font-mono text-[9px]">
                            <div class="flex justify-between border-b border-slate-100"><span class="text-slate-400 uppercase">TEACHERS</span><span class="font-black">{{ $row['no_of_teachers'] }}</span></div>
                            <div class="flex justify-between border-b border-slate-100"><span class="text-slate-400 uppercase">ENROLLEES</span><span class="font-black">{{ $row['no_of_enrollees'] }}</span></div>
                            <div class="flex justify-between border-b border-slate-100"><span class="text-slate-400 uppercase">CLASSROOMS</span><span class="font-black">{{ $row['no_of_classrooms'] }}</span></div>
                            <div class="flex justify-between border-b border-slate-100"><span class="text-slate-400 uppercase">CHAIRS</span><span class="font-black text-[#a52a2a]">{{ $row['no_of_chairs'] }}</span></div>
                        </div>
                    </td>

                    {{-- Utility Audit --}}
                    <td class="p-3 border-r border-black/10 align-top">
                        <div class="space-y-1">
                            @foreach([['key' => 'with_electricity', 'label' => 'Electricity'], ['key' => 'with_potable_water', 'label' => 'Water'], ['key' => 'with_internet', 'label' => 'Connectivity']] as $util)
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $row[$util['key']] ? 'bg-emerald-500' : 'bg-red-400' }}"></div>
                                <span class="text-[8px] font-black uppercase {{ $row[$util['key']] ? 'text-slate-700' : 'text-slate-300' }}">{{ $util['label'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </td>

                    {{-- Deficit Audit --}}
                    <td class="p-3 border-r border-black/10 align-top font-mono text-[9px]">
                        <div class="space-y-1 text-[#a52a2a] font-bold">
                            <p>CLASSROOM_SHORTAGE: {{ $row['classroom_shortage'] }}</p>
                            <p>CHAIR_SHORTAGE: {{ $row['chair_shortage'] }}</p>
                            <p>TOILET_SHORTAGE: {{ $row['toilet_shortage'] }}</p>
                        </div>
                    </td>

                    {{-- Technical Remarks --}}
                    <td class="p-3 align-top">
                        <p class="text-[8px] font-bold text-slate-500 uppercase leading-relaxed tracking-tighter italic">
                            {{ $row['hazards'] ?: 'No physical hazards documented.' }}
                        </p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Execution Protocol --}}
    <div class="mt-10 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 bg-black animate-pulse rounded-full"></div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Administrative Data Entry Protocol: Validated</p>
        </div>
        
        <form action="{{ route('schools.confirm_import') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="bg-[#a52a2a] text-white px-16 py-6 text-xs font-black uppercase tracking-[0.4em] hover:bg-black transition-all shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none">
                Execute Batch Synchronization
            </button>
        </form>
    </div>
</div>
@endsection