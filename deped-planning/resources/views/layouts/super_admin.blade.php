<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - DepEd</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --deped-red: #a52a2a;
            --sidebar-width: 240px;
        }
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Inter', sans-serif; 
            font-size: 13px;
        }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
        a { text-decoration: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @media print {
            nav, aside, .no-print, button, form { display: none !important; }
            body { background: white !important; color: #000 !important; font-size: 10pt; height: auto !important; overflow: visible !important; }
            header { border-bottom: 2px solid #000 !important; position: static !important; background: white !important; color: black !important;}
        }
    </style>
</head>

{{-- Adjusted Body for strict dashboard layout: fixed height, hidden overflow --}}
<body class="bg-slate-50 flex h-screen overflow-hidden text-slate-700" x-data="{ sidebarOpen: false }">

    {{-- Global Loader --}}
    <div id="globalLoader" class="fixed inset-0 z-[9999] hidden flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-sm">
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 border-2 border-slate-700 rounded-full"></div>
            <div class="absolute inset-0 border-2 border-white rounded-full border-t-transparent animate-spin"></div>
        </div>
        <div class="mt-4 text-center">
            <p class="text-white font-bold uppercase text-[10px] tracking-[0.2em] animate-pulse">Authenticating...</p>
        </div>
    </div>

    {{-- Sidebar Mobile Overlay --}}
    <div x-show="sidebarOpen" 
         x-cloak 
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         x-transition.opacity>
    </div>

    {{-- Responsive Sidebar (Full Height) --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-[240px] text-white h-full flex flex-col flex-shrink-0 shadow-xl transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 no-print" 
           style="background-color: var(--deped-red);"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
           
        <div class="p-5 font-bold text-lg text-white border-b border-white/10 uppercase tracking-tighter flex items-center justify-between gap-3 shrink-0">
            <div class="flex items-center gap-3">
                <i data-lucide="shield-check" class="w-5 h-5 text-white/80"></i>
                <span class="font-cinzel">Super Admin</span>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-white/70 hover:text-white transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
            
        <nav class="mt-2 flex-grow overflow-y-auto text-[12px] pb-4">
            <div class="px-5 py-3 text-[9px] font-black uppercase text-white/40 tracking-[0.15em]">Main Hub</div>
            
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('superadmin.notifications') }}" class="flex items-center justify-between px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('superadmin.notifications') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Account Requests</span>
                </div>
                @php $pendingCount = \App\Models\User::where('status', 'pending')->count() ?? 0; @endphp
                @if($pendingCount > 0)
                    <span class="bg-white text-red-900 text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="mt-4 px-5 py-3 text-[9px] font-black uppercase text-white/40 tracking-[0.15em]">Inventory & Data</div>

            <a href="{{ route('admin.schools') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('admin.schools') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
                <i data-lucide="school" class="w-4 h-4"></i>
                <span>Manage Schools</span>
            </a>

            <a href="{{ route('admin.schools.archive') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('admin.schools.archive') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
                <i data-lucide="archive" class="w-4 h-4"></i>
                <span>School Archive</span>
            </a>

            <div class="mt-4 px-5 py-3 text-[9px] font-black uppercase text-white/40 tracking-[0.15em]">Governance</div>
            
            <a href="{{ route('admin.health_report') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('admin.health_report') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
                <i data-lucide="activity" class="w-4 h-4"></i>
                <span>Health Report</span>
            </a>

            <a href="{{ route('superadmin.history') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('superadmin.history') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>System History</span>
            </a>

            <a href="{{ route('admin.map') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('admin.map') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
                <span>Registry Map</span>
            </a>
        </nav>

        <div class="mt-auto border-t border-white/10 bg-black/10 shrink-0">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-6 py-4 text-white hover:bg-red-900/50 text-[12px] font-bold transition-all">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Logout System
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Right Side Wrapper (Scrolls independently) --}}
    <div class="flex-1 flex flex-col h-full overflow-y-auto w-full relative">
        
        {{-- Responsive Unified Slim Header --}}
        <header class="bg-[#a52a2a] text-white shadow-sm relative z-30 w-full no-print shrink-0">
            <div class="px-4 py-3 md:px-6 md:py-2">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 relative">
                    
                    {{-- Mobile Menu Trigger --}}
                    <button @click="sidebarOpen = true" class="absolute left-0 top-0 lg:hidden text-white hover:text-white/80 transition p-1">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    {{-- Left Logos --}}
                    <div class="flex items-center gap-2 md:gap-3 shrink-0 mt-6 md:mt-0">
                        <img src="{{ asset('images/deped.png') }}" alt="DepEd" class="h-10 md:h-12 w-auto drop-shadow-sm">
                        <img src="{{ asset('images/r9.png') }}" alt="Region IX" class="h-10 md:h-12 w-auto drop-shadow-sm">
                    </div>

                    {{-- Central Branding --}}
                    <div class="flex flex-col font-cinzel text-white items-center md:items-start text-center md:text-left flex-1 md:border-l border-white/20 md:pl-4 w-full">
                        <span class="text-[7px] md:text-[8px] tracking-[0.2em] leading-tight font-bold uppercase opacity-80">Republic of the Philippines</span>
                        <span class="text-[7px] md:text-[8px] tracking-[0.2em] leading-tight font-bold uppercase opacity-80">Department of Education</span>
                        <h1 class="text-sm md:text-lg lg:text-xl tracking-tight font-black leading-tight uppercase mt-0.5">
                            {{ $site_settings->header_title ?? 'Zamboanga City Division' }}
                        </h1>
                    </div>

                    {{-- Right Logo & Time --}}
                    <div class="hidden md:flex items-center gap-4 shrink-0">
                        <div class="text-right border-r border-white/20 pr-4 hidden xl:block">
                            <p class="text-[9px] uppercase font-bold opacity-70">Server Time</p>
                            <p class="text-[10px] font-mono leading-none">{{ now()->format('H:i') }} PHT</p>
                        </div>
                        <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-10 w-auto opacity-80">
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="p-4 md:p-6 bg-slate-50 flex-grow shrink-0">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        {{-- Fixed Layout Footer --}}
        <footer class="bg-[#f2f2f2] text-slate-700 pt-10 pb-6 border-t border-gray-300 mt-auto shrink-0 relative" x-data="{ activeSection: null }">
            <div class="container mx-auto px-4 md:px-6 lg:px-12 xl:px-20">
                
                {{-- Top Section: Logos and Accordion/Grid Links --}}
                <div class="grid grid-cols-2 lg:flex lg:flex-row items-center lg:items-start gap-y-8 lg:gap-10 justify-between">
                    
                    {{-- 1. Left Logo (col-span-1 on mobile, flex on desktop) --}}
                    <div class="col-span-1 lg:w-auto flex justify-center lg:justify-start flex-shrink-0 order-2 lg:order-1">
                        @php $footerLeftLogos = isset($site_logos) ? $site_logos->where('position', 'footer_left') : collect(); @endphp
                        @forelse($footerLeftLogos as $logo)
                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[100px] md:w-[130px] opacity-80 mix-blend-multiply object-contain">
                        @empty
                            <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[100px] md:w-[130px] opacity-80 mix-blend-multiply object-contain">
                        @endforelse
                    </div>

                    {{-- 2. Middle Sections: Text & Links --}}
                    <div class="col-span-2 w-full lg:flex-1 grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-8 order-1 lg:order-2 lg:px-8">
                        
                        {{-- Republic Info --}}
                        <div class="border-b border-gray-300 md:border-none">
                            <button @click="activeSection = (activeSection === 'rep' ? null : 'rep')" 
                                    class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                <h2 class="font-bold text-[12px] md:text-sm uppercase tracking-wider text-slate-800">Republic of the Philippines</h2>
                                <i data-lucide="chevron-down" class="w-4 h-4 md:hidden transition-transform" :class="activeSection === 'rep' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'rep' ? 'max-h-40 pb-4' : 'max-h-0 md:mt-4'">
                                <p class="text-[13px] leading-relaxed whitespace-pre-line text-slate-600 font-light">
                                    {{ $site_settings->footer_about ?? 'All content is in the public domain unless otherwise stated.' }}
                                </p>
                            </div>
                        </div>

                        {{-- About GOVPH (Or Custom Sections) --}}
                        <div class="border-b border-gray-300 md:border-none">
                            @if(!empty($site_settings->footer_sections))
                                @foreach($site_settings->footer_sections as $index => $section)
                                    <button @click="activeSection = (activeSection === 'sec'+{{ $index }} ? null : 'sec'+{{ $index }})" 
                                            class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                        <h2 class="font-bold text-[12px] md:text-sm uppercase tracking-wider text-slate-800">{{ $section['title'] }}</h2>
                                        <i data-lucide="chevron-down" class="w-4 h-4 md:hidden transition-transform" :class="activeSection === 'sec'+{{ $index }} ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'sec'+{{ $index }} ? 'max-h-60 pb-4' : 'max-h-0 md:mt-4'">
                                        @if(!empty($section['content']))
                                            <p class="text-[13px] leading-relaxed mb-3 whitespace-pre-line text-slate-600 font-light">{{ $section['content'] }}</p>
                                        @endif
                                        @if(!empty($section['links']))
                                            <ul class="text-[13px] space-y-2.5 font-light">
                                                @foreach($section['links'] as $link)
                                                    <li><a href="{{ $link['url'] ?? '#' }}" class="text-slate-600 hover:text-red-800 transition-colors">{{ $link['label'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <button @click="activeSection = (activeSection === 'gov' ? null : 'gov')" 
                                        class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                    <h2 class="font-bold text-[12px] md:text-sm uppercase tracking-wider text-slate-800">About GOVPH</h2>
                                    <i data-lucide="chevron-down" class="w-4 h-4 md:hidden transition-transform" :class="activeSection === 'gov' ? 'rotate-180' : ''"></i>
                                </button>
                                <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'gov' ? 'max-h-40 pb-4' : 'max-h-0 md:mt-4'">
                                    <ul class="text-[13px] space-y-2.5 font-light">
                                        <li><a href="https://www.gov.ph" target="_blank" class="text-slate-600 hover:text-red-800 transition-colors">GOV.PH</a></li>
                                        <li><a href="#" class="text-slate-600 hover:text-red-800 transition-colors">Open Data Portal</a></li>
                                        <li><a href="#" class="text-slate-600 hover:text-red-800 transition-colors">Official Gazette</a></li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Contact Us --}}
                        <div class="border-b border-gray-300 md:border-none">
                            <button @click="activeSection = (activeSection === 'contact' ? null : 'contact')" 
                                    class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                <h2 class="font-bold text-[12px] md:text-sm uppercase tracking-wider text-slate-800">Contact Us</h2>
                                <i data-lucide="chevron-down" class="w-4 h-4 md:hidden transition-transform" :class="activeSection === 'contact' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'contact' ? 'max-h-80 pb-4' : 'max-h-0 md:mt-4'">
                                <div class="text-[13px] space-y-4 text-slate-600 font-light">
                                    @if(!empty($site_settings->address))
                                        <div>
                                            @foreach($site_settings->address as $address) <span class="block">{{ $address }}</span> @endforeach
                                        </div>
                                    @endif
                                    @if(!empty($site_settings->contact_email))
                                        <div>
                                            @foreach($site_settings->contact_email as $email) 
                                                <a href="mailto:{{ $email }}" class="block hover:text-red-800 transition-colors">{{ $email }}</a> 
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Right Logo --}}
                    <div class="col-span-1 lg:w-auto flex justify-center lg:justify-end flex-shrink-0 order-3 lg:order-3">
                        @php $footerRightLogos = isset($site_logos) ? $site_logos->where('position', 'footer_right') : collect(); @endphp
                        @forelse($footerRightLogos as $logo)
                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[80px] md:w-[100px] object-contain">
                        @empty
                            <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[80px] md:w-[100px] object-contain">
                        @endforelse
                    </div>
                </div>

                {{-- Bottom Copyright & Portal Access Link (Matches Screenshot perfectly) --}}
                <div class="mt-12 pt-5 border-t border-gray-200/80 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider text-center md:text-left">
                        &copy; 2026 Department of Education - Zamboanga City Division
                    </p>
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-slate-700 transition-colors flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider focus:outline-none no-underline">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i> PORTAL ACCESS
                    </a>
                </div>
            </div>
        </footer>

    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Loader logic
        document.addEventListener('submit', function(e) {
            if (!e.target.classList.contains('search-form')) {
                const loader = document.getElementById('globalLoader');
                if (loader) loader.classList.remove('hidden');
            }
        });

        window.showLoader = function() {
            document.getElementById('globalLoader').classList.remove('hidden');
        };
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>