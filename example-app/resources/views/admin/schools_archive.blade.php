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
                            <div class="flex items-center justify-center gap-4">
                                {{-- Restore Button --}}
                                <button type="button" 
                                    onclick="openModal('restore', '{{ $school->name }}', '{{ route('schools.restore', $school->id) }}')"
                                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest hover:text-blue-600 transition-colors">
                                    Restore ↺
                                </button>

                                {{-- Purge Button --}}
                                <button type="button" 
                                    onclick="openModal('purge', '{{ $school->name }}', '{{ route('schools.force_delete', $school->id) }}')"
                                    class="text-[10px] font-black text-red-800 uppercase tracking-widest hover:text-black transition-colors">
                                    Purge ✖
                                </button>
                            </div>
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

{{-- Single hidden form for modal submission --}}
<form id="modal-form" method="POST" class="hidden">
    @csrf
    <div id="method-container"></div>
</form>

{{-- The Confirmation Modal --}}
<div id="confirmation-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="p-8 text-center">
            <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6 transition-colors">
                <span id="modal-icon" class="text-2xl font-bold"></span>
            </div>
            
            <h3 id="modal-title" class="text-xl font-black text-slate-800 uppercase tracking-tight mb-2">Confirm Action</h3>
            <p id="modal-message" class="text-sm text-slate-500 font-bold uppercase tracking-wide leading-relaxed px-4"></p>
        </div>

        <div class="flex border-t border-slate-100">
            <button type="button" onclick="closeModal()" class="flex-1 p-5 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 transition-colors border-r border-slate-100">
                Cancel
            </button>
            <button id="confirm-button" class="flex items-center justify-center flex-1 p-5 text-[10px] font-black uppercase tracking-widest transition-all">
                <svg id="btn-spinner" class="hidden animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="btn-text">Proceed</span>
            </button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('confirmation-modal');
    const modalForm = document.getElementById('modal-form');
    const methodContainer = document.getElementById('method-container');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalIcon = document.getElementById('modal-icon');
    const modalIconContainer = document.getElementById('modal-icon-container');
    const confirmBtn = document.getElementById('confirm-button');
    const btnSpinner = document.getElementById('btn-spinner');
    const btnText = document.getElementById('btn-text');

    function openModal(type, name, url) {
        resetBtnState();
        modalForm.action = url;
        methodContainer.innerHTML = ''; 

        if (type === 'purge') {
            modalTitle.innerText = "Critical Warning";
            modalMessage.innerText = `Are you sure you want to permanently purge ${name}? This action is irreversible.`;
            modalIcon.innerText = "✖";
            modalIconContainer.className = "mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6 bg-red-100 text-red-600";
            confirmBtn.className = "flex-1 p-5 text-[10px] font-black uppercase tracking-widest bg-red-600 text-white hover:bg-red-700 transition-colors flex items-center justify-center";
            btnText.innerText = "Purge Permanently";
            
            // Manual method spoofing for DELETE
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
        } else {
            modalTitle.innerText = "Restore Record";
            modalMessage.innerText = `Confirm restoration of ${name} to the active registry?`;
            modalIcon.innerText = "↺";
            modalIconContainer.className = "mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6 bg-blue-100 text-blue-600";
            confirmBtn.className = "flex-1 p-5 text-[10px] font-black uppercase tracking-widest bg-slate-800 text-white hover:bg-black transition-colors flex items-center justify-center";
            btnText.innerText = "Confirm Restore";
        }

        confirmBtn.onclick = function() {
            setBtnLoading();
            modalForm.submit();
        };

        modal.classList.remove('hidden');
    }

    function setBtnLoading() {
        confirmBtn.disabled = true;
        confirmBtn.classList.add('opacity-80', 'cursor-not-allowed');
        btnSpinner.classList.remove('hidden');
        btnText.innerText = "Processing...";
    }

    function resetBtnState() {
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('opacity-80', 'cursor-not-allowed');
        btnSpinner.classList.add('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    window.onclick = function(event) {
        if (event.target == modal) closeModal();
    }
</script>
@endsection