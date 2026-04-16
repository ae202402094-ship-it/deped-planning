@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- System Alerts for CSV Import (Hidden on Print) --}}
    @if(session('success'))
        <div class="no-print mb-8 bg-emerald-50 border-2 border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-sm">
            <i class="bi bi-check-circle-fill text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="no-print mb-8 bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest flex flex-col gap-3 shadow-sm">
            <div class="flex items-center gap-3"><i class="bi bi-exclamation-triangle-fill text-lg"></i> <span>Protocol Violation / Error</span></div>
            @if($errors->any())
                <ul class="list-disc list-inside ml-8 text-[9px] text-red-600 opacity-80">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            @if(session('error'))
                <span class="ml-8 text-[9px] text-red-600 opacity-80">> {{ session('error') }}</span>
            @endif
        </div>
    @endif

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
    <div class="no-print flex flex-col xl:flex-row justify-between items-start xl:items-center mb-10 gap-6">
        <div class="w-full xl:w-auto flex-1">
            <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tight">School Registry</h2>
            <p class="text-sm text-slate-500 font-bold uppercase tracking-widest italic mb-4">Manage Institutional Data & Verification</p>
            
            {{-- RESTORED SEARCH BAR --}}
            <form action="{{ route('admin.schools') }}" method="GET" class="relative max-w-md search-form">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Registry ID or Name..." 
                       class="w-full bg-white border-2 border-slate-200 pl-11 pr-10 py-3 rounded-2xl font-bold text-[10px] uppercase tracking-widest text-slate-700 focus:outline-none focus:border-[#a52a2a] transition-all shadow-sm">
                @if(request('search'))
                    <a href="{{ route('admin.schools') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 hover:text-red-700 transition-colors" title="Clear Search">
                        <i class="bi bi-x-circle-fill text-lg"></i>
                    </a>
                @endif
            </form>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center shrink-0">
            
            {{-- Import CSV Modal Trigger --}}
            <button type="button" onclick="openImportModal()" class="bg-white border-2 border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:border-[#a52a2a] hover:text-[#a52a2a] transition-all flex items-center gap-2 shrink-0 shadow-sm">
                <i class="bi bi-cloud-upload-fill text-sm"></i> Batch Import
            </button>

            {{-- Clear All Data --}}
            <form action="{{ route('schools.clear_all') }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to purge ALL registry data? This action cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-50 border-2 border-red-100 text-red-600 px-4 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-100 hover:border-red-200 transition-all flex items-center justify-center shadow-sm" title="Purge All Records">
                    <i class="bi bi-trash3-fill text-sm"></i>
                </button>
            </form>

            {{-- Archives Button --}}
            <a href="{{ route('schools.archive') }}" class="bg-white border-2 border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:border-amber-500 hover:text-amber-600 transition-all flex items-center gap-2 shadow-sm">
                <i class="bi bi-archive-fill"></i> Archives
            </a>

            {{-- Print --}}
            <button onclick="window.print()" class="bg-white border-2 border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="bi bi-printer-fill"></i> Report
            </button>

            {{-- Add New --}}
            <a href="{{ route('schools.create') }}" class="bg-[#a52a2a] text-white px-8 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-900/20 hover:bg-black transition-all whitespace-nowrap">
                Add New
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
                        <td colspan="5" class="p-20 text-center text-slate-400">
                            <i class="bi bi-search block text-4xl mb-4 opacity-50"></i>
                            <span class="uppercase font-black tracking-[0.2em] text-sm">No Registry Data Found</span>
                            @if(request('search'))
                                <p class="text-xs font-bold mt-2">No results for "{{ request('search') }}"</p>
                            @endif
                        </td>
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

    {{-- ENHANCED IMPORT MODAL --}}
    <div id="importModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-all opacity-0 pointer-events-none" style="transition: opacity 0.3s ease;">
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="importModalContent">
            
            <div class="bg-slate-50 border-b border-slate-100 p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Batch Import Registry</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Upload CSV Data</p>
                </div>
                <button type="button" onclick="closeImportModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <form action="{{ route('schools.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col" id="importForm">
                @csrf
                <div class="p-8">
                    
                    {{-- User Guide Step-by-Step --}}
                    <div class="mb-8 flex gap-4 text-left bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
                        <div class="text-blue-500 mt-0.5"><i class="bi bi-info-circle-fill text-lg"></i></div>
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-blue-800 mb-2">Import Instructions</h4>
                            <ol class="text-xs text-blue-900/80 font-medium space-y-1.5 list-decimal list-inside marker:text-blue-400 marker:font-black">
                                <li><a href="{{ route('schools.sample') }}" class="underline decoration-blue-300 hover:text-[#a52a2a] transition-colors font-bold">Download the CSV template</a>.</li>
                                <li>Fill in your institutional data.</li>
                                <li>Upload the saved file below.</li>
                            </ol>
                        </div>
                    </div>

                    {{-- Interactive File Drop Area --}}
                    <div id="dropzone" class="relative border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center bg-slate-50 transition-all duration-200 group">
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               onchange="updateFileName(this)">
                        
                        {{-- Default Upload Prompt --}}
                        <div id="dropzone-prompt" class="flex flex-col items-center gap-3 pointer-events-none transition-all">
                            <div class="w-14 h-14 bg-white text-slate-400 border border-slate-200 rounded-full flex items-center justify-center group-hover:bg-[#a52a2a] group-hover:text-white group-hover:border-[#a52a2a] group-hover:scale-110 transition-all shadow-sm">
                                <i class="bi bi-cloud-upload text-2xl"></i>
                            </div>
                            <div class="flex flex-col mt-2">
                                <span class="text-xs font-black text-slate-700 uppercase tracking-tight group-hover:text-[#a52a2a] transition-colors">Click or Drag & Drop</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">.CSV files only</span>
                            </div>
                        </div>

                        {{-- Active File Selected State --}}
                        <div id="dropzone-file" class="hidden flex flex-col items-center gap-3 relative z-20">
                            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-full flex items-center justify-center shadow-sm">
                                <i class="bi bi-file-earmark-check-fill text-2xl"></i>
                            </div>
                            <div class="flex flex-col items-center mt-2">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Ready to upload</span>
                                <span id="fileNameDisplay" class="text-sm font-black text-slate-800 tracking-tight mt-1 px-4 truncate w-full max-w-[250px]"></span>
                            </div>
                            <button type="button" onclick="clearFile(event)" class="mt-3 text-[10px] font-black uppercase tracking-widest text-red-500 hover:text-red-700 hover:underline bg-white px-3 py-1.5 rounded-lg border border-red-100 transition-colors cursor-pointer relative z-30">
                                Remove File
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="bg-slate-50 border-t border-slate-100 p-6 flex gap-4">
                    <button type="button" onclick="closeImportModal()" class="flex-1 bg-white border-2 border-slate-200 text-slate-600 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">
                        Cancel
                    </button>
                    {{-- Button is disabled initially to prevent empty form submission --}}
                    <button type="submit" id="submitImportBtn" disabled class="flex-1 bg-[#a52a2a] text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-900/20 hover:bg-black transition-all flex items-center justify-center gap-2 opacity-50 cursor-not-allowed">
                        <i class="bi bi-cloud-arrow-up-fill" id="importIcon"></i>
                        <svg id="importSpinner" class="hidden animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span id="importBtnText">Execute Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- Modal & Drag/Drop JavaScript Logic --}}
<script>
    const modal = document.getElementById('importModal');
    const modalContent = document.getElementById('importModalContent');
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('csv_file');
    const promptArea = document.getElementById('dropzone-prompt');
    const fileArea = document.getElementById('dropzone-file');
    const fileDisplay = document.getElementById('fileNameDisplay');
    const submitBtn = document.getElementById('submitImportBtn');

    // 1. Modal Open/Close Logic
    function openImportModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeImportModal() {
        modal.classList.add('opacity-0', 'pointer-events-none');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            clearFile(); // Reset state
        }, 300);
    }

    // 2. Drag and Drop Visual Feedback
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('border-[#a52a2a]', 'bg-red-50/20');
            dropzone.classList.remove('border-slate-300', 'bg-slate-50');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('border-[#a52a2a]', 'bg-red-50/20');
            dropzone.classList.add('border-slate-300', 'bg-slate-50');
        }, false);
    });

    // Handle dropped file
    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if(files.length > 0) {
            fileInput.files = files;
            updateFileName(fileInput);
        }
    }, false);

    // 3. File Processing and UI Update
    function updateFileName(input) {
        if (input.files && input.files.length > 0) {
            const fileName = input.files[0].name;
            
            // Basic validation
            if(!fileName.endsWith('.csv')) {
                alert('Invalid file format. Please upload a .CSV file.');
                clearFile();
                return;
            }

            fileDisplay.innerText = fileName;
            
            // Switch UI States
            promptArea.classList.add('hidden');
            fileArea.classList.remove('hidden');
            dropzone.classList.add('bg-white', 'border-[#a52a2a]');
            dropzone.classList.remove('bg-slate-50', 'border-slate-300');
            
            // Enable Submit Button
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function clearFile(e = null) {
        if(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        // Reset Input
        fileInput.value = '';
        
        // Reset UI States
        promptArea.classList.remove('hidden');
        fileArea.classList.add('hidden');
        dropzone.classList.remove('bg-white', 'border-[#a52a2a]');
        dropzone.classList.add('bg-slate-50', 'border-slate-300');
        
        // Disable Submit Button
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // 4. Form Submit Spinner Logic
    document.getElementById('importForm').addEventListener('submit', function() {
        if (fileInput.files.length > 0) {
            document.getElementById('importIcon').classList.add('hidden');
            document.getElementById('importSpinner').classList.remove('hidden');
            document.getElementById('importBtnText').innerText = 'Processing...';
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
        }
    });
</script>
@endsection