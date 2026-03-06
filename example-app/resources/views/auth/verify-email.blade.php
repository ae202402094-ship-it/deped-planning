<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | DepEd Zamboanga City</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex flex-col min-h-screen">

    <header style="background-color: #a52a2a;" class="fixed p-1 flex justify-center z-40 w-full items-center shadow-md">
        <img src="{{ asset('images/deped_zambo_header.png') }}" class="w-full max-w-4xl h-auto" alt="Header">
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12">
        <div class="w-full max-w-md">
            
            @if (session('resent'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 text-xs font-bold rounded shadow-sm">
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden text-center">
                <div class="h-1.5 bg-[#a52a2a] w-full"></div>
                <div class="p-8">
                    <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <h2 class="text-xl font-bold text-slate-800 uppercase mb-2">Verify Your Email</h2>
                    <p class="text-sm text-slate-600 mb-6">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full bg-red-800 hover:bg-red-700 text-white py-2.5 rounded-lg font-bold shadow-md transition-all active:scale-95 cursor-pointer">
                            Resend Verification Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 underline uppercase tracking-widest font-bold">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 text-center text-slate-400 text-[10px] uppercase tracking-widest">
        &copy; {{ date('Y') }} DepEd Zamboanga City Planning Module
    </footer>

</body>
</html>