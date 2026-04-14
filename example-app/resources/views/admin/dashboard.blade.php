@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeModal: null }">
    
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 text-sm font-medium rounded-r shadow-sm flex items-center">
            <i data-lucide="check-circle-2" class="w-5 h-5 mr-2 text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-[#a52a2a] text-[#a52a2a] text-sm font-medium rounded-r shadow-sm flex items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 mr-2 text-[#a52a2a]"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- NEW: Personalized Admin Greeting & Date --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 border-b-2 border-slate-200 pb-6 gap-4">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-2">
                <i data-lucide="user" class="w-3.5 h-3.5"></i> Welcome back, {{ auth()->user()->name ?? 'Administrator' }}
            </p>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">System Overview</h1>
            <p class="text-[#a52a2a] font-bold tracking-wide mt-1 uppercase text-sm">Zamboanga City Division Planning Analytics</p>
        </div>
        <div class="text-left md:text-right bg-white px-5 py-3 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-black text-slate-600 uppercase tracking-widest flex items-center gap-2 md:justify-end">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#a52a2a]"></i> {{ now()->format('l, F j, Y') }}
            </p>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Current Active Session</p>
        </div>
    </div>

    <h2 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2 uppercase tracking-wide">
         <i data-lucide="building-2" class="w-5 h-5 text-[#a52a2a]"></i> Infrastructure Health
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#a52a2a]/20 hover:shadow-md hover:border-[#a52a2a]/40 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-[#a52a2a] uppercase tracking-wider mb-1">Total Registered Schools</p>
                    <p class="text-4xl font-black text-slate-800">{{ number_format($schoolCount) }}</p>
                </div>
                <div class="p-3 bg-[#a52a2a]/10 rounded-xl text-[#a52a2a]">
                    <i data-lucide="school" class="w-6 h-6"></i>
                </div>
            </div>
            <a href="{{ route('admin.schools') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-[#a52a2a] hover:text-[#7a1f1f] font-bold">
                Manage Schools <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div @click="activeModal = 'classroom'" class="bg-gradient-to-br from-[#a52a2a]/5 to-white p-6 rounded-2xl shadow-sm border border-[#a52a2a]/20 hover:shadow-lg hover:border-[#a52a2a]/60 hover:-translate-y-1 cursor-pointer transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-[#a52a2a] uppercase tracking-wider mb-1">Classroom Shortage</p>
                    <p class="text-4xl font-black text-slate-800">{{ number_format($classroomShortageSchools->count()) }}</p>
                </div>
                <div class="p-3 bg-[#a52a2a]/10 rounded-xl text-[#a52a2a] group-hover:bg-[#a52a2a] group-hover:text-white transition-colors">
                    <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-4 group-hover:text-[#a52a2a] flex items-center gap-1">
                Click to view list <i data-lucide="external-link" class="w-3 h-3"></i>
            </p>
        </div>

        <div @click="activeModal = 'toilet'" class="bg-gradient-to-br from-[#a52a2a]/5 to-white p-6 rounded-2xl shadow-sm border border-[#a52a2a]/20 hover:shadow-lg hover:border-[#a52a2a]/60 hover:-translate-y-1 cursor-pointer transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-[#a52a2a] uppercase tracking-wider mb-1">Toilet Shortage</p>
                    <p class="text-4xl font-black text-slate-800">{{ number_format($toiletShortageSchools->count()) }}</p>
                </div>
                <div class="p-3 bg-[#a52a2a]/10 rounded-xl text-[#a52a2a] group-hover:bg-[#a52a2a] group-hover:text-white transition-colors">
                    <i data-lucide="bath" class="w-6 h-6"></i>
                </div>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-4 group-hover:text-[#a52a2a] flex items-center gap-1">
                Click to view list <i data-lucide="external-link" class="w-3 h-3"></i>
            </p>
        </div>
    </div>

    <h2 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2 uppercase tracking-wide">
         <i data-lucide="shield-alert" class="w-5 h-5 text-[#a52a2a]"></i> Utilities & Risk Assessment
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#a52a2a]/20 hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-5 p-4">
                <i data-lucide="zap" class="w-24 h-24 text-[#a52a2a]"></i>
            </div>
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="zap" class="w-5 h-5 text-[#a52a2a]"></i>
                <p class="text-xs font-bold text-[#a52a2a] uppercase tracking-wider">Electricity Status</p>
            </div>
            <div class="flex justify-between items-end mb-2 relative z-10">
                <div>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($withPowerCount) }}</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">With Power</p>
                </div>
                <div @click="activeModal = 'power'" class="text-right cursor-pointer group px-3 py-1 -mr-3 -mb-1 rounded-lg hover:bg-rose-50 transition-colors">
                    <p class="text-2xl font-black text-[#a52a2a] group-hover:scale-110 transition-transform origin-right">{{ number_format($withoutPowerSchools->count()) }}</p>
                    <p class="text-[10px] text-[#a52a2a]/70 font-bold uppercase tracking-wide group-hover:text-[#a52a2a] flex items-center gap-1 justify-end">
                        Without Power <i data-lucide="pointer" class="w-3 h-3"></i>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#a52a2a]/20 hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-5 p-4">
                <i data-lucide="droplets" class="w-24 h-24 text-[#a52a2a]"></i>
            </div>
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="droplets" class="w-5 h-5 text-[#a52a2a]"></i>
                <p class="text-xs font-bold text-[#a52a2a] uppercase tracking-wider">Potable Water</p>
            </div>
            <div class="flex justify-between items-end mb-2 relative z-10">
                <div>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($withWaterCount) }}</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">With Water</p>
                </div>
                <div @click="activeModal = 'water'" class="text-right cursor-pointer group px-3 py-1 -mr-3 -mb-1 rounded-lg hover:bg-rose-50 transition-colors">
                    <p class="text-2xl font-black text-[#a52a2a] group-hover:scale-110 transition-transform origin-right">{{ number_format($withoutWaterSchools->count()) }}</p>
                    <p class="text-[10px] text-[#a52a2a]/70 font-bold uppercase tracking-wide group-hover:text-[#a52a2a] flex items-center gap-1 justify-end">
                        Without Water <i data-lucide="pointer" class="w-3 h-3"></i>
                    </p>
                </div>
            </div>
        </div>

        <div @click="activeModal = 'hazard'" class="bg-[#a52a2a] p-6 rounded-2xl shadow-md border border-[#7a1f1f] hover:shadow-xl hover:-translate-y-1 cursor-pointer transition-all text-white group">
            <div class="flex items-center gap-2 mb-1 justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="triangle-alert" class="w-5 h-5 text-white/80"></i>
                    <p class="text-xs font-bold text-white/90 uppercase tracking-wider">High Risk Hazards</p>
                </div>
                <i data-lucide="external-link" class="w-4 h-4 text-white/50 group-hover:text-white transition-colors"></i>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <span class="text-5xl font-black text-white">{{ number_format($highHazardSchools->count()) }}</span>
                <span class="text-xs text-white/80 font-medium leading-tight tracking-wide">Schools marked<br>as high risk</span>
            </div>
        </div>

    </div>

    <h2 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2 uppercase tracking-wide">
         <i data-lucide="hard-drive" class="w-5 h-5 text-[#a52a2a]"></i> System Records & Activity
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#a52a2a]/20 hover:shadow-md transition-shadow flex items-center justify-between group">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Archived Facilities</p>
                <p class="text-3xl font-black text-slate-800">{{ number_format($archivedSchoolsCount) }}</p>
                <a href="{{ route('schools.archive') }}" class="mt-2 inline-flex items-center gap-1 text-xs text-[#a52a2a] font-bold group-hover:underline">
                    View Archive <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
            <div class="h-14 w-14 bg-[#a52a2a]/10 rounded-full flex items-center justify-center text-[#a52a2a]">
                <i data-lucide="archive" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#a52a2a]/20 hover:shadow-md transition-shadow flex items-center justify-between group">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total History Logs</p>
                <p class="text-3xl font-black text-slate-800">{{ number_format($totalActivityLogs) }}</p>
                <a href="{{ route('admin.history') }}" class="mt-2 inline-flex items-center gap-1 text-xs text-[#a52a2a] font-bold group-hover:underline">
                    Review Audit Trail <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
            <div class="h-14 w-14 bg-[#a52a2a]/10 rounded-full flex items-center justify-center text-[#a52a2a]">
                <i data-lucide="file-clock" class="w-6 h-6"></i>
            </div>
        </div>

    </div>

    {{-- GLOBAL EVALUATION PARAMETERS --}}
    <h2 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2 uppercase tracking-wide">
         <i data-lucide="sliders-horizontal" class="w-5 h-5 text-[#a52a2a]"></i> Global Evaluation Parameters
    </h2>
    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-[#a52a2a]/20 mb-10 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-5 p-4 pointer-events-none">
            <i data-lucide="settings-2" class="w-32 h-32 text-[#a52a2a]"></i>
        </div>
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
            <div class="flex-1 w-full grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[9px] font-black text-[#a52a2a] uppercase tracking-widest block mb-2">Learners per Room</label>
                    <div class="flex items-center bg-slate-50 border-2 border-slate-200 rounded-xl overflow-hidden focus-within:border-[#a52a2a] transition-colors">
                        <div class="px-4 py-3 bg-slate-100 border-r border-slate-200 text-slate-400"><i data-lucide="door-open" class="w-4 h-4"></i></div>
                        <input type="number" id="global_ratio_classroom" class="w-full bg-transparent p-3 font-mono text-lg font-black text-slate-800 outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-[9px] font-black text-[#a52a2a] uppercase tracking-widest block mb-2">Learners per Chair</label>
                    <div class="flex items-center bg-slate-50 border-2 border-slate-200 rounded-xl overflow-hidden focus-within:border-[#a52a2a] transition-colors">
                        <div class="px-4 py-3 bg-slate-100 border-r border-slate-200 text-slate-400"><i data-lucide="armchair" class="w-4 h-4"></i></div>
                        <input type="number" id="global_ratio_chair" class="w-full bg-transparent p-3 font-mono text-lg font-black text-slate-800 outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-[9px] font-black text-[#a52a2a] uppercase tracking-widest block mb-2">Learners per Toilet</label>
                    <div class="flex items-center bg-slate-50 border-2 border-slate-200 rounded-xl overflow-hidden focus-within:border-[#a52a2a] transition-colors">
                        <div class="px-4 py-3 bg-slate-100 border-r border-slate-200 text-slate-400"><i data-lucide="bath" class="w-4 h-4"></i></div>
                        <input type="number" id="global_ratio_toilet" class="w-full bg-transparent p-3 font-mono text-lg font-black text-slate-800 outline-none">
                    </div>
                </div>
            </div>
            
            <div class="shrink-0 flex flex-col items-end">
                <button onclick="saveGlobalRatios()" id="saveRatiosBtn" class="bg-[#a52a2a] text-white px-8 py-3.5 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-black transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Apply System Wide
                </button>
                <span id="ratioSaveStatus" class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest mt-2 opacity-0 transition-opacity">Parameters Synced</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-md border border-[#a52a2a]/30 overflow-hidden mb-8 relative">
        <div class="h-2 bg-[#a52a2a] w-full"></div>
        <div class="p-6 sm:p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-6 h-6 text-[#a52a2a]"></i> Geographic Distribution
                    </h2>
                    <p class="text-xs font-medium text-slate-500 mt-1 uppercase tracking-wide">Interactive map of all registered DepEd facilities.</p>
                </div>
                <a href="{{ route('admin.map') }}" class="bg-[#a52a2a] text-white hover:bg-[#7a1f1f] px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition-all active:scale-95 cursor-pointer flex items-center gap-2">
                    <i data-lucide="map" class="w-4 h-4"></i> Open Full Map
                </a>
            </div>

            <div id="dashboardMap" class="w-full h-[450px] rounded-2xl border border-slate-300 shadow-inner z-0"></div>
        </div>
    </div>

    {{-- DYNAMIC MODALS --}}
    <div x-cloak x-show="activeModal !== null" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-transition.opacity>
        <div @click.away="activeModal = null" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden" x-transition.scale.origin.bottom>
            
            <div class="bg-[#a52a2a] text-white px-6 py-4 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-lg flex items-center gap-2 tracking-wide uppercase text-sm">
                    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                    <span x-show="activeModal === 'classroom'">Schools with Classroom Shortage</span>
                    <span x-show="activeModal === 'toilet'">Schools with Toilet Shortage</span>
                    <span x-show="activeModal === 'hazard'">Facilities Marked High Risk</span>
                    <span x-show="activeModal === 'power'">Facilities Without Electricity</span>
                    <span x-show="activeModal === 'water'">Facilities Without Potable Water</span>
                </h3>
                <button @click="activeModal = null" class="text-white/70 hover:text-white transition-colors bg-black/10 hover:bg-black/20 p-2 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="overflow-y-auto flex-1 p-0">
                
                {{-- CLASSROOM SHORTAGE TABLE --}}
                <table x-show="activeModal === 'classroom'" class="w-full text-left border-collapse">
                    <thead class="bg-slate-100 sticky top-0 border-b-2 border-slate-200 z-10">
                        <tr>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest">School ID</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest w-2/5">School Name</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Classroom Shortage</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($classroomShortageSchools as $school)
                        <tr class="hover:bg-rose-50/50 transition-colors group">
                            <td class="py-4 px-6 text-sm font-bold text-slate-700">{{ $school->school_id }}</td>
                            <td class="py-4 px-6 text-sm font-semibold text-slate-600">{{ $school->name }}</td>
                            <td class="py-4 px-6 text-lg font-black text-[#a52a2a] text-center">{{ $school->classroom_shortage }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('schools.edit', $school->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-[#a52a2a] hover:bg-[#a52a2a] hover:text-white hover:border-[#a52a2a] px-3 py-1.5 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all shadow-sm opacity-50 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-sm font-medium text-slate-400 italic">No schools are currently reporting a classroom shortage.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- TOILET SHORTAGE TABLE --}}
                <table x-show="activeModal === 'toilet'" class="w-full text-left border-collapse">
                    <thead class="bg-slate-100 sticky top-0 border-b-2 border-slate-200 z-10">
                        <tr>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest">School ID</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest w-2/5">School Name</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Toilet Shortage</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($toiletShortageSchools as $school)
                        <tr class="hover:bg-rose-50/50 transition-colors group">
                            <td class="py-4 px-6 text-sm font-bold text-slate-700">{{ $school->school_id }}</td>
                            <td class="py-4 px-6 text-sm font-semibold text-slate-600">{{ $school->name }}</td>
                            <td class="py-4 px-6 text-lg font-black text-[#a52a2a] text-center">{{ $school->toilet_shortage }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('schools.edit', $school->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-[#a52a2a] hover:bg-[#a52a2a] hover:text-white hover:border-[#a52a2a] px-3 py-1.5 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all shadow-sm opacity-50 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-sm font-medium text-slate-400 italic">No schools are currently reporting a toilet shortage.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- HAZARDS TABLE --}}
                <table x-show="activeModal === 'hazard'" class="w-full text-left border-collapse">
                    <thead class="bg-slate-100 sticky top-0 border-b-2 border-slate-200 z-10">
                        <tr>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest">School ID</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest w-2/5">School Name</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Hazard Type</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($highHazardSchools as $school)
                        <tr class="hover:bg-rose-50/50 transition-colors group">
                            <td class="py-4 px-6 text-sm font-bold text-slate-700">{{ $school->school_id }}</td>
                            <td class="py-4 px-6 text-sm font-semibold text-slate-600">{{ $school->name }}</td>
                            <td class="py-4 px-6 text-sm font-bold text-[#a52a2a] text-center uppercase tracking-wider">{{ $school->hazard_type }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('schools.edit', $school->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-[#a52a2a] hover:bg-[#a52a2a] hover:text-white hover:border-[#a52a2a] px-3 py-1.5 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all shadow-sm opacity-50 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-sm font-medium text-slate-400 italic">No high-risk schools detected in the system.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- POWER TABLE --}}
                <table x-show="activeModal === 'power'" class="w-full text-left border-collapse">
                    <thead class="bg-slate-100 sticky top-0 border-b-2 border-slate-200 z-10">
                        <tr>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest w-1/4">School ID</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest w-1/2">School Name</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($withoutPowerSchools as $school)
                        <tr class="hover:bg-rose-50/50 transition-colors group">
                            <td class="py-4 px-6 text-sm font-bold text-slate-700">{{ $school->school_id }}</td>
                            <td class="py-4 px-6 text-sm font-semibold text-slate-600">{{ $school->name }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('schools.edit', $school->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-[#a52a2a] hover:bg-[#a52a2a] hover:text-white hover:border-[#a52a2a] px-3 py-1.5 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all shadow-sm opacity-50 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-12 text-center text-sm font-medium text-slate-400 italic">All registered facilities currently have electricity.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- WATER TABLE --}}
                <table x-show="activeModal === 'water'" class="w-full text-left border-collapse">
                    <thead class="bg-slate-100 sticky top-0 border-b-2 border-slate-200 z-10">
                        <tr>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest w-1/4">School ID</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest w-1/2">School Name</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($withoutWaterSchools as $school)
                        <tr class="hover:bg-rose-50/50 transition-colors group">
                            <td class="py-4 px-6 text-sm font-bold text-slate-700">{{ $school->school_id }}</td>
                            <td class="py-4 px-6 text-sm font-semibold text-slate-600">{{ $school->name }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('schools.edit', $school->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-[#a52a2a] hover:bg-[#a52a2a] hover:text-white hover:border-[#a52a2a] px-3 py-1.5 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all shadow-sm opacity-50 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-12 text-center text-sm font-medium text-slate-400 italic">All registered facilities currently have potable water.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end shrink-0">
                <button @click="activeModal = null" class="bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 px-6 py-2 rounded-lg text-sm font-bold transition-all active:scale-95 cursor-pointer">
                    Close Window
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // JS Logic for the Global Evaluation Parameters
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('global_ratio_classroom').value = localStorage.getItem('deped_ratio_classroom') || 40;
        document.getElementById('global_ratio_chair').value = localStorage.getItem('deped_ratio_chair') || 1;
        document.getElementById('global_ratio_toilet').value = localStorage.getItem('deped_ratio_toilet') || 50;

        // Initialize the map centered on Zamboanga City
        var map = L.map('dashboardMap').setView([6.9214, 122.0790], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var schools = @json($mapSchools);

        schools.forEach(function(school) {
            if(school.latitude && school.longitude) {
                var hazardWarning = (school.hazard_type && school.hazard_type !== 'None') 
                    ? `<span class="text-[#a52a2a] font-black block mt-2 pt-2 border-t border-slate-200 uppercase tracking-widest text-[9px]">⚠️ ${school.hazard_type}</span>` 
                    : '';

                var popupContent = `
                    <div class="font-sans min-w-[150px]">
                        <strong class="text-slate-800 block mb-1">${school.name}</strong>
                        <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">ID: ${school.school_id}</span>
                        ${hazardWarning}
                    </div>
                `;
                L.marker([school.latitude, school.longitude])
                 .addTo(map)
                 .bindPopup(popupContent);
            }
        });
    });

    function saveGlobalRatios() {
        const btn = document.getElementById('saveRatiosBtn');
        const status = document.getElementById('ratioSaveStatus');
        
        // Save to Local Storage
        localStorage.setItem('deped_ratio_classroom', document.getElementById('global_ratio_classroom').value || 40);
        localStorage.setItem('deped_ratio_chair', document.getElementById('global_ratio_chair').value || 1);
        localStorage.setItem('deped_ratio_toilet', document.getElementById('global_ratio_toilet').value || 50);

        // Visual Feedback
        btn.innerHTML = `<i data-lucide="check-check" class="w-4 h-4"></i> Applied`;
        btn.classList.replace('bg-[#a52a2a]', 'bg-emerald-600');
        lucide.createIcons();
        status.classList.remove('opacity-0');

        setTimeout(() => {
            btn.innerHTML = `<i data-lucide="save" class="w-4 h-4"></i> Apply System Wide`;
            btn.classList.replace('bg-emerald-600', 'bg-[#a52a2a]');
            lucide.createIcons();
            status.classList.add('opacity-0');
        }, 2000);
    }
</script>
@endsection