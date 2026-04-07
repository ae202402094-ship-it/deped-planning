<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - DepEd</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --deped-red: #a52a2a;
            --sidebar-width: 240px;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            font-size: 13px; /* Smaller base font for professional look */
        }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
        a { text-decoration: none !important; }
        
        /* Custom scrollbar for professional feel */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #999; }

        @media print {
            nav, aside, .no-print, button, form { display: none !important; }
            body { background: white !important; color: #000 !important; font-size: 10pt; }
            header { border-bottom: 2px solid #000 !important; position: static !important; background: white !important; color: black !important;}
        }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen overflow-x-hidden text-slate-700">

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

    {{-- Sidebar --}}
   {{-- Sidebar --}}
<aside class="w-[240px] text-white min-h-screen no-print flex flex-col flex-shrink-0 z-40 shadow-xl" style="background-color: var(--deped-red);">
    <div class="p-5 font-bold text-lg text-white border-b border-white/10 uppercase tracking-tighter flex items-center gap-3">
        <i data-lucide="shield-check" class="w-5 h-5 text-white/80"></i>
        <span class="font-cinzel">Super Admin</span>
    </div>
        
    <nav class="mt-2 flex-grow overflow-y-auto text-[12px]">
        <div class="px-5 py-3 text-[9px] font-black uppercase text-white/40 tracking-[0.15em]">Main Hub</div>
        
        {{-- Updated the text color classes here from text-white/90 to just text-white to ensure maximum contrast against the red --}}
        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('superadmin.notifications') }}" class="flex items-center justify-between px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('superadmin.notifications') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
            <div class="flex items-center gap-3">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Account Requests</span>
            </div>
            @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="bg-white text-red-900 text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm">{{ $pendingCount }}</span>
            @endif
        </a>

        <div class="mt-4 px-5 py-3 text-[9px] font-black uppercase text-white/40 tracking-[0.15em]">Inventory & Data</div>

        <a href="{{ route('admin.schools') }}" class="flex items-center gap-3 px-5 py-2.5 text-white hover:bg-black/10 transition-all {{ request()->routeIs('admin.schools') ? 'bg-black/20 font-semibold border-r-4 border-white' : '' }}">
            <i data-lucide="school" class="w-4 h-4"></i>
            <span>Manage Schools</span>
        </a>

        {{-- Note the change from text-white/80 to text-white --}}
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

    <div class="mt-auto border-t border-white/10 bg-black/10">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-6 py-4 text-white hover:bg-red-900/50 text-[12px] font-bold transition-all">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Logout System
            </button>
        </form>
    </div>
</aside>

    {{-- Main Wrapper --}}
    <div class="flex-1 flex flex-col overflow-y-auto">
        
        {{-- Unified Slim Header --}}
        <header class="bg-[#a52a2a] text-white shadow-md relative z-10 w-full no-print">
            <div class="px-6 py-2"> {{-- Reduced padding for slim look --}}
                <div class="flex items-center justify-between gap-4">
                    
                    {{-- Left Logos --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <img src="{{ asset('images/deped.png') }}" alt="DepEd" class="h-12 w-auto drop-shadow-sm">
                        <img src="{{ asset('images/r9.png') }}" alt="Region IX" class="h-12 w-auto drop-shadow-sm">
                    </div>

                    {{-- Central Branding --}}
                    <div class="flex flex-col font-cinzel text-white items-start text-left flex-1 border-l border-white/20 pl-4">
                        <span class="text-[8px] tracking-[0.2em] leading-tight font-bold uppercase opacity-80">Republic of the Philippines</span>
                        <span class="text-[8px] tracking-[0.2em] leading-tight font-bold uppercase opacity-80">Department of Education</span>
                        <h1 class="text-lg lg:text-xl tracking-tight font-black leading-tight uppercase mt-0.5">
                            {{ $site_settings->header_title ?? 'Zamboanga City Division' }}
                        </h1>
                    </div>

                    {{-- Right Logo --}}
                    <div class="hidden md:flex items-center gap-4 shrink-0">
                        <div class="text-right border-r border-white/20 pr-4 hidden lg:block">
                            <p class="text-[9px] uppercase font-bold opacity-70">Server Time</p>
                            <p class="text-[10px] font-mono leading-none">{{ now()->format('H:i') }} PHT</p>
                        </div>
                        <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-10 w-auto opacity-80">
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="p-6 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
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