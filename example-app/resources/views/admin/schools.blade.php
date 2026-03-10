@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">School Management</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Division of Zamboanga City</p>
        </div>

        <div class="flex gap-4 items-center">
            {{-- NEW: Link to the separate registration page --}}
            <a href="{{ route('schools.create') }}" style="background-color: #a52a2a;" class="text-white px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-900 transition shadow-lg">
                + Click Here To Register A Single School
            </a>
            {{-- NEW: Debug Purge Button --}}
    <form action="{{ route('schools.clear_all') }}" method="POST" onsubmit="return confirm('WARNING: This will delete EVERY school in the database. Proceed with Wipe Protocol?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-white border-2 border-red-200 text-red-400 px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-800 hover:text-white hover:border-red-800 transition shadow-sm">
            ⚠ Purge Registry
        </button>
    </form>
            
            <form action="{{ route('admin.schools') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID/Name..." 
                       class="w-64 border border-slate-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none shadow-sm">
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl font-bold uppercase text-[10px] tracking-widest">
                    Find
                </button>
            </form>
        </div>
    </div>
{{-- resources/views/admin/schools.blade.php --}}

{{-- Enhanced Bulk Registry Synchronization UI --}}
<div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-xl shadow-slate-200/50 mb-12 overflow-hidden relative">
    {{-- Decorative Background Element --}}
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-slate-50 rounded-full opacity-50"></div>

    <div class="relative flex flex-col lg:flex-row items-center justify-between gap-8">
        <div class="max-w-md">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-1.5 h-6 bg-red-800 rounded-full"></div>
                <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.4em]">Bulk Registry Sync</h3>
            </div>
            <p class="text-sm text-slate-500 font-medium leading-relaxed mb-4">
                Automate your institutional updates by uploading a structured dataset. 
                <span class="block mt-2">
                    <a href="{{ route('schools.sample') }}" class="inline-flex items-center gap-2 text-red-800 font-bold text-xs uppercase tracking-widest group">
                        <span class="border-b-2 border-red-200 group-hover:border-red-800 transition-all">Download Master Template</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v12m0 0l-4-4m4 4l4-4M8 20h8" />
                        </svg>
                    </a>
                </span>
            </p>
        </div>
        
        <form action="{{ route('schools.import') }}" method="POST" enctype="multipart/form-data" class="w-full lg:w-auto">
            @csrf
            <div class="flex flex-col sm:flex-row items-stretch gap-4">
                {{-- Custom Styled File Input Area --}}
                <div class="relative flex-1 group">
                    <input type="file" name="csv_file" accept=".csv" required 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl px-6 py-4 flex items-center gap-4 group-hover:border-red-300 group-hover:bg-white transition-all">
                        <div class="p-2 bg-white rounded-xl shadow-sm group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Select File</p>
                            <p id="fileNameDisplay" class="text-xs font-bold text-slate-600 truncate max-w-[150px]">Choose CSV...</p>
                        </div>
                    </div>
                </div>

                {{-- Submit Action --}}
                <button type="submit" 
                        class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-red-800 hover:shadow-lg hover:shadow-red-900/20 active:scale-95 transition-all flex items-center justify-center gap-3">
                    Execute Protocol
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
    {{-- SCHOOLS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($schools as $school)
            <a href="{{ route('schools.edit', $school->id) }}" class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">ID: {{ $school->school_id }}</span>
                        <span class="text-red-700 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-black uppercase tracking-widest">Edit Profile →</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase leading-tight mb-6 group-hover:text-red-800 transition-colors">{{ $school->name }}</h3>
                </div>
                <div class="mt-auto bg-slate-50 p-4 text-center border-t border-slate-100 group-hover:bg-red-50 transition-colors">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] group-hover:text-red-700">Open Official Census Data</span>
                </div>
            </a>
        @endforeach
    </div>
</div>

{{-- Shared Map Modal --}}
@include('admin.partials.map_modal')
<script>
    document.querySelector('input[name="csv_file"]').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : "Choose CSV...";
        const display = document.getElementById('fileNameDisplay');
        
        // Update the text to the filename
        display.innerText = fileName;
        
        // Optional: Change the color to red-800 to show it's ready
        display.classList.remove('text-slate-600');
        display.classList.add('text-red-800');
        document.querySelector('form[action*="import"]').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = `
        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Processing...
    `;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
});
    });
</script>
@endsection