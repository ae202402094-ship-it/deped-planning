<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - DepEd</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    {{-- REMOVE THE BOOTSTRAP CSS LINK FROM HERE --}}
</head>
<body class="bg-slate-50 flex">
    <aside class="w-64 bg-slate-800 text-white min-h-screen no-print">
        <div class="p-6 font-bold text-xl text-red-500 border-b border-slate-700">ADMIN PANEL</div>
        <nav class="mt-4">
            <a href="{{ route('admin.schools') }}" class="block px-6 py-3 hover:bg-slate-700">Manage Schools</a>
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