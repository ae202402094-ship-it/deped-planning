<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DepEd</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        /* Standard UI layering */
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }

        @media print {
            nav, aside, .no-print, button, form, .danger-zone, .pagination, .coord-hint { display: none !important; }
            @page { size: A4; margin: 2cm; }
            body { background: white !important; color: #000 !important; font-family: "Inter", "Segoe UI", serif !important; line-height: 1.5; height: auto !important; overflow: visible !important; }
            .flex-1 { margin: 0 !important; padding: 0 !important; }
            main { padding: 0 !important; overflow: visible !important; }
            header { background-color: transparent !important; border-bottom: 3px double #000 !important; margin-bottom: 2rem !important; padding-bottom: 1rem !important; position: static !important; }
            header img { filter: grayscale(100%) brightness(0); height: 80px !important; }
            table { width: 100% !important; border-collapse: collapse !important; margin: 1.5rem 0 !important; }
            th { background-color: #f8fafc !important; border: 1px solid #000 !important; text-transform: uppercase !important; font-size: 8pt !important; padding: 10px !important; }
            td { border: 1px solid #cbd5e1 !important; padding: 10px !important; font-size: 10pt !important; }
        }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

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

    <aside class="w-64 text-white min-h-screen no-print flex flex-col flex-shrink-0 z-40 shadow-lg" style="background-color: #a52a2a;">
        <div class="p-6 font-bold text-xl text-white border-b border-white/20 uppercase tracking-widest">
            Admin Panel
        </div>
        
        <nav class="mt-4 flex-grow overflow-y-auto">
            <a href="{{ route('admin.schools') }}" class="block px-6 py-3 text-white hover:bg-black/10 transition">Manage Schools</a>
            <a href="{{ route('admin.map') }}" class="block px-6 py-3 text-gray-200 hover:text-white hover:bg-black/10 transition">View School Map</a>
            <a href="{{ route('admin.history') }}" class="block px-6 py-3 text-gray-200 hover:text-white hover:bg-black/10 transition">View History</a>
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

    <div class="flex-1 flex flex-col overflow-hidden">
        
        <header style="background-color: #a52a2a;" class="p-1 flex justify-center shadow-md flex-shrink-0 z-50">
            <img src="{{ asset('images/deped_zambo_header.png') }}" class="h-16 w-auto" alt="Header">
        </header>

        <main class="p-8 flex-1 overflow-y-auto bg-slate-50">
            @yield('content')
        </main>

    </div>

    <script>
        /**
         * Global Form Interceptor
         * Automatically triggers the loader when any form (except search) is submitted.
         */
        document.addEventListener('submit', function(e) {
            // Check if the form has the 'search-form' class to skip loading screen
            if (!e.target.classList.contains('search-form')) {
                const loader = document.getElementById('globalLoader');
                if (loader) {
                    loader.classList.remove('hidden');
                }
            }
        });

        /**
         * Optional: Manual Loader Trigger
         * Can be called manually: window.showLoader();
         */
        window.showLoader = function() {
            document.getElementById('globalLoader').classList.remove('hidden');
        };
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>