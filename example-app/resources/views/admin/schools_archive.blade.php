@extends(auth()->user()->role === 'super_admin' ? 'layouts.super_admin' : 'layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Decommissioned Registry</h2>
        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest italic">Archived Institutional Records</p>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="p-5">School ID</th>
                    <th class="p-5">Name</th>
                    <th class="p-5 text-center">Decommissioned On</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($schools as $school)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="p-5 font-mono font-bold text-slate-500">{{ $school->school_id }}</td>
                        <td class="p-5 font-black text-slate-800 uppercase">{{ $school->name }}</td>
                        <td class="p-5 text-center font-bold text-slate-500">
                            {{ $school->deleted_at->format('M d, Y') }}
                        </td>
                        <td class="p-5 text-center">
                            {{-- We will add Restore/Force Delete logic next --}}
                           {{-- resources/views/admin/schools_archive.blade.php --}}
<td class="p-5 text-center">
    <div class="flex items-center justify-center gap-4">
        {{-- Restore Form --}}
        <form action="{{ route('schools.restore', $school->id) }}" method="POST">
            @csrf
            <button type="submit" class="text-[10px] font-black text-slate-800 uppercase tracking-widest hover:text-red-800 transition-colors">
                Restore ↺
            </button>
        </form>

        {{-- Permanent Delete Form --}}
        <form action="{{ route('schools.force_delete', $school->id) }}" method="POST" onsubmit="return confirm('CRITICAL WARNING: This action is irreversible. Purge {{ $school->name }} permanently?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-[10px] font-black text-red-800 uppercase tracking-widest hover:text-black transition-colors">
                Purge ✖
            </button>
        </form>
    </div>
</td>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-slate-400 uppercase font-black tracking-widest text-xs">
                            Archive is Empty
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection