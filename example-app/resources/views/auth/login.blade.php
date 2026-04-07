<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DepEd Zamboanga City</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12">
        <div class="w-full max-w-md">
            
            @if (session('resent'))
                <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 text-xs font-bold rounded shadow-sm">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                <div class="h-1.5 bg-[#a52a2a] w-full"></div>
                <div class="p-8">
                    <h2 class="text-xl font-bold text-slate-800 text-center uppercase mb-6">Sign In</h2>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 text-xs font-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all pr-10">
                                
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-red-800 hover:bg-red-700 text-white py-2 rounded-lg font-bold shadow-md transition-all active:scale-95 cursor-pointer">
                            Login
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-100 space-y-4">
                        <p class="text-center text-sm text-slate-500">
                            Need an account? <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Register here</a>
                        </p>

                        <div class="text-center bg-slate-50 p-3 rounded-lg">
                            <p class="text-[11px] text-slate-500 uppercase font-bold tracking-tight">Didn't receive verification email?</p>
                            <form class="inline" method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-800 font-bold text-xs underline cursor-pointer mt-1">
                                    Click here to resend link
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-[#f2f2f2] text-gray-700 pt-10 pb-12 md:pt-16 md:pb-16 border-t border-gray-300 mt-auto relative" x-data="{ activeSection: null }">
    <div class="container mx-auto px-4 md:px-6 lg:px-20">
        <div class="flex flex-col lg:flex-row items-center lg:items-start gap-10 justify-between">
            
            {{-- 1. Left Section: Logo --}}
            <div class="w-full lg:w-auto flex justify-center lg:justify-start flex-shrink-0">
                @php $footerLeftLogos = isset($site_logos) ? $site_logos->where('position', 'footer_left') : collect(); @endphp
                @forelse($footerLeftLogos as $logo)
                    <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[100px] md:w-[150px] h-auto object-contain">
                @empty
                    <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[100px] md:w-[150px] h-auto object-contain">
                @endforelse
            </div>

            {{-- 2. Middle Sections: Accordion on Mobile, Grid on Desktop --}}
            <div class="w-full flex-grow grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-8 lg:mx-10">
                
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

                {{-- Dynamic Footer Sections (About GOVPH / Custom) --}}
                <div class="border-b border-gray-200 md:border-none">
                    @if(!empty($site_settings->footer_sections))
                        @foreach($site_settings->footer_sections as $index => $section)
                            <button @click="activeSection = (activeSection === 'sec'+{{ $index }} ? null : 'sec'+{{ $index }})" 
                                    class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">{{ $section['title'] }}</h2>
                                <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'sec'+{{ $index }} ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'sec'+{{ $index }} ? 'max-h-60 pb-4' : 'max-h-0 md:mt-4'">
                                @if(!empty($section['content']))
                                    <p class="text-[13px] leading-relaxed mb-3 whitespace-pre-line text-gray-600">{{ $section['content'] }}</p>
                                @endif
                                @if(!empty($section['links']))
                                    <ul class="text-[13px] space-y-2">
                                        @foreach($section['links'] as $link)
                                            <li><a href="{{ $link['url'] ?? '#' }}" class="text-gray-500 hover:text-red-700 transition-colors">{{ $link['label'] }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <button @click="activeSection = (activeSection === 'gov' ? null : 'gov')" 
                                class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                            <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">About GOVPH</h2>
                            <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'gov' ? 'rotate-180' : ''"></i>
                        </button>
                        <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'gov' ? 'max-h-40 pb-4' : 'max-h-0 md:mt-4'">
                            <ul class="text-[13px] space-y-2">
                                <li><a href="https://www.gov.ph" target="_blank" class="text-gray-500 hover:text-red-700 transition-colors">GOV.PH</a></li>
                                <li><a href="#" class="text-gray-500 hover:text-red-700 transition-colors">Open Data Portal</a></li>
                                <li><a href="#" class="text-gray-500 hover:text-red-700 transition-colors">Official Gazette</a></li>
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Contact Section --}}
                <div class="border-b border-gray-200 md:border-none">
                    <button @click="activeSection = (activeSection === 'contact' ? null : 'contact')" 
                            class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                        <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">Contact Us</h2>
                        <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'contact' ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'contact' ? 'max-h-80 pb-4' : 'max-h-0 md:mt-4'">
                        <div class="text-[13px] space-y-4 text-gray-600">
                            @if(!empty($site_settings->address))
                                <div><strong>Address:</strong><br>
                                    @foreach($site_settings->address as $address) <span class="block">{{ $address }}</span> @endforeach
                                </div>
                            @endif
                            @if(!empty($site_settings->contact_email))
                                <div><strong>Email:</strong><br>
                                    @foreach($site_settings->contact_email as $email) 
                                        <a href="mailto:{{ $email }}" class="block hover:text-red-700 transition-colors">{{ $email }}</a> 
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Right Section: Logo --}}
            <div class="w-full lg:w-auto flex justify-center lg:justify-end flex-shrink-0 mt-4 lg:mt-0">
                @php $footerRightLogos = isset($site_logos) ? $site_logos->where('position', 'footer_right') : collect(); @endphp
                @forelse($footerRightLogos as $logo)
                    <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[100px] md:w-[150px] h-auto object-contain">
                @empty
                    <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[100px] md:w-[150px] h-auto object-contain">
                @endforelse
            </div>
        </div>

        {{-- Bottom Copyright & Admin Toggle --}}
        <div class="container mx-auto px-4 mt-10 pt-6 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest text-center md:text-left">
                &copy; 2026 Department of Education - Zamboanga City Division
            </p>
        </div>
    </div>
</footer>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            // Toggle the type attribute
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeSlashIcon.classList.add('hidden');
                eyeIcon.classList.remove('hidden');
            }
        });
    </script>

</body>
</html>