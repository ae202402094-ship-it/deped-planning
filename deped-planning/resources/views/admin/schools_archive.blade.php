@extends(auth()->user()->role === 'super_admin' ? 'layouts.super_admin' : 'layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Decommissioned Registry</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest italic">Archived Institutional Records</p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- Wipe All Button --}}
            @if($archivedSchools->count() > 0)
                <button type="button" onclick="openWipeModal()" class="bg-white border-2 border-rose-600 text-rose-600 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-50 transition-all flex items-center gap-2">
                    <i class="bi bi-radioactive"></i> Delete All Archives
                </button>
            @endif

            {{-- Mass Action Button (Hidden until a checkbox is clicked) --}}
            <div id="mass-action-container" class="hidden animate-in fade-in slide-in-from-right-4">
                <button type="button" onclick="openMassDeleteModal()" class="bg-rose-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg flex items-center gap-2">
                    <i class="bi bi-trash3-fill"></i> Purge Selected (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>
    </div>

    {{-- Main Mass Action Form --}}
    <form id="mass-delete-form" action="{{ route('superadmin.force_delete_batch') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="scope" id="form-scope" value="selected">
        
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="p-5 w-10">
                            {{-- Select All Checkbox --}}
                            <label class="relative flex items-center cursor-pointer">
                                <input type="checkbox" id="select-all" class="peer sr-only">
                                <div class="w-5 h-5 border-2 border-slate-300 rounded-full peer-checked:bg-slate-800 peer-checked:border-slate-800 transition-all flex items-center justify-center">
                                    <i class="bi bi-check text-white hidden peer-checked:block text-lg"></i>
                                </div>
                            </label>
                        </th>
                        <th class="p-5">School ID</th>
                        <th class="p-5">Institution Name</th>
                        <th class="p-5 text-center">Decommissioned On</th>
                        <th class="p-5 text-center">Administrative Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($archivedSchools as $school)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-5">
                                {{-- Circular Row Checkbox --}}
                                <label class="relative flex items-center cursor-pointer">
                                    <input type="checkbox" name="ids[]" value="{{ $school->id }}" data-name="{{ $school->name }}" class="school-checkbox peer sr-only">
                                    <div class="w-5 h-5 border-2 border-slate-300 rounded-full peer-checked:bg-rose-600 peer-checked:border-rose-600 transition-all flex items-center justify-center">
                                        <i class="bi bi-check text-white hidden peer-checked:block text-lg"></i>
                                    </div>
                                </label>
                            </td>
                            <td class="p-5 font-mono font-bold text-slate-500">{{ $school->school_id }}</td>
                            <td class="p-5 font-black text-slate-800 uppercase">{{ $school->name }}</td>
                            <td class="p-5 text-center font-bold text-slate-600">{{ $school->deleted_at->format('M d, Y') }}</td>
                            <td class="p-5 text-center">
                                <button type="button" onclick="openModal('restore', '{{ $school->name }}', '{{ route('schools.restore', $school->id) }}')" class="text-[10px] font-black uppercase text-slate-800 hover:text-emerald-600 mr-4">Restore</button>
                                <button type="button" onclick="openModal('purge', '{{ $school->name }}', '{{ route('schools.force_delete', $school->id) }}')" class="text-[10px] font-black uppercase text-rose-700 hover:text-black">Purge</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-20 text-center uppercase font-black text-slate-400">Archive is Empty</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    
    <div class="mt-6">
        {{ $archivedSchools->links() }}
    </div>
</div>

{{-- Confirmation Modal --}}
<div id="confirmation-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden transform transition-all duration-300">
        <div class="p-10 text-center">
            <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-8 border-4">
                <i id="modal-icon" class="text-3xl"></i>
            </div>
            <h3 id="modal-title" class="text-2xl font-black text-slate-800 uppercase mb-3">Confirm Action</h3>
            <div id="modal-message" class="text-xs text-slate-500 font-bold uppercase tracking-widest leading-loose"></div>
        </div>
        <div class="flex border-t">
            <button type="button" onclick="closeModal()" class="flex-1 p-6 text-[10px] font-black uppercase text-slate-400 border-r hover:bg-slate-50">Cancel</button>
            <button id="confirm-button" class="flex-1 p-6 text-[10px] font-black uppercase text-white">Proceed</button>
        </div>
    </div>
</div>

{{-- Hidden Form for Individual Restore/Purge --}}
<form id="modal-form" method="POST" class="hidden">@csrf <div id="method-container"></div></form>

<script>
    const modal = document.getElementById('confirmation-modal');
    const massDeleteForm = document.getElementById('mass-delete-form');
    const modalForm = document.getElementById('modal-form');
    const methodContainer = document.getElementById('method-container');
    const formScope = document.getElementById('form-scope');
    const confirmBtn = document.getElementById('confirm-button');
    
    // UI Elements
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.school-checkbox');
    const massActionContainer = document.getElementById('mass-action-container');
    const selectedCountSpan = document.getElementById('selected-count');

    // Handle Selection Logic
    function updateUI() {
        const checkedCount = document.querySelectorAll('.school-checkbox:checked').length;
        selectedCountSpan.innerText = checkedCount;
        massActionContainer.classList.toggle('hidden', checkedCount === 0);
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateUI();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateUI));

    // Function for the "Delete All Archives" button
function openWipeModal() {
    // CRITICAL: This line tells the controller to ignore 'ids' and delete everything
    document.getElementById('form-scope').value = "all"; 
    
    document.getElementById('modal-title').innerText = "Confirm Total Wipe";
    document.getElementById('modal-message').innerHTML = "This will permanently delete <span class='text-rose-600 font-bold'>ALL archived schools</span>. This cannot be undone.";
    
    const confirmBtn = document.getElementById('confirm-button');
    confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-rose-700 text-white";
    
    confirmBtn.onclick = function() {
        document.getElementById('mass-delete-form').submit();
    };
    document.getElementById('confirmation-modal').classList.remove('hidden');
}

// Function for the "Purge Selected" button
function openMassDeleteModal() {
    // CRITICAL: This tells the controller to only use the checked IDs
    document.getElementById('form-scope').value = "selected"; 

    const checked = document.querySelectorAll('.school-checkbox:checked');
    document.getElementById('modal-title').innerText = "Purge Selected";
    document.getElementById('modal-message').innerText = `Permanently delete ${checked.length} selected records?`;
    
    const confirmBtn = document.getElementById('confirm-button');
    confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-rose-600 text-white";

    confirmBtn.onclick = function() {
        document.getElementById('mass-delete-form').submit();
    };
    document.getElementById('confirmation-modal').classList.remove('hidden');
}

    // Individual Action
    function openModal(type, name, url) {
        modalForm.action = url;
        if (type === 'purge') {
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
            confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-rose-600 text-white";
        } else {
            methodContainer.innerHTML = '';
            confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-slate-900 text-white";
        }
        confirmBtn.onclick = () => modalForm.submit();
        modal.classList.remove('hidden');
    }

    function closeModal() { modal.classList.add('hidden'); }
</script>
@endsection