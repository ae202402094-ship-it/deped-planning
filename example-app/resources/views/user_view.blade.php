<!DOCTYPE html>
<html>
<head>
    <title>Teacher Census - {{ $selectedSchool->name ?? 'All Schools' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100">
    <div class="max-w-5xl mx-auto p-6">
        <div class="mb-4">
            <a href="{{ route('public.schools') }}" class="text-slate-600 hover:text-red-700 font-bold">← Back to Schools</a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <div class="bg-slate-800 p-6 text-center">
                <h2 class="text-white text-2xl font-bold">{{ $selectedSchool->name ?? 'General Census' }}</h2>
                <p class="text-slate-400">Teacher Personnel Ranking Summary</p>
            </div>

            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Career Stage</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Position Title</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-center">SG</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-center">Count</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rankings as $rank)
                    <tr>
                        <td class="p-4 text-slate-600">{{ $rank->career_stage }}</td>
                        <td class="p-4 font-bold text-slate-800">{{ $rank->position_title }}</td>
                        <td class="p-4 text-center"><span class="bg-slate-100 px-2 py-1 rounded">SG-{{ $rank->salary_grade }}</span></td>
                        <td class="p-4 text-center font-bold">{{ $rank->teacher_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>