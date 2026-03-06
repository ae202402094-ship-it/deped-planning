@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Admin Overview</h1>
        @if(isset($pendingUsers) && $pendingUsers->count() > 0)
            <span class="bg-red-100 text-red-700 px-4 py-1 rounded-full text-xs font-bold animate-pulse">
                {{ $pendingUsers->count() }} Pending Approvals
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Schools</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $schoolCount }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9.345 12.938 9.547 13.342L11.182 16.657L14.818 10.657C15.02 10.253 15.479 10.253 15.681 10.657L17.316 13.342M21 12C21 6.47715 16.5228 2 11 2C5.47715 2 1 6.47715 1 12C1 17.5228 5.47715 22 11 22C16.5228 22 21 17.5228 21 12Z" />
                </svg>
            </div>
            <a href="{{ route('admin.schools') }}" class="mt-4 block text-sm text-blue-600 hover:underline font-semibold">
                Manage Schools →
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden mb-8">
        <div class="h-1.5 bg-[#a52a2a] w-full"></div>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tight">Pending Registration Requests</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Users must be verified via email before appearing here</p>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Verification Queue</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="py-4 px-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">User Email</th>
                            <th class="py-4 px-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Requested Date</th>
                            <th class="py-4 px-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($pendingUsers as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-2 text-sm font-semibold text-slate-700">{{ $user->email }}</td>
                            <td class="py-4 px-2 text-xs text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-2 text-right space-x-2">
                                
                                <form action="{{ route('admin.approve', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded text-xs font-bold shadow-sm transition-all active:scale-95 cursor-pointer">
                                        Approve & Activate
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.reject', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject and delete this user? This action cannot be undone.');">
                                    @csrf
                                    <button type="submit" class="border border-slate-300 text-slate-500 hover:bg-slate-100 px-4 py-1.5 rounded text-xs font-bold transition-all active:scale-95 cursor-pointer">
                                        Reject
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($pendingUsers->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-slate-200 text-5xl mb-3 text-center">✓</div>
                        <p class="text-slate-400 text-sm italic">No pending registration requests found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-slate-100 p-8 rounded-2xl border-2 border-dashed border-slate-300 text-center">
        <h3 class="text-slate-600 font-medium mb-2">DepEd Zamboanga Planning Module</h3>
        <p class="text-slate-500 text-sm italic">System access is only granted after email verification and admin approval.</p>
    </div>
</div>
@endsection