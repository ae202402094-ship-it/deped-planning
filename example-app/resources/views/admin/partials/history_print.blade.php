<div class="grid grid-cols-1 gap-1">
    @if(isset($changes['before']) && isset($changes['after']))
        {{-- Handle Comparison View (Update Actions) --}}
        @foreach($changes['before'] as $key => $oldValue)
            @php 
                $newValue = $changes['after'][$key] ?? $oldValue;
                $hasChanged = (string)$oldValue !== (string)$newValue;
            @endphp
            @if($hasChanged)
            <div class="flex justify-between items-center text-[10px] border-b border-slate-100 pb-1">
                <span class="font-black text-slate-400 uppercase text-[8px]">{{ str_replace('_', ' ', $key) }}</span>
                <span class="font-mono text-slate-600">
                    <span class="line-through text-slate-300">{{ is_array($oldValue) ? json_encode($oldValue) : ($oldValue ?: 'Ø') }}</span> 
                    <span class="text-red-800 font-bold ml-1">→ {{ is_array($newValue) ? json_encode($newValue) : ($newValue ?: 'Ø') }}</span>
                </span>
            </div>
            @endif
        @endforeach
    @elseif(is_array($changes) || is_object($changes))
        {{-- Handle Flat View (Create, Delete, or Login Actions) --}}
        @foreach($changes as $key => $value)
            @if(!is_array($value))
            <div class="flex justify-between items-center text-[10px] border-b border-slate-100 pb-1">
                <span class="font-black text-slate-400 uppercase text-[8px]">{{ str_replace('_', ' ', $key) }}</span>
                <span class="font-mono text-slate-800">{{ $value ?: 'Ø' }}</span>
            </div>
            @endif
        @endforeach
    @else
        <div class="text-[10px] italic text-slate-400">No specific data changes recorded.</div>
    @endif
</div>