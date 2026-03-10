<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DepEd Zamboanga City</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex flex-col min-h-screen">

    <header style="background-color: #a52a2a;" class="fixed p-1 flex justify-center z-40 w-full items-center shadow-md">
        <img src="{{ asset('images/deped_zambo_header.png') }}" class="w-full max-w-4xl h-auto" alt="Header">
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

    <footer class="py-4 text-center text-slate-400 text-[10px] uppercase tracking-widest">
        &copy; {{ date('Y') }} DepEd Zamboanga City Planning Module
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