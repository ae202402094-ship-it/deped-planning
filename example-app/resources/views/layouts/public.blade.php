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
<body class="bg-slate-50 text-slate-900" x-data="{ mobileMenu: false }">
    <header class="bg-[#a52a2a] text-white shadow-lg relative z-50">
        <div class="container mx-auto px-4 py-3 md:py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                
                {{-- Left Logos: Hidden on extra small, shown from small up --}}
                <div class="flex items-center gap-2 md:gap-4 shrink-0">
                    <img src="{{ asset('images/deped.png') }}" alt="DepEd" class="h-10 sm:h-12 md:h-20 w-auto">
                    <img src="{{ asset('images/r9.png') }}" alt="Region IX" class="h-10 sm:h-12 md:h-20 w-auto">
                </div>

                {{-- Central Branding: Text sizes adjust based on screen width --}}
                <div class="flex flex-col font-cinzel items-center md:items-start text-center md:text-left flex-1 px-2">
                    <span class="text-[8px] sm:text-[10px] md:text-sm tracking-widest font-black uppercase">Republic of the Philippines</span>
                    <span class="text-[8px] sm:text-[10px] md:text-sm tracking-widest font-black uppercase">Department of Education</span>
                    <div class="w-full border-b border-white/30 my-1 md:my-2"></div>
                    <h1 class="text-xs sm:text-lg md:text-2xl lg:text-3xl font-black leading-tight tracking-wide">
                        {{ $site_settings->header_title ?? 'Zamboanga City Division' }}
                    </h1>
                </div>

                {{-- Right Logos: Hidden on mobile to save space, visible on MD+ --}}
                <div class="hidden md:flex items-center gap-4 shrink-0">
                    <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-16 lg:h-20 w-auto opacity-90">
                </div>
            </div>
        </div>
    </header>

    {{-- Responsive Navigation --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 no-print">
        <div class="container mx-auto px-4">
            {{-- Desktop Menu: Stays centered on larger screens --}}
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

            {{-- 2. Mobile Menu Trigger: Visible on all screens smaller than 'sm' --}}
            <div class="sm:hidden flex justify-between items-center py-3">
                {{-- Dynamic Label based on current route --}}
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">
                    {{ request()->routeIs('public.map') ? 'Map View' : 'Registry' }}
                </span>
                
                {{-- Toggle Button --}}
                <button @click="mobileMenu = !mobileMenu" 
                        class="text-[#a52a2a] flex items-center gap-2 border-2 border-[#a52a2a] px-3 py-1 font-black text-[10px] uppercase outline-none active:bg-[#a52a2a] active:text-white transition-all">
                    <span x-text="mobileMenu ? 'CLOSE' : 'MENU'"></span>
                    <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'"></i>
                </button>
            </div>

            {{-- 3. Mobile Dropdown: Wildcard routeIs('public.*') ensures highlighting works on sub-pages --}}
            <div x-show="mobileMenu" 
                 x-cloak 
                 @click.away="mobileMenu = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="sm:hidden pb-4 space-y-1 border-t border-slate-100 pt-2 absolute left-0 right-0 bg-white px-4 shadow-xl">
                
                <a href="{{ route('public.map') }}" 
                   class="block py-3 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.map') ? 'text-[#a52a2a]' : 'text-slate-500' }}">
                    <i class="bi bi-map-fill mr-2"></i> Map
                </a>
                
                <a href="{{ route('public.schools') }}" 
                   class="block py-3 text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('public.schools') || request()->is('schools/*') ? 'text-[#a52a2a]' : 'text-slate-500' }}">
                    <i class="bi bi-list-ul mr-2"></i> Directories
                </a>
            </div>
        </div>
    </nav>
    
    <main class="py-10">
        @yield('content')
    </main>

    <footer class="bg-[#f2f2f2] text-gray-700 pt-8 pb-12 md:pt-12 md:pb-16 border-t border-gray-300 mt-auto relative">
        <div class="container mx-auto px-4 md:px-6 lg:px-20 flex flex-col md:flex-row flex-wrap lg:flex-nowrap items-center md:items-start gap-6 md:gap-8 justify-between">
            
            <div class="w-full lg:w-auto flex flex-row justify-center md:flex-col items-center md:items-start gap-4 flex-shrink-0">
                @php $footerLeftLogos = isset($site_logos) ? $site_logos->where('position', 'footer_left') : collect(); @endphp
                @if($footerLeftLogos->isNotEmpty())
                    @foreach($footerLeftLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[80px] md:w-[150px] h-auto object-contain">
                    @endforeach
                @else
                    <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[80px] md:w-[150px] h-auto object-contain">
                @endif
            </div>

            <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:flex gap-4 md:gap-8 justify-around w-full">
                <div class="text-center md:text-left max-w-[250px] hidden md:block">
                    <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                    <p class="text-[13px] leading-relaxed whitespace-pre-line">{{ $site_settings->footer_about ?? 'All content is in the public domain unless otherwise stated.' }}</p>
                </div>

                @if(!empty($site_settings->footer_sections))
                    @foreach($site_settings->footer_sections as $section)
                        <div class="text-center md:text-left max-w-[300px] hidden md:block">
                            <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">{{ $section['title'] }}</h2>
                            @if(!empty($section['content']))
                                <p class="text-[13px] leading-relaxed mb-3 whitespace-pre-line">{{ $section['content'] }}</p>
                            @endif
                            @if(!empty($section['links']) && count($section['links']) > 0)
                                <ul class="text-[13px] space-y-1">
                                    @foreach($section['links'] as $link)
                                        <li><a href="{{ $link['url'] ?? '#' }}" class="hover:text-red-700 transition-colors">{{ $link['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center md:text-left hidden md:block">
                        <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">About GOVPH</h2>
                        <ul class="text-[13px] space-y-1">
                            <li><a href="https://www.gov.ph" target="_blank" rel="noopener noreferrer" class="hover:text-red-700 transition-colors">GOV.PH</a></li>
                            <li><a href="#" target="_blank" rel="noopener noreferrer" class="hover:text-red-700 transition-colors">Open Data Portal</a></li>
                            <li><a href="#" target="_blank" rel="noopener noreferrer" class="hover:text-red-700 transition-colors">Official Gazette</a></li>
                        </ul>
                    </div>
                @endif

                <div class="text-center md:text-left w-full md:w-auto mt-4 md:mt-0">
                    <h2 class="font-bold text-sm uppercase mb-2 md:mb-4 tracking-wider text-gray-800">Contact Us</h2>
                    <div class="text-[13px] space-y-4">
                        @if(!empty($site_settings->address))
                        <div>
                            <strong>Address:</strong><br>
                            @foreach($site_settings->address as $address)
                                <span class="block">{{ $address }}</span>
                            @endforeach
                        </div>
                        @endif

                        @if(!empty($site_settings->contact_email))
                        <div>
                            <strong>Email:</strong><br>
                            @foreach($site_settings->contact_email as $email)
                                <a href="mailto:{{ $email }}" class="block hover:text-red-700 transition-colors">{{ $email }}</a>
                            @endforeach
                        </div>
                        @endif

                        @if(!empty($site_settings->contact_phone))
                        <div>
                            <strong>Phone:</strong><br>
                            @foreach($site_settings->contact_phone as $phone)
                                <span class="block">{{ $phone }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="w-full lg:w-auto flex flex-col items-center md:items-end flex-shrink-0 mt-6 md:mt-0">
                <div class="flex flex-row md:flex-col gap-4 items-center md:items-end">
                    @php $footerRightLogos = isset($site_logos) ? $site_logos->where('position', 'footer_right') : collect(); @endphp
                    @if($footerRightLogos->isNotEmpty())
                        @foreach($footerRightLogos as $logo)
                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[80px] md:w-[150px] h-auto object-contain">
                        @endforeach
                    @else
                        <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[80px] md:w-[150px] h-auto object-contain">
                    @endif
                </div>
            </div>

        </div>

        <button @click="loginModal = true" class="absolute bottom-4 right-4 text-gray-400 hover:text-[#a52a2a] transition-colors p-2 focus:outline-none" title="Admin Login">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </button>
    </footer>
</body>
</html>