<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Zamboanga - School Census</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900">
   <div class="flex-1">
        <header style="background-color: #a52a2a;" class="p-1 flex justify-center shadow-md">
            <img src="{{ asset('images/deped_zambo_header.png') }}" class="h-16 w-auto" alt="Header">
        </header>
    </div>

    <main class="py-10">
        @yield('content')
    </main>

    <footer class="text-center py-8 text-slate-400 text-xs uppercase tracking-widest border-t border-slate-200 mt-10">
        &copy; 2026 Department of Education - Zamboanga City
    </footer>
</body>
</html>