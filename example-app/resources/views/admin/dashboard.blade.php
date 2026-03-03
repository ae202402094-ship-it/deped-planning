@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-slate-800 mb-8">Admin Overview</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Schools</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $schoolCount }}</p>
                </div>
                <div class="text-4xl text-blue-100"></div>
            </div>
            <a href="{{ route('admin.schools') }}" class="mt-4 block text-sm text-blue-600 hover:underline font-semibold">
                Manage Schools →
            </a>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-red-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Teachers Listed</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $teacherCount }}</p>
                </div>
                <div class="text-4xl text-red-100"></div>
            </div>
            <a href="{{ route('admin.teachers') }}" class="mt-4 block text-sm text-red-600 hover:underline font-semibold">
                View Census Data →
            </a>
        </div>
    </div>

    <div class="bg-slate-100 p-8 rounded-2xl border-2 border-dashed border-slate-300 text-center">
        <h3 class="text-slate-600 font-medium mb-2">Welcome to the DepEd Zamboanga Planning Module</h3>
        <p class="text-slate-500 text-sm">Use the sidebar to manage school information or update teacher rankings.</p>
    </div>
</div>
@endsection