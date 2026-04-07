@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- 00. PROFESSIONAL PRINT HEADER (Visible on Paper Only) --}}
    <div class="hidden print:block mb-10 border-b-2 border-slate-900 pb-8">
        <div class="flex justify-between items-start mb-6">
            <div class="flex items-center gap-6">
                <img src="{{ asset('images/deped.png') }}" class="h-20 w-auto">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-slate-500 leading-tight">Republic of the Philippines</span>
                    <h1 class="text-2xl font-black uppercase text-red-900 leading-tight">Department of Education</h1>
                    <p class="text-[11px] font-bold italic text-slate-500 uppercase tracking-widest">Division of Zamboanga City | Planning & Research Section</p>
                </div>
            </div>
            <div class="text-right flex flex-col items-end">
                <span class="bg-slate-900 text-white px-4 py-1.5 text-[9px] font-black uppercase tracking-[0.2em] mb-3">Institutional Record</span>
                <p class="text-[10px] font-mono text-slate-400 uppercase leading-none">Serial: REG-{{ now()->format('Ymd') }}-{{ strtoupper(Str::random(4)) }}</p>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-3xl font-black uppercase tracking-tighter border-l-8 border-red-900 pl-5">Institutional Registry Summary</h2>
            <p class="text-[11px] text-slate-500 font-bold mt-2 uppercase tracking-[0.25em]">Comprehensive inventory of verified learning centers and resource metrics</p>
        </div>

        {{-- Executive Summary Block for Print --}}
        <div class="mt-10 grid grid-cols-4 gap-6 border-y-2 border-slate-100 py-8">
            <div class="text-center border-r border-slate-100">
                <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2">Total Institutions</p>
                <p class="text-2xl font-black text-slate-900">{{ $schools->total() }}</p>
            </div>
            <div class="text-center border-r border-slate-100">
                <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2">Authorized By</p>
                <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
            </div>
            <div class="text-center">
                <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2">Filing Date</p>
                <p class="text-sm font-bold text-slate-900">{{ now()->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- 01. INTERACTIVE PAGE HEADER (Hidden on Print) --}}
    <div class="no-print flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tight">School Registry</h2>
            <p class="text-sm text-slate-500 font-bold uppercase tracking-widest italic">Manage Institutional Data & Verification</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="bg-white border-2 border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-printer-fill"></i> Generate Report
            </button>
            <a href="{{ route('schools.create') }}" class="bg-[#a52a2a] text-white px-8 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-900/20 hover:bg-black transition-all">
                Add New School
            </a>
        </div>
    </div>

    {{-- 02. REGISTRY TABLE --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-2xl shadow-slate-200/50 overflow-hidden print:border-slate-900 print:shadow-none print:rounded-none">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200 print:bg-slate-100">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest print:text-slate-900">
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300">ID CODE</th>
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300">Institutional Entity</th>
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300 text-center">Faculty</th>
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300 text-center">Enrollees</th>
                    <th class="p-6 text-center no-print">Operations</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($schools as $school)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors print:border-slate-200">
                        <td class="p-6 border-r border-slate-50 print:border-slate-200 font-mono font-bold text-slate-500 print:text-black">
                            #{{ $school->school_id }}
                        </td>
                        <td class="p-6 border-r border-slate-50 print:border-slate-200">
                            <span class="font-black text-slate-800 uppercase tracking-tight block">{{ $school->name }}</span>
                        </td>
                        <td class="p-6 border-r border-slate-50 print:border-slate-200 text-center font-bold tabular-nums">
                            {{ number_format($school->no_of_teachers) }}
                        </td>
                        <td class="p-6 border-r border-slate-50 print:border-slate-200 text-center font-bold tabular-nums">
                            {{ number_format($school->no_of_enrollees) }}
                        </td>
                        <td class="p-6 text-center no-print">
                            <div class="flex justify-center items-center gap-4">
                                <a href="{{ route('schools.edit', $school->id) }}" 
                                   class="inline-flex items-center gap-1.5 text-[#a52a2a] hover:text-black font-black text-[10px] uppercase tracking-tighter transition-colors">
                                    <i class="bi bi-pencil-square"></i>
                                    Edit Profile
                                </a>
                                <form action="{{ route('schools.destroy', $school->id) }}" method="POST" onsubmit="return confirm('Archive this institution?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-red-600 font-black text-[10px] uppercase tracking-tighter transition-colors">
                                        Archive
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-20 text-center uppercase font-black text-slate-200 tracking-[1em]">No Registry Data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- 03. PAGINATION (Hidden on Print) --}}
        @if($schools->hasPages())
            <div class="p-8 bg-slate-50 border-t border-slate-200 no-print">
                {{ $schools->links() }}
            </div>
        @endif
    </div>

    {{-- 04. VALIDATION FOOTER (Visible on Paper Only) --}}
    <div class="hidden print:block mt-24">
        <div class="flex justify-between items-end px-12">
            <div class="text-center w-72">
                <div class="border-b-2 border-slate-900 mb-3"></div>
                <p class="text-[10px] font-black uppercase tracking-widest">Certified Correct By:</p>
                <p class="text-[9px] text-slate-500 font-bold uppercase mt-1">Division Planning Officer</p>
            </div>
            <div class="text-center w-72">
                <div class="border-b-2 border-slate-900 mb-3"></div>
                <p class="text-[10px] font-black uppercase tracking-widest">Approved For Release:</p>
                <p class="text-[9px] text-slate-500 font-bold uppercase mt-1">Schools Division Superintendent</p>
            </div>
        </div>
        
        <div class="mt-24 flex flex-col items-center gap-2">
            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.5em]">End of Institutional Registry Summary</p>
            <p class="text-[8px] text-slate-400 italic">System-generated document. Electronic verification code: {{ strtoupper(Str::random(12)) }}</p>
        </div>
    </div>

</div>
@endsection