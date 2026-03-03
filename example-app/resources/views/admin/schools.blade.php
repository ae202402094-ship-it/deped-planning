@extends('layouts.admin')

@section('content')
    <h2 class="text-2xl font-bold mb-6">School Management</h2>

    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <form action="{{ route('schools.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <input type="text" name="name" placeholder="School Name" required class="border p-2 rounded">
            <input type="text" name="location" placeholder="Location" class="border p-2 rounded">
            <button type="submit" class="bg-blue-600 text-white rounded font-bold">Add School</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-100">
                <tr><th class="p-4">Name</th><th class="p-4">Location</th></tr>
            </thead>
            <tbody>
                @foreach($schools as $school)
                    <tr class="border-t">
                        <td class="p-4">{{ $school->name }}</td>
                        <td class="p-4">{{ $school->location }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection