<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DepEd</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        .font-cinzel { font-family: 'Cinzel', serif; }
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }

        /* Print styles preserved from your original code */
        @media print {
            nav, aside, .no-print, button, form { display: none !important; }
            body { background: white !important; color: #000 !important; }
            header { border-bottom: 3px double #000 !important; position: static !important; }
        }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen overflow-x-hidden">

    {{-- Global Loader --}}
    <div id="globalLoader" class="fixed inset-0 z-[9999] hidden flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-md">
        <div class="relative w-24 h-24">
            <div class="absolute inset-0 border-4 border-slate-700 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-red-800 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <div class="mt-8 text-center">
            <h3 class="text-white font-black uppercase tracking-[0.3em] text-xs mb-2">System Processing</h3>
            <p class="text-slate-400 font-bold uppercase text-[9px] tracking-widest animate-pulse">Synchronizing Registry Data...</p>
        </div>
    </div>

    {{-- Sidebar: Stays on the left --}}
    <aside class="w-64 text-white flex flex-col flex-shrink-0 z-40 shadow-lg no-print" style="background-color: #a52a2a;">
        <div class="p-6 font-bold text-xl text-white border-b border-white/20 uppercase tracking-widest">
            Admin Panel
        </div>
        
        <nav class="mt-4 flex-grow overflow-y-auto">
            <div class="px-6 py-2 text-[9px] font-black uppercase text-white/40 tracking-[0.2em]">Management</div>
            <a href="{{ route('admin.schools') }}" class="block px-6 py-3 text-white hover:bg-black/10 transition {{ request()->routeIs('admin.schools') ? 'bg-black/20 font-black' : '' }}">Manage Schools</a>
            <a href="{{ route('admin.history') }}" class="block px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition">Audit Logs</a>

            <div class="mt-6 px-6 py-2 text-[9px] font-black uppercase text-white/40 tracking-[0.2em]">Live Tools</div>
            <a href="{{ route('admin.map') }}" class="block px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition {{ request()->routeIs('admin.map') ? 'bg-black/20 font-black' : '' }}">Registry Map</a>
            
            <div class="mt-6 px-6 py-2 text-[9px] font-black uppercase text-white/40 tracking-[0.2em]">Public Preview</div>
            <a href="{{ route('public.map') }}" target="_blank" class="block px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition">View Interactive Map</a>
            <a href="{{ route('public.schools') }}" target="_blank" class="block px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition">View Directory</a>
        </nav>

        <div class="mt-auto border-t border-white/20">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full text-left px-6 py-4 text-white hover:bg-black/10 font-bold transition-all cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Wrapper: Header and Content scroll together --}}
    <div class="flex-1 flex flex-col overflow-y-auto">
        
        {{-- Fixed Header Layout: Non-Sticky --}}
        <header class="bg-[#a52a2a] text-white shadow-lg relative z-10 w-full no-print">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between gap-6">
                    
                    {{-- Left Logos --}}
                    <div class="flex items-center gap-4 shrink-0">
                        <img src="{{ asset('images/deped.png') }}" alt="DepEd" class="h-16 w-auto drop-shadow-md">
                        <img src="{{ asset('images/r9.png') }}" alt="Region IX" class="h-16 w-auto drop-shadow-md">
                    </div>

                    {{-- Central Branding --}}
                    <div class="flex flex-col font-cinzel text-white items-start text-left flex-1 border-l border-white/20 pl-6">
                        <span class="text-[9px] tracking-widest leading-tight font-black uppercase">Republic of the Philippines</span>
                        <span class="text-[9px] tracking-widest leading-tight font-black uppercase">Department of Education</span>
                        <div class="w-full border-b border-white/30 my-1"></div>
                        <h1 class="text-xl lg:text-2xl tracking-wide font-black leading-tight uppercase">
                            {{ $site_settings->header_title ?? 'Zamboanga City Division' }}
                        </h1>
                    </div>

                    {{-- Right Logo --}}
                    <div class="hidden xl:block shrink-0">
                        <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-16 w-auto opacity-90">
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="p-8 bg-slate-50 min-h-screen">
            @yield('content')
        </main>

    </div>

    <script>
        {{-- Preserved Form Interceptor from your original code --}}
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