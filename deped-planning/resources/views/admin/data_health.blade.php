@extends(auth()->user()->role === 'super_admin' ? 'layouts.super_admin' : 'layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="mb-12 flex justify-between items-end border-b border-slate-100 pb-8">
        <div>
            <span class="text-[10px] font-black text-red-800 uppercase tracking-[0.4em] mb-2 block">Executive Oversight</span>
            <h2 class="text-4xl font-black text-slate-800 uppercase tracking-tighter italic">Data Health & Hazard Audit</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">Regional Risk Assessment Matrix</p>
        </div>
        <div class="text-right">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Flagged Institutions</span>
            <p class="text-2xl font-black text-slate-800 font-mono">{{ count($flaggedSchools) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-2xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                    <th class="p-8">Institution</th>
                    <th class="p-8">Physical Hazards</th>
                    <th class="p-8">Resource Discrepancies</th>
                    <th class="p-8 text-center">Protocol</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($flaggedSchools as $data)
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="p-8">
                            <span class="text-[10px] font-bold text-slate-400 block mb-1">ID: {{ $data['school']->school_id }}</span>
                            <p class="font-black text-slate-800 uppercase tracking-tight text-lg group-hover:text-red-800 transition-colors">
                                {{ $data['school']->name }}
                            </p>
                        </td>

                        {{-- Column 2: Physical Hazard Status --}}
                        <td class="p-8">
                            @if($data['school']->hazard_type && $data['school']->hazard_type !== 'None')
                                <div class="inline-flex flex-col gap-2">
                                    <span class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-700">
                                        <span class="w-2 h-2 rounded-full {{ $data['school']->hazard_level === 'High' ? 'bg-red-600 animate-pulse' : 'bg-amber-500' }}"></span>
                                        Type: {{ $data['school']->hazard_type }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter w-fit
                                        {{ $data['school']->hazard_level === 'High' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                        Severity: {{ $data['school']->hazard_level }}
                                    </span>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-slate-300 uppercase italic tracking-widest">No Physical Hazard</span>
                            @endif
                        </td>

                        {{-- Column 3: Resource Ratio Issues --}}
                        <td class="p-8">
                            <div class="space-y-2">
                                @forelse($data['issues'] as $issue)
                                    <div class="flex items-start gap-2 text-[10px] font-bold text-slate-500 uppercase leading-tight">
                                        <span class="text-red-500">⚠</span>
                                        {{ $issue }}
                                    </div>
                                @empty
                                    <span class="text-[10px] font-black text-slate-300 uppercase italic tracking-widest">Resources Stable</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- Column 4: Action --}}
                        <td class="p-8 text-center">
                            <a href="{{ route('schools.edit', $data['school']->id) }}" class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 text-slate-800 hover:bg-red-800 hover:text-white transition-all shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-32 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-2xl font-bold">✓</div>
                                <p class="text-xs text-slate-400 uppercase font-black tracking-[0.3em]">All Institutions Clear of Hazards</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection