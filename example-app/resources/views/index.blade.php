<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Census Management</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
        .btn { transition: all 0.2s ease-in-out; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

    <header style="background-color: #a52a2a;" class="fixed p-1 flex justify-center z-40 w-full items-center shadow-md">
        <img src="{{ asset('images/deped_zambo_header.png') }}" class="w-full max-w-4xl h-auto block"
            alt="DepEd Zamboanga Header">
    </header>

    <main class="max-w-6xl mx-auto p-4 md:p-8">
        
        {{-- Session Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm rounded-r">
                <p class="font-medium">Success: {{ session('success') }}</p>
            </div>
        @endif

        {{-- Combined Search & Filter Bar --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6 no-print">
            <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full pl-4 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" 
                           placeholder="Search position title...">
                </div>

                <div class="w-full md:w-48">
                    <select name="filter_stage" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                        <option value="">All Stages</option>
                        @foreach($rankings->pluck('career_stage')->unique() as $stage)
                            <option value="{{ $stage }}" {{ request('filter_stage') == $stage ? 'selected' : '' }}>
                                {{ $stage }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium cursor-pointer transition-colors">
                        Apply
                    </button>
                    <a href="{{ url()->current() }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg font-medium text-center transition-colors">
                        Reset
                    </a>
                    <button type="button" onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 cursor-pointer transition-colors">
                        Print
                    </button>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden mb-8">
            <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
                <h2 class="text-white font-bold text-lg tracking-wide uppercase">Teacher Ranking & School Census</h2>
                <span class="bg-red-600 text-white text-xs px-3 py-1 rounded-full font-bold">LIVE DATA</span>
            </div>
            
            <form action="{{ url()->current() }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-slate-600 font-semibold text-sm uppercase">Career Stage</th>
                                <th class="px-6 py-4 text-slate-600 font-semibold text-sm uppercase">Position Title</th>
                                <th class="px-6 py-4 text-slate-600 font-semibold text-sm uppercase text-center">SG</th>
                                <th class="px-6 py-4 text-slate-600 font-semibold text-sm uppercase text-center">Current Count</th>
                                <th class="px-6 py-4 text-slate-600 font-semibold text-sm uppercase text-right no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rankings as $rank)
                                {{-- Client-side Stage Filtering (Optional backup) --}}
                                @if(!request('filter_stage') || request('filter_stage') == $rank->career_stage)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 italic text-slate-500">{{ $rank->career_stage }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $rank->position_title }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-slate-100 text-slate-700 py-1 px-3 rounded text-sm font-mono">SG-{{ $rank->salary_grade }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <input type="number" name="counts[{{ $rank->id }}]" value="{{ $rank->teacher_count }}" 
                                               class="w-20 text-center border border-slate-300 rounded p-1 focus:ring-2 focus:ring-blue-500 outline-none">
                                    </td>
                                    <td class="px-6 py-4 text-right no-print">
                                        <a href="?delete={{ $rank->id }}" 
                                           onclick="return confirm('Are you sure?')"
                                           class="text-red-500 hover:text-red-700 font-medium text-sm underline decoration-dotted">Delete</a>
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-200">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-slate-700">Total Personnel Count:</td>
                                <td class="px-6 py-4 text-center text-xl text-blue-700">{{ number_format($totalTeachers) }}</td>
                                <td class="px-6 py-4 text-right no-print">
                                    <button type="submit" name="update_all" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow-md cursor-pointer active:scale-95 transition-transform">
                                        Update All
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>


{{-- Add School Section --}}
<div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden mb-8 no-print">
    <div class="bg-slate-100 px-6 py-3 border-b border-slate-200">
        <h3 class="text-slate-700 font-bold uppercase text-sm">Register New School</h3>
    </div>
    <div class="p-6">
        <form action="{{ route('schools.store') }}" method="POST" class="flex flex-col md:flex-row gap-4">
            @csrf
            <div class="flex-grow">
                <label class="block text-xs font-bold text-slate-500 mb-1 text-uppercase">School Name</label>
                <input type="text" name="name" placeholder="e.g. Zamboanga Central School" required 
                       class="w-full border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-red-500 outline-none">
            </div>
            <div class="flex-grow">
                <label class="block text-xs font-bold text-slate-500 mb-1 text-uppercase">Location/District</label>
                <input type="text" name="location" placeholder="e.g. City Central" 
                       class="w-full border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-red-500 outline-none">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white px-6 py-2 rounded-lg font-bold transition-colors cursor-pointer">
                    Add School
                </button>
            </div>
        </form>
    </div>
</div>
        
        {{-- Add Entry Section --}}
        <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden no-print">
            <div class="bg-slate-100 px-6 py-3 border-b border-slate-200">
                <h3 class="text-slate-700 font-bold uppercase text-sm">Add New Rank to Census</h3>
            </div>
            <div class="p-6">
                <form action="{{ url()->current() }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-500 mb-1">STAGE</label>
                        <input type="text" name="new_stage" placeholder="e.g. Proficient" required 
                               class="w-full border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-1">POSITION TITLE</label>
                        <input type="text" name="new_title" placeholder="e.g. Teacher II" required 
                               class="w-full border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">SG</label>
                        <input type="number" name="new_sg" placeholder="11" required 
                               class="w-full border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">INITIAL COUNT</label>
                        <div class="flex gap-2">
                            <input type="number" name="new_count" placeholder="0" required 
                                   class="w-full border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            <button type="submit" name="add_rank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold cursor-pointer transition-colors">
                                Add
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

     <footer class="bg-[#f2f2f2] text-gray-700 py-12 border-t border-gray-300">
        <div class="container mx-auto px-6 lg:px-20 flex flex-wrap md:flex-nowrap items-start gap-8">
            <div class="w-full md:w-1/6 flex justify-start">
                <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[200px] h-auto object-contain">
            </div>
            <div class="w-full md:w-1/4">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                <p class="text-[13px] leading-relaxed">All content is in the public domain unless otherwise stated.</p>
            </div>
            <div class="w-full md:w-1/5">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">About GOVPH</h2>
                <ul class="text-[13px] space-y-1">
                    <li><a href="https://www.gov.ph" class="hover:text-red-700 transition-colors">GOV.PH</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Open Data Portal</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Official Gazette</a></li>
                </ul>
            </div>
            <div class="w-full md:w-1/4">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Contact Us</h2>
                <div class="text-[13px] space-y-3">
                    <p><strong>Address:</strong><br>Pilar Street, Zamboanga City, 7000</p>
                    <p><strong>Email:</strong><br>zamboanga.city@deped.gov.ph</p>
                    <p><strong>Phone:</strong><br>(062) 991-1234</p>
                </div>
            </div>
            <div class="w-full md:w-1/6 flex justify-end">
                <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[200px] h-auto object-contain">
            </div>
        </div>
    </footer>

</body>
</html>