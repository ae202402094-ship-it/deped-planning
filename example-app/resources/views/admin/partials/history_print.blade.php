<div class="grid grid-cols-1 gap-1">
    @foreach($changes['before'] as $key => $oldValue)
        @php 
            $newValue = $changes['after'][$key] ?? $oldValue;
            $hasChanged = (string)$oldValue !== (string)$newValue;
        @endphp
        @if($hasChanged)
        <div class="flex justify-between items-center text-[10px] border-b border-slate-100 pb-1">
            <span class="font-black text-slate-400 uppercase text-[8px]">{{ str_replace('_', ' ', $key) }}</span>
            <span class="font-mono text-slate-600">
                <span class="line-through text-slate-300">{{ $oldValue }}</span> 
                <span class="text-red-800 font-bold ml-1">→ {{ $newValue }}</span>
            </span>
        </div>
        @endif
    @endforeach
</div>