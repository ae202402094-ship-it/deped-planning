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
        /* Print styles remain exactly the same */
        nav, aside, .no-print, button, form, .danger-zone, .pagination, .coord-hint { display: none !important; }
        @page { size: A4; margin: 2cm; }
        body { background: white !important; color: #000 !important; font-family: "Inter", "Segoe UI", serif !important; line-height: 1.5; height: auto !important; overflow: visible !important; }
        .flex-1 { margin: 0 !important; padding: 0 !important; }
        main { padding: 0 !important; overflow: visible !important; }
        .max-w-7xl, .max-w-6xl { max-width: 100% !important; width: 100% !important; }
        header { background-color: transparent !important; border-bottom: 3px double #000 !important; margin-bottom: 2rem !important; padding-bottom: 1rem !important; position: static !important; }
        header img { filter: grayscale(100%) brightness(0); height: 80px !important; }
        table { width: 100% !important; border-collapse: collapse !important; margin: 1.5rem 0 !important; }
        th { background-color: #f8fafc !important; border: 1px solid #000 !important; text-transform: uppercase !important; font-size: 8pt !important; padding: 10px !important; }
        td { border: 1px solid #cbd5e1 !important; padding: 10px !important; font-size: 10pt !important; }
        thead { display: table-header-group !important; }
        details { display: block !important; border: 1px solid #000 !important; margin: 1rem 0 !important; page-break-inside: avoid; }
        details > summary { display: none !important; }
        details > div { display: block !important; background: white !important; }
        h1 { font-size: 22pt !important; font-weight: 900 !important; letter-spacing: -0.05em !important; }
        .text-red-800 { color: #000 !important; border-bottom: 1px solid #000; }
    }
</style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">
    
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>