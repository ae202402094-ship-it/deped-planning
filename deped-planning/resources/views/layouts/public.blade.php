@php
    // Detect if the page is being loaded in an iframe for the other team
    $isEmbed = request()->query('embed') === 'true';
@endphp

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
        
        /* Ensure the map touches the edges in embed mode */
        @if($isEmbed)
            body { background-color: white; }
            .container { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
        @endif
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900" x-data="{ mobileMenu: false }">

    {{-- 1. HEADER: Hidden if embed=true --}}
    @if(!$isEmbed)
    <header class="bg-[#a52a2a] text-white shadow-lg relative z-50">
        <div class="container mx-auto px-4 py-3 md:py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 md:gap-4 shrink-0">
                    <img src="{{ asset('images/deped.png') }}" alt="DepEd" class="h-10 sm:h-12 md:h-20 w-auto">
                    <img src="{{ asset('images/r9.png') }}" alt="Region IX" class="h-10 sm:h-12 md:h-20 w-auto">
                </div>

                <div class="flex flex-col font-cinzel items-center md:items-start text-center md:text-left flex-1 px-2">
                    <span class="text-[8px] sm:text-[10px] md:text-sm tracking-widest font-black uppercase">Republic of the Philippines</span>
                    <span class="text-[8px] sm:text-[10px] md:text-sm tracking-widest font-black uppercase">Department of Education</span>
                    <div class="w-full border-b border-white/30 my-1 md:my-2"></div>
                    <h1 class="text-xs sm:text-lg md:text-2xl lg:text-3xl font-black leading-tight tracking-wide">
                        {{ $site_settings->header_title ?? 'Zamboanga City Division' }}
                    </h1>
                </div>

                <div class="hidden md:flex items-center gap-4 shrink-0">
                    <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-16 lg:h-20 w-auto opacity-90">
                </div>
            </div>
        </div>
    </header>

    {{-- 2. NAVIGATION: Hidden if embed=true --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 no-print">
        <div class="container mx-auto px-4">
            <div class="hidden sm:flex justify-center gap-4 md:gap-8">
                <a href="{{ route('public.map') }}" 
                   class="py-4 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all {{ request()->routeIs('public.map') ? 'border-[#a52a2a] text-[#a52a2a]' : 'border-transparent text-slate-400 hover:text-[#a52a2a]' }}">
                    Interactive Map
                </a>
                <a href="{{ route('public.schools') }}" 
                   class="py-4 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all {{ request()->routeIs('public.schools') ? 'border-[#a52a2a] text-[#a52a2a]' : 'border-transparent text-slate-400 hover:text-[#a52a2a]' }}">
                    School Directories
                </a>
            </div>

            <div class="sm:hidden flex justify-between items-center py-3">
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">
                    {{ request()->routeIs('public.map') ? 'Map View' : 'Registry' }}
                </span>
                
                <button @click="mobileMenu = !mobileMenu" 
                        class="text-[#a52a2a] flex items-center gap-2 border-2 border-[#a52a2a] px-3 py-1 font-black text-[10px] uppercase outline-none active:bg-[#a52a2a] active:text-white transition-all">
                    <span x-text="mobileMenu ? 'CLOSE' : 'MENU'"></span>
                    <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'"></i>
                </button>
            </div>

            <div x-show="mobileMenu" 
                 x-cloak 
                 @click.away="mobileMenu = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="sm:hidden pb-4 space-y-1 border-t border-slate-100 pt-2 absolute left-0 right-0 bg-white px-4 shadow-xl">
                
                <a href="{{ route('public.map') }}" 
                   class="block py-3 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.map') ? 'text-[#a52a2a]' : 'text-slate-500' }}">
                    Map
                </a>
                
                <a href="{{ route('public.schools') }}" 
                   class="block py-3 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.schools') || request()->is('schools/*') ? 'text-[#a52a2a]' : 'text-slate-500' }}">
                    Directories
                </a>
            </div>
        </div>
    </nav>
    @endif
    
    {{-- 3. MAIN CONTENT: Padding removed if embedded --}}
    <main class="{{ $isEmbed ? 'py-0' : 'py-10' }}">
        @yield('content')
    </main>

    {{-- 4. FOOTER: Hidden if embed=true --}}
    @if(!$isEmbed)
    <footer class="bg-[#f2f2f2] text-gray-700 pt-10 pb-12 md:pt-16 md:pb-16 border-t border-gray-300 mt-auto relative" x-data="{ activeSection: null }">
        <div class="container mx-auto px-4 md:px-6 lg:px-20">
            <div class="grid grid-cols-2 lg:flex lg:flex-row items-center lg:items-start gap-y-8 lg:gap-10 justify-between">
                
                <div class="col-span-1 lg:w-auto flex justify-center lg:justify-start flex-shrink-0 order-2 lg:order-1">
                    @php $footerLeftLogos = isset($site_logos) ? $site_logos->where('position', 'footer_left') : collect(); @endphp
                    @forelse($footerLeftLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[100px] md:w-[150px] h-auto object-contain">
                    @empty
                        <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[100px] md:w-[150px] h-auto object-contain">
                    @endforelse
                </div>

                <div class="col-span-2 w-full flex-grow grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-8 lg:mx-10 order-1 lg:order-2">
                    {{-- Republic Info --}}
                    <div class="border-b border-gray-200 md:border-none">
                        <button @click="activeSection = (activeSection === 'rep' ? null : 'rep')" 
                                class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                            <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">Republic of the Philippines</h2>
                            <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'rep' ? 'rotate-180' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'rep' ? 'max-h-40 pb-4' : 'max-h-0 md:mt-4'">
                            <p class="text-[13px] leading-relaxed whitespace-pre-line text-gray-600">
                                {{ $site_settings->footer_about ?? 'All content is in the public domain unless otherwise stated.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Dynamic Sections --}}
                    <div class="border-b border-gray-200 md:border-none">
                        @if(!empty($site_settings->footer_sections))
                            @foreach($site_settings->footer_sections as $index => $section)
                                <button @click="activeSection = (activeSection === 'sec'+{{ $index }} ? null : 'sec'+{{ $index }})" 
                                        class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                    <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">{{ $section['title'] }}</h2>
                                </button>
                                <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'sec'+{{ $index }} ? 'max-h-60 pb-4' : 'max-h-0 md:mt-4'">
                                    <p class="text-[13px] text-gray-600">{{ $section['content'] ?? '' }}</p>
                                </div>
                            @endforeach
                        @else
                            <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">About GOVPH</h2>
                            <ul class="text-[13px] space-y-2 mt-4">
                                <li><a href="https://www.gov.ph" target="_blank" class="text-gray-500 hover:text-red-700 transition-colors">GOV.PH</a></li>
                            </ul>
                        @endif
                    </div>

                    {{-- Contact Section --}}
                    <div class="border-b border-gray-200 md:border-none">
                        <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">Contact Us</h2>
                        <div class="text-[13px] mt-4 space-y-4 text-gray-600">
                            @if(!empty($site_settings->contact_email))
                                @foreach($site_settings->contact_email as $email) 
                                    <a href="mailto:{{ $email }}" class="block hover:text-red-700 transition-colors">{{ $email }}</a> 
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-span-1 lg:w-auto flex justify-center lg:justify-end flex-shrink-0 order-3 lg:order-3">
                    <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[100px] md:w-[150px] h-auto object-contain">
                </div>
            </div>

            <div class="container mx-auto px-4 mt-10 pt-6 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest text-center md:text-left">
                    &copy; 2026 Department of Education - Zamboanga City Division
                </p>
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-[#a52a2a] transition-colors flex items-center gap-2 text-[10px] font-black uppercase tracking-widest no-underline">
                    Portal Access
                </a>
            </div>
        </div>
    </footer>
    @endif
<script>
        /**
         * Global Embed State Persistence
         * Automatically appends ?embed=true to all internal links 
         * if the current page was loaded in embed mode.
         */
        function persistEmbedMode() {
            const params = new URLSearchParams(window.location.search);
            
            if (params.get('embed') === 'true') {
                document.querySelectorAll('a').forEach(link => {
                    try {
                        const url = new URL(link.href);
                        
                        // Only modify links for your own domain to avoid breaking external links
                        if (url.origin === window.location.origin) {
                            // Append the parameter if it's missing
                            if (!url.searchParams.has('embed')) {
                                url.searchParams.set('embed', 'true');
                                link.href = url.toString();
                            }
                        }
                    } catch (e) {
                        // Silently skip non-standard links (mailto, tel, #, etc.)
                    }
                });
            }
        }

        // Run when the initial DOM is ready
        document.addEventListener("DOMContentLoaded", persistEmbedMode);

        // Run when navigating back/forward or if Alpine/Livewire updates the DOM
        window.addEventListener('popstate', persistEmbedMode);
        
        // Optional: If you use Alpine.js, you can also re-trigger on DOM changes
        if (window.Alpine) {
            Alpine.nextTick(() => persistEmbedMode());
        }
    </script>
</body>
</html>