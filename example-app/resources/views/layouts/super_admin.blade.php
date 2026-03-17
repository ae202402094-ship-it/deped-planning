<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - DepEd</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
        a { text-decoration: none; }

        /* --- STRICT PROFESSIONAL PRINT LEDGER --- */
        @media print {
        /* 1. Hide everything unnecessary */
        nav, aside, header, .no-print, .pagination, button, form {
            display: none !important;
        }

        /* 2. Reset Page & Body */
        @page { size: A4 portrait; margin: 12mm; }
        
        body {
            background: white !important;
            font-family: 'Arial', sans-serif !important;
            font-size: 10pt;
            color: black !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* 3. THE TABLE GRID (The "Make it look like this" fix) */
        table {
            width: 100% !important;
            border-collapse: collapse !important; /* Forces borders to join */
            border: 1.5pt solid #000 !important; /* Heavy outer border */
            margin-top: 10px;
        }

        th {
            background-color: #f2f2f2 !important; /* Light gray header */
            border: 1pt solid #000 !important; /* Black grid lines */
            padding: 8pt !important;
            text-transform: uppercase;
            font-size: 9pt;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
        }

        td {
            border: 1pt solid #000 !important; /* Black grid lines */
            padding: 6pt 8pt !important;
            vertical-align: top !important;
            word-wrap: break-word;
        }

        /* 4. Formatting inside the table cells */
        .print-bold { font-weight: bold !important; }
        .print-uppercase { text-transform: uppercase !important; }
        
        /* Ensure rows don't split mid-page awkwardly */
        tr { page-break-inside: avoid !important; }
    }
    </style>
</head>
<body class="bg-slate-50 flex flex-col h-screen overflow-hidden">
    
    <header style="background-color: #a52a2a;" class="p-1 flex justify-center shadow-md flex-shrink-0 z-50 relative">
        <img src="{{ asset('images/deped_zambo_header.png') }}" class="h-16 w-auto" alt="Header">
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <aside class="w-64 text-white flex flex-col flex-shrink-0 shadow-lg z-40" style="background-color: #a52a2a;">
            
            <div class="p-6 font-bold text-xl text-white border-b uppercase tracking-widest" style="border-color: rgba(255,255,255,0.1);">
                Super Admin
            </div>
            
            <nav class="mt-4 flex-grow overflow-y-auto">
                
                <a href="{{ route('superadmin.dashboard') }}" 
                   class="block px-6 py-3 transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-black/20 text-white font-semibold shadow-inner' : 'text-white hover:bg-black/10' }}">
                    Dashboard
                </a>

                <a href="{{ route('superadmin.notifications') }}" 
                   class="flex items-center justify-between px-6 py-3 transition {{ request()->routeIs('superadmin.notifications') ? 'bg-black/20 text-white font-semibold shadow-inner' : 'text-white hover:bg-black/10' }}">
                    <span>Account Requests</span>
                    
                    @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="bg-white text-xs font-bold px-2 py-1 rounded-full shadow-sm" style="color: #a52a2a;">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('superadmin.history') }}" 
                   class="block px-6 py-3 transition {{ request()->routeIs('superadmin.history') ? 'bg-black/20 text-white font-semibold shadow-inner' : 'text-white hover:bg-black/10' }}">
                    System History
                </a>

                <div class="px-6 py-3 mt-4 text-xs font-bold uppercase tracking-wider text-white">
                    Data Management
                </div>

                <a href="{{ route('admin.schools') }}" 
                   class="block px-6 py-3 transition {{ request()->routeIs('admin.schools') ? 'bg-black/20 text-white font-semibold shadow-inner' : 'text-white hover:bg-black/10' }}">
                    Manage Schools
                </a>

                <a href="{{ route('admin.map') }}" 
                   class="block px-6 py-3 transition {{ request()->routeIs('admin.map') ? 'bg-black/20 text-white font-semibold shadow-inner' : 'text-white hover:bg-black/10' }}">
                    View School Map
                </a>
            </nav>

            <div class="mt-auto border-t" style="border-color: rgba(255,255,255,0.1);">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-6 py-4 font-bold transition-all cursor-pointer text-white hover:bg-black/10">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="p-8 overflow-y-auto flex-1 bg-slate-50">
            @yield('content')
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>