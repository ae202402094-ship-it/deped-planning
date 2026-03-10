<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | DepEd Zamboanga City</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex flex-col min-h-screen">

    <header style="background-color: #a52a2a;" class="fixed p-1 flex justify-center z-40 w-full items-center shadow-md">
        <img src="{{ asset('images/deped_zambo_header.png') }}" class="w-full max-w-4xl h-auto" alt="Header">
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12" x-data="{ submitted: {{ session('submitted') ? 'true' : 'false' }} }">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                <div class="h-1.5 bg-[#a52a2a] w-full"></div>
                <div class="p-8">
                    
                    <div x-show="!submitted">
                        <h2 class="text-xl font-bold text-slate-800 text-center uppercase mb-6">Create Account</h2>
                        
                        @if($errors->any())
                            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Password</label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all pr-10">
                                    
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg id="eyeSlashIcon1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all pr-10">
                                    
                                    <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg id="eyeSlashIcon2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-red-800 hover:bg-red-700 text-white py-2 rounded-lg font-bold shadow-md transition-all active:scale-95 cursor-pointer">
                                Register
                            </button>
                        </form>
                        <p class="mt-6 text-center text-sm text-slate-500">Already registered? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Login</a></p>
                    </div>

                    <div x-show="submitted" x-cloak class="text-center py-6">
                        <div class="text-blue-500 text-6xl mb-4">📧</div>
                        <h2 class="text-xl font-bold text-slate-800 mb-2">Check Your Email!</h2>
                        <div class="space-y-4 text-slate-600 text-sm leading-relaxed">
                            <p>We've sent a verification link to your email to ensure it's valid.</p>
                            <p class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <b>Next Step:</b> After you verify your email, the <b>Administrator</b> will review your qualification for final approval.
                            </p>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('login') }}" class="text-blue-600 font-bold uppercase tracking-widest text-xs underline">Back to Login</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script>
        function setupPasswordToggle(toggleBtnId, inputId, eyeIconId, eyeSlashIconId) {
            document.getElementById(toggleBtnId).addEventListener('click', function () {
                const passwordInput = document.getElementById(inputId);
                const eyeIcon = document.getElementById(eyeIconId);
                const eyeSlashIcon = document.getElementById(eyeSlashIconId);

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
        }

        // Initialize both toggles
        setupPasswordToggle('togglePassword', 'password', 'eyeIcon1', 'eyeSlashIcon1');
        setupPasswordToggle('toggleConfirmPassword', 'password_confirmation', 'eyeIcon2', 'eyeSlashIcon2');
    </script>
</body>
</html>