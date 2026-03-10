@props(['value', 'field', 'row', 'index'])

@php
    $oldValue = $row['old_values'][$field] ?? null;
    $hasChanged = ($row['status'] === 'update' && isset($oldValue) && (string)$oldValue !== (string)$value);
@endphp

<td class="p-4 border-r border-slate-100 text-center tabular-nums group">
    <div class="flex flex-col items-center">
        {{-- Display Mode --}}
        <div id="display-{{ $field }}-{{ $index }}" 
             class="cursor-pointer hover:bg-blue-50 px-2 py-1 rounded border border-transparent hover:border-blue-200 transition-all flex items-center gap-1"
             onclick="toggleEdit('{{ $field }}', {{ $index }})">
            
            <span class="font-bold {{ $hasChanged ? 'text-blue-700' : 'text-slate-700' }}">
                {{ $value }}
            </span>
            <span class="text-[8px] text-blue-400 opacity-0 group-hover:opacity-100">✎</span>
        </div>

        {{-- Edit Mode --}}
        <input 
            type="text" 
            id="input-{{ $field }}-{{ $index }}" 
            value="{{ $value }}" 
            class="hidden w-20 text-center text-xs font-bold border-2 border-blue-500 rounded p-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
            onblur="saveEdit('{{ $field }}', {{ $index }})"
            onkeydown="if(event.key==='Enter') { this.blur(); }"
        >
        
        @if($hasChanged)
            <span class="text-[9px] text-slate-400 line-through leading-none mt-1">
                {{ $oldValue }}
            </span>
        @endif
    </div>
</td>