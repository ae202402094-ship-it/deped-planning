@extends('layouts.admin')

@section('content')
    <h2 class="text-2xl font-bold mb-6">School Management</h2>

    <div class="bg-white rounded-xl shadow-md p-6 mb-8 border-t-4" style="border-color: #a52a2a;">
    <h3 class="text-sm font-bold text-slate-400 uppercase mb-4 tracking-widest text-center">Register New School Profile</h3>
    <form action="{{ route('schools.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @csrf
        <input type="text" name="school_id" placeholder="School ID" required class="border p-2 rounded text-sm">
        <input type="text" name="name" placeholder="School Name" required class="border p-2 rounded text-sm">
        <input type="number" name="no_of_teachers" placeholder="Teachers" required class="border p-2 rounded text-sm" min="0">
        <input type="number" name="no_of_enrollees" placeholder="Enrollees" required class="border p-2 rounded text-sm" min="0">
        <input type="number" name="no_of_classrooms" placeholder="Classrooms" required class="border p-2 rounded text-sm" min="0">
        <input type="number" name="no_of_toilets" placeholder="Toilets" required class="border p-2 rounded text-sm" min="0">
        <button type="submit" class="bg-blue-600 text-white rounded font-bold uppercase text-xs hover:bg-blue-700 transition lg:col-span-full py-3 mt-2">
            Add School to Registry
        </button>
    </form>
</div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full text-left border-collapse">
    <thead>
        <tr style="background-color: #a52a2a;" class="text-white uppercase text-xs">
            <th class="p-4">School Name</th>
            <th class="p-4 text-center">Teachers</th>
            <th class="p-4 text-center">Enrollees</th>
            <th class="p-4 text-center">Classrooms</th>
            <th class="p-4 text-center">Toilets</th>
            <th class="p-4 text-center">Action</th>
        </tr>
    </thead>
    <tbody class="divide-y">
        @foreach($schools as $school)
        <tr>
            <form action="{{ route('schools.update', $school->id) }}" method="POST">
                @csrf
                @method('PUT')
                <td class="p-4 font-bold">{{ $school->name }}</td>
                <td class="p-4"><input type="number" name="no_of_teachers" value="{{ $school->no_of_teachers }}" class="w-20 border rounded text-center"></td>
                <td class="p-4"><input type="number" name="no_of_enrollees" value="{{ $school->no_of_enrollees }}" class="w-20 border rounded text-center"></td>
                <td class="p-4"><input type="number" name="no_of_classrooms" value="{{ $school->no_of_classrooms }}" class="w-20 border rounded text-center"></td>
                <td class="p-4"><input type="number" name="no_of_toilets" value="{{ $school->no_of_toilets }}" class="w-20 border rounded text-center"></td>
                <td class="p-4 text-center">
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold uppercase hover:bg-green-700">Update</button>
                </td>
            </form>
        </tr>
        @endforeach
    </tbody>
</table>
    </div>
@endsection