<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $site_settings->header_title ?? 'DepEd Zamboanga City Division' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .font-cinzel { font-family: 'Cinzel', serif; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #a52a2a; border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex-1">
        <header class="bg-[#a52a2a] text-white py-2 md:py-4 px-2 md:px-10 shadow-lg relative z-50">
        @php 
            $leftLogos = isset($site_logos) ? $site_logos->where('position', 'left') : collect(); 
            $rightLogos = isset($site_logos) ? $site_logos->where('position', 'right') : collect();
        @endphp

        <div class="container mx-auto flex flex-row items-center justify-between gap-1 sm:gap-2 md:gap-6">
            
            {{-- Left Logos --}}
            <div class="flex items-center gap-1 sm:gap-2 md:gap-4 flex-shrink-0">
                @if($leftLogos->isNotEmpty())
                    @foreach($leftLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="h-8 sm:h-12 md:h-20 w-auto drop-shadow-md">
                    @endforeach
                @else
                        <img src="{{ asset('images/deped.png') }}" alt="DepEd Logo" class="h-8 sm:h-12 md:h-20 w-auto drop-shadow-md">
                        <img src="{{ asset('images/r9.png') }}" alt="Region IX Logo" class="h-8 sm:h-12 md:h-20 w-auto drop-shadow-md">
                @endif
            </div>

            {{-- Text Content --}}
            <div class="flex flex-col font-cinzel text-white items-center md:items-start text-center md:text-left mx-1 sm:mx-2 md:mx-0 md:ml-6 md:mr-auto justify-center">
                <span class="text-[6.5px] sm:text-[9px] md:text-sm tracking-wider leading-tight font-black">Republic of the Philippines</span>
                <span class="text-[6.5px] sm:text-[9px] md:text-sm tracking-wider leading-tight pb-0 font-black">Department Of Education</span>
                <div class="w-full border-b-[1px] md:border-b-[2px] border-white my-0.5 md:my-1"></div>
                <h1 class="text-[8px] sm:text-[11px] md:text-[25px] tracking-wide pt-0 font-black leading-tight">{{ $site_settings->header_title ?? 'Zamboanga City Division' }}</h1>
            </div>

            {{-- Right Logos --}}
            <div class="flex items-center gap-1 sm:gap-2 md:gap-4 flex-shrink-0">
                @if($rightLogos->isNotEmpty())
                    @foreach($rightLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="h-8 sm:h-12 md:h-20 w-auto opacity-90 hover:opacity-100 transition-opacity">
                    @endforeach
                @else
                    <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-8 sm:h-12 md:h-20 w-auto opacity-90 hover:opacity-100 transition-opacity drop-shadow-md">
                @endif
            </div>

        </div>
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