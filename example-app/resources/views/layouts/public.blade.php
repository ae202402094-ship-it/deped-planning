<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Zamboanga - School Census</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex-1">
        <header style="background-color: #a52a2a;" class="p-4 shadow-md flex flex-col items-center gap-4">
            <img src="{{ asset('images/deped_zambo_header.png') }}" class="h-24 w-auto" alt="Header">
            
            <nav class="flex gap-8 border-t border-white/20 pt-4">
                {{-- Home Button linking to the Interactive Map --}}
                <a href="{{ route('public.map') }}" class="text-white font-black uppercase text-[10px] tracking-[0.2em] hover:text-slate-200 transition flex items-center gap-2">
                    <i class=""></i> Interactive Map
                </a>

                <a href="{{ route('public.schools') }}" class="text-white font-black uppercase text-[10px] tracking-[0.2em] hover:text-slate-200 transition flex items-center gap-2">
                    <i class=" "></i> Schools
                </a>
            </nav>
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