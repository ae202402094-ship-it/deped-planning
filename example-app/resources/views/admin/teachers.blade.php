@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6 no-print">
    <form action="{{ route('admin.teachers') }}" method="GET" class="flex items-center gap-4">
        <label class="text-slate-600 font-bold text-sm uppercase tracking-wide">Viewing School:</label>
        <select name="school_id" onchange="this.form.submit()" 
                class="flex-grow md:flex-grow-0 md:w-64 border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 cursor-pointer">
            <option value="">-- All Schools --</option>
            @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ isset($selectedSchoolId) && $selectedSchoolId == $school->id ? 'selected' : '' }}>
                    {{ $school->name }}
                </option>
            @endforeach
        </select>
        @if(isset($selectedSchoolId) && $selectedSchoolId)
            <a href="{{ route('admin.teachers') }}" class="text-xs text-red-600 hover:underline font-bold uppercase">Clear Filter</a>
        @endif
    </form>
</div>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight">Teacher Census Management</h2>
    <div class="bg-blue-600 text-white px-4 py-1 rounded-full font-bold text-sm shadow-sm">
        Total: {{ number_format($totalTeachers) }}
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 border border-green-200 animate-pulse">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white p-6 rounded-xl shadow-md border border-slate-200 mb-8">
    <h3 class="text-xs font-bold text-slate-400 uppercase mb-4 tracking-widest">Add New Ranking Entry</h3>
    <form action="{{ route('admin.teachers') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        @csrf
        <select name="school_id" required class="border border-slate-300 p-2 rounded text-sm outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Select School</option>
            @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ isset($selectedSchoolId) && $selectedSchoolId == $school->id ? 'selected' : '' }}>
                    {{ $school->name }}
                </option>
            @endforeach
        </select>
        <select name="new_stage" required class="border border-slate-300 p-2 rounded text-sm outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Stage</option>
            <option value="Elementary">Elementary</option>
            <option value="Junior High">Junior High</option>
            <option value="Senior High">Senior High</option>
        </select>
        <input type="text" name="new_title" placeholder="Position Title" required class="border border-slate-300 p-2 rounded text-sm outline-none focus:ring-2 focus:ring-green-500">
        <input type="number" name="new_sg" placeholder="SG" required class="border border-slate-300 p-2 rounded text-sm outline-none focus:ring-2 focus:ring-green-500">
        <input type="number" name="new_count" placeholder="Count" required class="border border-slate-300 p-2 rounded text-sm outline-none focus:ring-2 focus:ring-green-500">
        <button type="submit" name="add_rank" class="bg-green-600 text-white rounded font-bold text-sm hover:bg-green-700 transition-colors shadow-sm">
            Add Rank
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
    <form action="{{ route('admin.teachers') }}" method="POST">
        @csrf
        <div class="bg-slate-800 p-4 flex justify-between items-center">
            <h3 class="text-white text-xs font-bold uppercase tracking-widest">Active Rankings List</h3>
            <button type="submit" name="update_all" class="bg-blue-500 text-white text-xs px-4 py-2 rounded font-bold hover:bg-blue-600 transition shadow-sm uppercase">
                Save Bulk Changes
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">School</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Position</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-center">SG</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-center w-32">Count</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rankings as $rank)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm font-semibold text-slate-600">
                            {{ $rank->school->name ?? 'Unassigned' }}
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-slate-900">{{ $rank->position_title }}</div>
                            <div class="text-xs text-slate-400 italic">{{ $rank->career_stage }}</div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-slate-100 px-2 py-1 rounded text-xs font-mono text-slate-600 border border-slate-200">SG-{{ $rank->salary_grade }}</span>
                        </td>
                        <td class="p-4">
                            <input type="number" name="counts[{{ $rank->id }}]" value="{{ $rank->teacher_count }}" 
                                   class="w-full border border-slate-200 text-center rounded py-1 focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none transition-all">
                        </td>
                        <td class="p-4 text-center">
                            <button type="submit" name="delete" value="{{ $rank->id }}" 
                                    onclick="return confirm('Delete this record permanently?')"
                                    class="text-red-500 hover:text-red-700 text-xs font-bold uppercase transition-colors">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-400 italic">
                            No teacher rankings found for this selection.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection