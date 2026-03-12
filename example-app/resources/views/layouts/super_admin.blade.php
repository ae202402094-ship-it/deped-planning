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
        /* This prevents the Bootstrap Modal backdrop from graying out the wrong layers */
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
        /* Remove default link underlines for tailwind sidebar */
        a { text-decoration: none; }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">
    
    <aside class="w-64 bg-slate-800 text-white min-h-screen no-print flex flex-col flex-shrink-0">
        <div class="p-6 font-bold text-xl text-red-500 border-b border-slate-700 uppercase tracking-widest">
            Super Admin
        </div>
        
        <nav class="mt-4 flex-grow overflow-y-auto">
            
            <a href="{{ route('superadmin.dashboard') }}" 
               class="block px-6 py-3 hover:bg-slate-700 transition {{ request()->routeIs('superadmin.dashboard') ? 'text-white font-semibold' : 'text-slate-400' }}">
                Dashboard
            </a>

            <a href="{{ route('superadmin.notifications') }}" 
               class="flex items-center justify-between px-6 py-3 hover:bg-slate-700 transition {{ request()->routeIs('superadmin.notifications') ? 'text-white font-semibold' : 'text-slate-400' }}">
                <span>Account Requests</span>
                
                @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('superadmin.history') }}" 
               class="block px-6 py-3 hover:bg-slate-700 transition {{ request()->routeIs('superadmin.history') ? 'text-white font-semibold' : 'text-slate-400' }}">
                System History
            </a>

            <div class="px-6 py-3 mt-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                Data Management
            </div>

            <a href="{{ route('admin.schools') }}" 
               class="block px-6 py-3 hover:bg-slate-700 transition {{ request()->routeIs('admin.schools') ? 'text-white font-semibold' : 'text-slate-400' }}">
                Manage Schools
            </a>

            <a href="{{ route('admin.map') }}" 
               class="block px-6 py-3 hover:bg-slate-700 transition {{ request()->routeIs('admin.map') ? 'text-white font-semibold' : 'text-slate-400' }}">
                View School Map
            </a>
        </nav>

        <div class="mt-auto border-t border-slate-700">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full text-left px-6 py-4 text-red-400 hover:text-red-300 hover:bg-slate-700 font-bold transition-all cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <header style="background-color: #a52a2a;" class="p-1 flex justify-center shadow-md flex-shrink-0 z-50">
            <img src="{{ asset('images/deped_zambo_header.png') }}" class="h-16 w-auto" alt="Header">
        </header>

        <main class="p-8 overflow-y-auto flex-1">
            @yield('content')
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>