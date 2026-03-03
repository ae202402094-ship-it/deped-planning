<!DOCTYPE html>
<html>
<head>
    <title>DepEd Zamboanga - School Directory</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50">
    <header style="background-color: #a52a2a;" class="p-4 shadow-lg text-center">
        <h1 class="text-white font-bold text-2xl uppercase">School Directory</h1>
    </header>

    <main class="max-w-4xl mx-auto p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($schools as $school)
                <div class="bg-white p-6 rounded-xl shadow-md border hover:border-red-500 transition">
                    <h3 class="text-xl font-bold text-slate-800">{{ $school->name }}</h3>
                    <p class="text-slate-500 text-sm mb-4">{{ $school->location ?? 'Zamboanga City' }}</p>
                    <a href="{{ route('public.view', ['school_id' => $school->id]) }}" 
                       class="text-red-700 font-bold hover:underline">
                        View Teacher Census →
                    </a>
                </div>
            @empty
                <p class="text-center text-slate-500">No schools registered yet.</p>
            @endforelse
        </div>
    </main>
</body>
</html>