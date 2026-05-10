@props(['value', 'field', 'row'])

@php
    $oldValue = $row['old_values'][$field] ?? null;
    $hasChanged = ($row['status'] === 'update' && isset($oldValue) && (string)$oldValue !== (string)$value);
@endphp

<td class="p-4 border-r border-slate-100 text-center tabular-nums">
    <div class="flex flex-col items-center">
        <span class="font-bold {{ $hasChanged ? 'text-blue-700' : 'text-slate-700' }}">
            {{ $value }}
        </span>
        
        @if($hasChanged)
            <span class="text-[9px] text-slate-400 line-through leading-none mt-1">
                {{ $oldValue }}
            </span>
        @endif
    </div>
</td>