@extends('layouts.admin')

@section('content')

{{-- ADVANCED PRINT STYLESHEET --}}
<style>
    @media print {
        @page { size: landscape; margin: 12mm; }
        body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; background: white !important; }
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .print-shadow-none { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        .print-break-inside-avoid { break-inside: avoid; }
        
        /* Official DepEd Header Styling */
        .official-print-header { border-bottom: 4px double #1e293b; padding-bottom: 1.5rem; margin-bottom: 2rem; }
        .deped-red { color: #7f1d1d !important; } /* Deep red for print */
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- System Alerts --}}
    @if(session('success'))
        <div class="no-print mb-8 bg-emerald-50 border-2 border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-sm">
            <i class="bi bi-check-circle-fill text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- 00. OFFICIAL DEPED PRINT HEADER (Visible on Paper Only) --}}
    <div class="print-only official-print-header">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-6">
                <img src="{{ asset('images/deped.png') }}" class="h-24 w-auto" alt="DepEd Logo">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-slate-800 leading-tight">Republic of the Philippines</span>
                    <h1 class="text-3xl font-black uppercase deped-red leading-tight tracking-tight">Department of Education</h1>
                    <p class="text-[11px] font-bold italic text-slate-600 uppercase tracking-widest mt-1">Division of Zamboanga City | Planning & Research Section</p>
                </div>
            </div>
            <div class="text-right flex flex-col items-end">
                <div class="border-2 border-slate-900 px-4 py-2 mb-2">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 block leading-none">Document Control</span>
                    <span class="text-[14px] font-black deped-red uppercase tracking-widest block mt-1">Registry Report</span>
                </div>
                <p class="text-[10px] font-mono font-bold text-slate-600 uppercase leading-none">Serial: REG-{{ now()->format('Ymd') }}-{{ strtoupper(Str::random(4)) }}</p>
            </div>
        </div>

        <div class="flex justify-between items-end mt-8">
            <div>
                <h2 class="text-2xl font-black uppercase tracking-tighter text-slate-900">Institutional Masterlist</h2>
                <p class="text-[10px] text-slate-600 font-bold uppercase tracking-[0.2em] mt-1" id="printFilterCriteria">
                    Showing: All Levels • All Districts
                </p>
            </div>
            <div class="text-right">
                <p class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Generated On</p>
                <p class="text-sm font-bold text-slate-900">{{ now()->format('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    {{-- 01. INTERACTIVE PAGE HEADER (Hidden on Print) --}}
    <div class="no-print flex flex-col xl:flex-row justify-between items-start xl:items-center mb-10 gap-6">
        <div class="w-full xl:w-auto flex-1">
            <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tight">School Registry</h2>
            <p class="text-sm text-slate-500 font-bold uppercase tracking-widest italic mb-4">Manage Institutional Data & Verification</p>
            
            {{-- SEARCH BAR & ADVANCED FILTERS --}}
            <form action="{{ route('admin.schools') }}" method="GET" class="flex flex-wrap gap-3 w-full max-w-5xl search-form items-center">
                
                {{-- Search Input --}}
                <div class="relative flex-1 min-w-[180px] flex items-center bg-white border-2 border-slate-200 rounded-2xl focus-within:border-[#a52a2a] transition-all shadow-sm pl-4 pr-3 py-1">
                    <i class="bi bi-search text-slate-400 mr-2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID or Name..." 
                           class="w-full bg-transparent font-bold text-[10px] uppercase tracking-widest text-slate-700 focus:outline-none py-2">
                </div>

                {{-- Level Filter --}}
                <div class="flex items-center bg-white border-2 border-slate-200 rounded-2xl shadow-sm focus-within:border-[#a52a2a] transition-all px-3">
                    <select name="level" onchange="this.form.submit()" class="py-3 pr-2 bg-transparent font-bold text-[10px] uppercase tracking-widest text-slate-700 outline-none cursor-pointer">
                        <option value="">All Levels</option>
                        <option value="Primary" {{ request('level') == 'Primary' ? 'selected' : '' }}>Primary (Elem)</option>
                        <option value="Secondary" {{ request('level') == 'Secondary' ? 'selected' : '' }}>Secondary (HS)</option>
                    </select>
                </div>

                {{-- District Filter --}}
                <div class="flex items-center bg-white border-2 border-slate-200 rounded-2xl shadow-sm focus-within:border-[#a52a2a] transition-all px-3">
                    <select name="district" onchange="this.form.submit()" class="py-3 pr-2 bg-transparent font-bold text-[10px] uppercase tracking-widest text-slate-700 outline-none cursor-pointer">
                        <option value="">All Districts</option>
                        @if(isset($districts))
                            @foreach($districts as $dist)
                                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                @if(request('search') || request('level') || request('district'))
                    <a href="{{ route('admin.schools') }}" class="flex items-center text-red-500 hover:text-red-700 transition-colors px-2" title="Clear Filters">
                        <i class="bi bi-x-circle-fill text-lg"></i>
                    </a>
                @endif
            </form>
        </div>
        
        {{-- ACTION BUTTONS --}}
        <div class="flex flex-wrap gap-4 items-center shrink-0 mt-4 xl:mt-0">
            <button type="button" onclick="openImportModal()" class="bg-white border-2 border-slate-200 text-slate-600 px-5 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:border-[#a52a2a] hover:text-[#a52a2a] transition-all flex items-center gap-2 shadow-sm">
                <i class="bi bi-cloud-upload-fill text-sm"></i> Batch Import
            </button>

            <a href="{{ route('schools.archive') }}" class="bg-white border-2 border-slate-200 text-slate-600 px-5 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:border-amber-500 hover:text-amber-600 transition-all flex items-center gap-2 shadow-sm">
                <i class="bi bi-archive-fill"></i> Archives
            </a>

            {{-- INTERACTIVE PRINT BUTTON --}}
            <button type="button" onclick="openPrintConfigModal()" class="bg-[#1e293b] text-white border-2 border-[#1e293b] px-5 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all flex items-center gap-2 shadow-sm">
                <i class="bi bi-printer-fill"></i> Print Report
            </button>

            <a href="{{ route('schools.create') }}" class="bg-[#a52a2a] text-white px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-900/20 hover:bg-black transition-all whitespace-nowrap">
                Add New
            </a>
        </div>
    </div>

    {{-- 02. REGISTRY TABLE --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-2xl shadow-slate-200/50 overflow-hidden print-shadow-none print:rounded-none" id="printableTableContainer">
        <table class="w-full text-left border-collapse print:w-full">
            <thead class="bg-slate-50 border-b border-slate-200 print:bg-slate-100 print:border-slate-400">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest print:text-slate-800">
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300 print:py-4">ID CODE</th>
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300 print:py-4 w-1/3">Institutional Entity</th>
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300 print:py-4">Classification</th>
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300 text-center print:py-4">Faculty</th>
                    <th class="p-6 border-r border-slate-200/50 print:border-slate-300 text-center print:py-4">Enrollees</th>
                    <th class="p-6 text-center no-print">Operations</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($schools as $school)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors print:border-slate-300 print-break-inside-avoid">
                        <td class="p-6 border-r border-slate-50 print:border-slate-300 font-mono font-bold text-slate-500 print:text-slate-900 print:py-3">
                            #{{ $school->school_id }}
                        </td>
                        <td class="p-6 border-r border-slate-50 print:border-slate-300 print:py-3">
                            <span class="font-black text-slate-800 uppercase tracking-tight block">{{ $school->name }}</span>
                        </td>
                        <td class="p-6 border-r border-slate-50 print:border-slate-300 print:py-3">
                            <div class="flex flex-wrap gap-1 mb-1">
                                {{-- LEVEL BADGE --}}
                                <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 text-[8px] font-black uppercase tracking-widest rounded-md print:border-slate-400">
                                    {{ $school->school_level ?? 'N/A' }}
                                </span>
                            </div>
                            <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1 print:text-slate-800"><i class="bi bi-geo-alt-fill"></i> {{ $school->district ?? 'No District' }}</span>
                        </td>
                        <td class="p-6 border-r border-slate-50 print:border-slate-300 text-center font-bold tabular-nums print:text-slate-900 print:py-3">
                            {{ number_format($school->no_of_teachers) }}
                        </td>
                        <td class="p-6 border-r border-slate-50 print:border-slate-300 text-center font-bold tabular-nums print:text-slate-900 print:py-3">
                            {{ number_format($school->no_of_enrollees) }}
                        </td>
                        <td class="p-6 text-center no-print">
                            <div class="flex justify-center items-center gap-4">
                                <a href="{{ route('schools.edit', $school->id) }}" 
                                   class="inline-flex items-center gap-1.5 text-[#a52a2a] hover:text-black font-black text-[10px] uppercase tracking-tighter transition-colors">
                                    <i class="bi bi-pencil-square"></i> Edit
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
                        <td colspan="6" class="p-20 text-center text-slate-400 print:p-10">
                            <i class="bi bi-folder-x block text-4xl mb-4 opacity-50 no-print"></i>
                            <span class="uppercase font-black tracking-[0.2em] text-sm">No Registry Data Found</span>
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

    {{-- 04. OFFICIAL VALIDATION FOOTER (Visible on Paper Only) --}}
    <div class="print-only mt-16 print-break-inside-avoid" id="printSignatureSection">
        <div class="flex justify-between items-end px-12">
            <div class="text-center w-72">
                <div class="border-b-2 border-slate-900 mb-3 h-10"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-900">Certified Correct By:</p>
                <p class="text-[9px] text-slate-600 font-bold uppercase mt-1">Division Planning Officer</p>
            </div>
            <div class="text-center w-72">
                <div class="border-b-2 border-slate-900 mb-3 h-10"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-900">Approved For Release:</p>
                <p class="text-[9px] text-slate-600 font-bold uppercase mt-1">Schools Division Superintendent</p>
            </div>
        </div>
        
        <div class="mt-16 flex flex-col items-center gap-2 border-t border-slate-300 pt-6">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.5em]">End of Official Institutional Registry</p>
            <p class="text-[8px] text-slate-500 italic font-mono">System-generated verifiable document. E-Code: {{ strtoupper(Str::random(16)) }}</p>
        </div>
    </div>

</div>

{{-- INTERACTIVE PRINT CONFIGURATION MODAL --}}
<div id="printConfigModal" class="fixed inset-0 z-[3000] hidden flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-all opacity-0 pointer-events-none no-print" style="transition: opacity 0.3s ease;">
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="printConfigContent">
        <div class="bg-[#1e293b] p-6 flex justify-between items-center text-white">
            <div>
                <h3 class="text-sm font-black uppercase tracking-widest flex items-center gap-2"><i class="bi bi-printer"></i> Print Configuration</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Customize your official PDF report</p>
            </div>
            <button type="button" onclick="closePrintConfigModal()" class="text-slate-400 hover:text-white transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-8 flex flex-col gap-5">
            
            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 flex gap-3">
                <i class="bi bi-info-circle-fill text-blue-500"></i>
                <p class="text-[10px] font-bold text-blue-800 leading-relaxed uppercase tracking-wide">The report will print the data exactly as currently filtered in the table behind this window.</p>
            </div>

            <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" id="toggleSignatures" checked class="w-5 h-5 rounded text-[#a52a2a] focus:ring-[#a52a2a] border-slate-300 cursor-pointer">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-widest text-slate-700 group-hover:text-black transition-colors">Include Signature Block</span>
                    <span class="block text-[9px] text-slate-500 font-bold uppercase mt-0.5">Appends official approval lines at the bottom.</span>
                </div>
            </label>

            <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" id="toggleFilters" checked class="w-5 h-5 rounded text-[#a52a2a] focus:ring-[#a52a2a] border-slate-300 cursor-pointer">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-widest text-slate-700 group-hover:text-black transition-colors">Display Filter Criteria</span>
                    <span class="block text-[9px] text-slate-500 font-bold uppercase mt-0.5">Shows applied levels and districts in header.</span>
                </div>
            </label>
            
        </div>
        <div class="bg-slate-50 border-t border-slate-100 p-6 flex gap-4">
            <button type="button" onclick="closePrintConfigModal()" class="flex-1 bg-white border-2 border-slate-200 text-slate-600 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">Cancel</button>
            <button type="button" onclick="executePrint()" class="flex-1 bg-[#1e293b] text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:bg-black transition-all flex items-center justify-center gap-2">
                <i class="bi bi-printer-fill"></i> Execute Print
            </button>
        </div>
    </div>
</div>

{{-- EXISTING BATCH IMPORT MODAL --}}
<div id="importModal" class="fixed inset-0 z-[2000] hidden flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-all opacity-0 pointer-events-none no-print" style="transition: opacity 0.3s ease;">
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
                <div id="dropzone" class="relative border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center bg-slate-50 transition-all duration-200 group">
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                    <div id="dropzone-prompt" class="flex flex-col items-center gap-3 pointer-events-none transition-all">
                        <div class="w-14 h-14 bg-white text-slate-400 border border-slate-200 rounded-full flex items-center justify-center group-hover:bg-[#a52a2a] group-hover:text-white group-hover:border-[#a52a2a] group-hover:scale-110 transition-all shadow-sm">
                            <i class="bi bi-cloud-upload text-2xl"></i>
                        </div>
                        <div class="flex flex-col mt-2">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tight group-hover:text-[#a52a2a] transition-colors">Click or Drag & Drop</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">.CSV files only</span>
                        </div>
                    </div>
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
                <button type="button" onclick="closeImportModal()" class="flex-1 bg-white border-2 border-slate-200 text-slate-600 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">Cancel</button>
                <button type="submit" id="submitImportBtn" disabled class="flex-1 bg-[#a52a2a] text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-900/20 hover:bg-black transition-all flex items-center justify-center gap-2 opacity-50 cursor-not-allowed">
                    <i class="bi bi-cloud-arrow-up-fill" id="importIcon"></i>
                    <span id="importBtnText">Execute Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    /* =========================================================
       PRINT CONFIGURATION LOGIC
       ========================================================= */
    const printModal = document.getElementById('printConfigModal');
    const printModalContent = document.getElementById('printConfigContent');
    const sigSection = document.getElementById('printSignatureSection');
    const criteriaText = document.getElementById('printFilterCriteria');

    function openPrintConfigModal() {
        printModal.classList.remove('hidden');
        setTimeout(() => {
            printModal.classList.remove('opacity-0', 'pointer-events-none');
            printModalContent.classList.remove('scale-95');
            printModalContent.classList.add('scale-100');
        }, 10);
    }

    function closePrintConfigModal() {
        printModal.classList.add('opacity-0', 'pointer-events-none');
        printModalContent.classList.remove('scale-100');
        printModalContent.classList.add('scale-95');
        setTimeout(() => printModal.classList.add('hidden'), 300);
    }

    function executePrint() {
        // 1. Handle Signatures toggle
        if(document.getElementById('toggleSignatures').checked) {
            sigSection.classList.remove('hidden');
        } else {
            sigSection.classList.add('hidden');
        }

        // 2. Handle Dynamic Filter Text
        if(document.getElementById('toggleFilters').checked) {
            let urlParams = new URLSearchParams(window.location.search);
            let lvl = urlParams.get('level') || 'All Levels';
            let dist = urlParams.get('district') || 'All Districts';
            criteriaText.innerHTML = `Showing: ${lvl} &bull; ${dist}`;
            criteriaText.classList.remove('hidden');
        } else {
            criteriaText.classList.add('hidden');
        }

        // 3. Close modal and open browser print window
        closePrintConfigModal();
        setTimeout(() => {
            window.print();
        }, 400); 
    }

    /* =========================================================
       IMPORT MODAL & DRAG/DROP LOGIC
       ========================================================= */
    const importModalObj = document.getElementById('importModal');
    const importContent = document.getElementById('importModalContent');
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('csv_file');
    const submitBtn = document.getElementById('submitImportBtn');

    function openImportModal() {
        importModalObj.classList.remove('hidden');
        setTimeout(() => {
            importModalObj.classList.remove('opacity-0', 'pointer-events-none');
            importContent.classList.remove('scale-95');
            importContent.classList.add('scale-100');
        }, 10);
    }

    function closeImportModal() {
        importModalObj.classList.add('opacity-0', 'pointer-events-none');
        importContent.classList.remove('scale-100');
        importContent.classList.add('scale-95');
        setTimeout(() => {
            importModalObj.classList.add('hidden');
            clearFile(); 
        }, 300);
    }

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => dropzone.addEventListener(e, preventDefaults, false));
    function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
    
    ['dragenter', 'dragover'].forEach(e => dropzone.addEventListener(e, () => dropzone.classList.add('border-[#a52a2a]', 'bg-red-50/20')));
    ['dragleave', 'drop'].forEach(e => dropzone.addEventListener(e, () => dropzone.classList.remove('border-[#a52a2a]', 'bg-red-50/20')));

    dropzone.addEventListener('drop', (e) => {
        if(e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            updateFileName(fileInput);
        }
    });

    function updateFileName(input) {
        if (input.files && input.files.length > 0) {
            document.getElementById('fileNameDisplay').innerText = input.files[0].name;
            document.getElementById('dropzone-prompt').classList.add('hidden');
            document.getElementById('dropzone-file').classList.remove('hidden');
            dropzone.classList.add('bg-white', 'border-[#a52a2a]');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function clearFile(e = null) {
        if(e) { e.preventDefault(); e.stopPropagation(); }
        fileInput.value = '';
        document.getElementById('dropzone-prompt').classList.remove('hidden');
        document.getElementById('dropzone-file').classList.add('hidden');
        dropzone.classList.remove('bg-white', 'border-[#a52a2a]');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
</script>
@endsection