@extends(auth()->user()->role === 'super_admin' ? 'layouts.super_admin' : 'layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Decommissioned Registry</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest italic">Archived Institutional Records</p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- Wipe All Button --}}
            @if($archivedSchools->count() > 0)
                <button type="button" onclick="openWipeModal()" class="bg-white border-2 border-rose-600 text-rose-600 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-50 transition-all flex items-center gap-2">
                    <i class="bi bi-radioactive"></i> Wipe All Archive
                </button>
            @endif

            {{-- Mass Action Button (Hidden until selection) --}}
            <div id="mass-action-container" class="hidden animate-in fade-in slide-in-from-right-4">
                <button type="button" onclick="openMassDeleteModal()" class="bg-rose-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg flex items-center gap-2">
                    <i class="bi bi-trash3-fill"></i> Purge Selected (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>
    </div>

    <form id="mass-delete-form" action="{{ route('superadmin.force_delete_batch') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="scope" id="form-scope" value="selected">
        
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="p-5 w-10">
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
                        <th class="p-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($archivedSchools as $school)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-5">
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
                                <button type="button" onclick="openModal('restore', '{{ $school->name }}', '{{ route('schools.restore', $school->id) }}')" class="text-[10px] font-black text-slate-800 uppercase tracking-widest hover:text-emerald-600 mr-4 transition-all">Restore</button>
                                <button type="button" onclick="openModal('purge', '{{ $school->name }}', '{{ route('schools.force_delete', $school->id) }}')" class="text-[10px] font-black text-rose-700 uppercase tracking-widest hover:text-black transition-all">Purge</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-20 text-center text-slate-400 uppercase font-black text-xs">Archive Empty</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>

{{-- Confirmation Modal --}}
<div id="confirmation-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden">
        <div class="p-10 text-center">
            <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-8 border-4">
                <i id="modal-icon" class="text-3xl"></i>
            </div>
            <h3 id="modal-title" class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-3">Confirm</h3>
            <div id="modal-message" class="text-xs text-slate-500 font-bold uppercase tracking-widest leading-loose max-h-40 overflow-y-auto"></div>
        </div>
        <div class="flex border-t">
            <button type="button" onclick="closeModal()" class="flex-1 p-6 text-[10px] font-black text-slate-400 uppercase border-r hover:bg-slate-50">Cancel</button>
            <button id="confirm-button" class="flex-1 p-6 text-[10px] font-black uppercase text-white">Proceed</button>
        </div>
    </div>
</div>

{{-- Single Action Form --}}
<form id="modal-form" method="POST" class="hidden">@csrf <div id="method-container"></div></form>

<script>
    const modal = document.getElementById('confirmation-modal');
    const massDeleteForm = document.getElementById('mass-delete-form');
    const formScope = document.getElementById('form-scope');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalIcon = document.getElementById('modal-icon');
    const modalIconContainer = document.getElementById('modal-icon-container');
    const confirmBtn = document.getElementById('confirm-button');
    
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.school-checkbox');
    const massActionContainer = document.getElementById('mass-action-container');
    const selectedCountSpan = document.getElementById('selected-count');

    // Handle Selection UI
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

    // Nuclear Wipe
    function openWipeModal() {
        formScope.value = "all";
        modalTitle.innerText = "Wipe Archive";
        modalMessage.innerHTML = "Wipe <span class='text-rose-600 font-black'>ALL RECORDS</span>? This is irreversible.";
        modalIcon.className = "bi bi-radioactive";
        modalIconContainer.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-8 bg-rose-100 text-rose-700 border-rose-200";
        confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-rose-700 text-white";
        confirmBtn.onclick = () => massDeleteForm.submit();
        modal.classList.remove('hidden');
    }

    // Batch Selected
    function openMassDeleteModal() {
        formScope.value = "selected";
        const checked = document.querySelectorAll('.school-checkbox:checked');
        const names = Array.from(checked).map(cb => cb.getAttribute('data-name')).join(', ');
        modalTitle.innerText = "Batch Purge";
        modalMessage.innerHTML = `Purge: <span class="text-rose-600">${names}</span>?`;
        modalIcon.className = "bi bi-trash3-fill";
        modalIconContainer.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-8 bg-rose-50 text-rose-600 border-rose-100";
        confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-rose-600 text-white";
        confirmBtn.onclick = () => massDeleteForm.submit();
        modal.classList.remove('hidden');
    }

    // Individual Action
    function openModal(type, name, url) {
        const singleForm = document.getElementById('modal-form');
        const methodContainer = document.getElementById('method-container');
        singleForm.action = url;
        
        if (type === 'purge') {
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
            modalTitle.innerText = "Purge Record";
            modalMessage.innerText = `Delete ${name} permanently?`;
            modalIcon.className = "bi bi-trash3-fill";
            modalIconContainer.className = "mx-auto h-20 w-20 rounded-full mb-8 bg-rose-50 text-rose-600 border-rose-100 flex items-center justify-center";
            confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-rose-600 text-white";
        } else {
            methodContainer.innerHTML = '';
            modalTitle.innerText = "Restore Record";
            modalMessage.innerText = `Restore ${name}?`;
            modalIcon.className = "bi bi-arrow-counterclockwise";
            modalIconContainer.className = "mx-auto h-20 w-20 rounded-full mb-8 bg-emerald-50 text-emerald-600 border-emerald-100 flex items-center justify-center";
            confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase bg-slate-900 text-white";
        }
        confirmBtn.onclick = () => singleForm.submit();
        modal.classList.remove('hidden');
    }

    function closeModal() { modal.classList.add('hidden'); }
</script>
@endsection