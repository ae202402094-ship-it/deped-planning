<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DepEd</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        /* This prevents the Bootstrap Modal backdrop from graying out the wrong layers */
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
    </style>
</head>
<body class="bg-slate-50 flex">
    <aside class="w-64 bg-slate-800 text-white min-h-screen no-print">
        <div class="p-6 font-bold text-xl text-red-500 border-b border-slate-700 uppercase tracking-widest">
            Admin Panel
        </div>
        <nav class="mt-4">
    <a href="{{ route('admin.schools') }}" class="block px-6 py-3 hover:bg-slate-700 transition">Manage Schools</a>
    <a href="{{ route('admin.map') }}" class="block px-6 py-3 hover:bg-slate-700 transition text-slate-400">View School Map</a>
</nav>
    </aside>

    <div class="flex-1">
        <header style="background-color: #a52a2a;" class="p-1 flex justify-center shadow-md">
            <img src="{{ asset('images/deped_zambo_header.png') }}" class="h-16 w-auto" alt="Header">
        </header>

        <main class="p-8">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>