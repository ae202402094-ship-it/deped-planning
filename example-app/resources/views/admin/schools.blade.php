@extends(auth()->user()->role === 'super_admin' ? 'layouts.super_admin' : 'layouts.admin')

@section('content')

{{-- 00. OFFICIAL PRINT HEADER (Only visible on paper) --}}
<div class="hidden print:block mb-10 border-b-4 border-double border-slate-900 pb-6 text-center">
    <div class="flex flex-col items-center">
        <h1 class="text-2xl font-black uppercase tracking-widest">Republic of the Philippines</h1>
        <h2 class="text-3xl font-black uppercase tracking-tighter text-red-900">Department of Education</h2>
        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-slate-500 mt-1">Division of Zamboanga City | Information Systems Office</p>
    </div>
    <div class="mt-6 flex justify-between items-end text-[9px] font-mono text-slate-400 uppercase">
        <div>
            <p>Document: Institutional Registry Summary</p>
            <p>Total Records: {{ $schools->total() }}</p>
        </div>
        <div class="text-right">
            <p>Generated: {{ now()->format('M d, Y | H:i') }}</p>
            <p>Ref ID: REG-{{ strtoupper(Str::random(8)) }}</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4">
    {{-- 01. NAVIGATION & SEARCH --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">School Registry</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest italic">Institutional Management Interface</p>
        </div>

        <div class="flex flex-wrap gap-4 items-center no-print">
            {{-- Print Trigger --}}
            <button onclick="window.print()" class="bg-slate-100 text-slate-800 px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-200 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Registry
            </button>
            @if(auth()->user()->role === 'admin')
        <a href="{{ route('schools.archive') }}" class="text-[10px] font-black text-slate-400 hover:text-red-800 transition-all uppercase tracking-widest px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            Institutional Archive
        </a>
    @endif

    <a href="{{ route('schools.create') }}" class="bg-red-800 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition shadow-lg">
        + Register School
    </a>
            
            {{-- Search Bar --}}
            <form action="{{ route('admin.schools') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID or Name..." 
                       class="w-64 border border-slate-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none shadow-sm text-sm font-medium">
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl font-bold uppercase text-[10px] tracking-widest">Find</button>
            </form>
        </div>
    </div>

    {{-- 02. BULK REGISTRY SYNC (CSVs) --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-xl shadow-slate-200/50 mb-12 relative overflow-hidden no-print">
        <div class="relative flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="max-w-md">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-1.5 h-6 bg-red-800 rounded-full"></div>
                    <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.4em]">Bulk Registry Sync</h3>
                </div>
                <p class="text-sm text-slate-500 font-medium mb-4">
                    Automate updates by uploading a structured dataset. 
                    <span class="block mt-2">
                        <a href="{{ route('schools.sample') }}" class="text-red-800 font-bold text-xs uppercase tracking-widest border-b-2 border-red-200 hover:border-red-800 transition-all">
                            Download Master Template
                        </a>
                    </span>
                </p>
            </div>
            
            <form action="{{ route('schools.import') }}" method="POST" enctype="multipart/form-data" class="w-full lg:w-auto">
                @csrf
                <div class="flex flex-col sm:flex-row items-stretch gap-4">
                    <div class="relative flex-1 group">
                        <input type="file" name="csv_file" accept=".csv" required 
                               onchange="document.getElementById('fileNameDisplay').innerText = this.files[0].name"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl px-6 py-4 flex items-center gap-4 group-hover:border-red-300 transition-all">
                            <div class="text-left">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Select File</p>
                                <p id="fileNameDisplay" class="text-xs font-bold text-slate-600 truncate max-w-[150px]">Choose CSV...</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-800 transition-all">
                        Execute Protocol
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 03. MAIN DATA TABLE --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden print:border-slate-900">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200 print:bg-transparent">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest print:text-black">
                    <th class="p-5 border-r border-slate-200">School ID</th>
                    <th class="p-5 border-r border-slate-200">Institutional Name</th>
                    <th class="p-5 border-r border-slate-200 text-center">Teachers</th>
                    <th class="p-5 border-r border-slate-200 text-center">Enrollees</th>
                    <th class="p-5 text-center no-print">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($schools as $school)
                    <tr class="border-b border-slate-100 hover:bg-red-50/30 transition-colors group">
                        <td class="p-5 border-r border-slate-100 font-mono font-bold text-slate-500 print:text-black">{{ $school->school_id }}</td>
                        <td class="p-5 border-r border-slate-100 font-black text-slate-800 uppercase tracking-tight">
                            {{ $school->name }}
                        </td>
                        <td class="p-5 border-r border-slate-100 text-center font-bold tabular-nums">
                            {{ number_format($school->no_of_teachers) }}
                        </td>
                        <td class="p-5 border-r border-slate-100 text-center font-bold tabular-nums">
                            {{ number_format($school->no_of_enrollees) }}
                        </td>
                        <td class="p-5 text-center no-print">
                            <a href="{{ route('schools.edit', $school->id) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-red-800 uppercase tracking-widest hover:text-black transition-colors">
                                Edit Profile →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-20 text-center text-slate-400 uppercase font-black tracking-widest text-xs">
                            No Institutional Records Found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($schools->hasPages())
            <div class="p-6 bg-slate-50 border-t border-slate-200 no-print">
                {{ $schools->links() }}
            </div>
        @endif
    </div>

    {{-- 04. EMERGENCY CONTROLS --}}
    <div class="mt-12 flex flex-col items-center gap-4 no-print">
        <form action="{{ route('schools.clear_all') }}" method="POST" onsubmit="return confirm('CRITICAL WARNING: This will wipe the entire registry. Proceed?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em] hover:text-red-800 transition-colors">
                ⚠ Emergency Wipe Protocol
            </button>
        </form>
        
        <p class="text-[9px] font-black text-slate-200 uppercase tracking-[0.5em] mt-4">
            Division of Zamboanga City Data Systems
        </p>
    </div>
</div>
@endsection