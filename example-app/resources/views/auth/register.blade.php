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
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Password</label>
                                <input type="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
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
</body>
</html>