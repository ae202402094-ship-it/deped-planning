@extends(auth()->user()->role === 'super_admin' ? 'layouts.super_admin' : 'layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Decommissioned Registry</h2>
        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest italic">Archived Institutional Records</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-6 d-flex align-items-center bg-white rounded-2xl p-4 border-l-4 border-emerald-500">
            <i class="bi bi-check-circle-fill text-emerald-500 me-3 fs-5"></i>
            <div class="text-slate-700 font-bold uppercase text-xs tracking-wider">{{ session('success') }}</div>
        </div>
    @endif

    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="p-5">School ID</th>
                    <th class="p-5">Institution Name</th>
                    <th class="p-5 text-center">Decommissioned On</th>
                    <th class="p-5 text-center">Administrative Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($archivedSchools as $school)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="p-5 font-mono font-bold text-slate-500">
                            <span class="bg-slate-100 px-2 py-1 rounded">{{ $school->school_id }}</span>
                        </td>
                        <td class="p-5 font-black text-slate-800 uppercase">
                            {{ $school->name }}
                            <div class="text-[9px] text-slate-400 font-bold tracking-widest mt-1">STATUS: ARCHIVED</div>
                        </td>
                        <td class="p-5 text-center">
                            <div class="font-bold text-slate-600">{{ $school->deleted_at->format('M d, Y') }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $school->deleted_at->format('h:i A') }}</div>
                        </td>
                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-6">
                                {{-- Restore Trigger --}}
                                <button type="button" 
                                    onclick="openModal('restore', '{{ $school->name }}', '{{ route('superadmin.restore_school', $school->id) }}')"
                                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest hover:text-emerald-600 transition-all flex items-center">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                </button>

                                {{-- Purge Trigger --}}
                                <button type="button" 
                                    onclick="openModal('purge', '{{ $school->name }}', '{{ route('superadmin.force_delete_school', $school->id) }}')"
                                    class="text-[10px] font-black text-rose-700 uppercase tracking-widest hover:text-black transition-all flex items-center">
                                    <i class="bi bi-trash3-fill me-1"></i> Purge
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-24 text-center">
                            <i class="bi bi-archive text-slate-200 text-6xl d-block mb-4"></i>
                            <span class="text-slate-400 uppercase font-black tracking-widest text-xs">Registry Archive is Empty</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Submission Form --}}
<form id="modal-form" method="POST" class="hidden">
    @csrf
    <div id="method-container"></div>
</form>

{{-- Modern Confirmation Modal --}}
<div id="confirmation-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-in zoom-in duration-300">
        <div class="p-10 text-center">
            <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-8 transition-colors border-4">
                <i id="modal-icon" class="text-3xl"></i>
            </div>
            
            <h3 id="modal-title" class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-3">Confirm Action</h3>
            <p id="modal-message" class="text-xs text-slate-500 font-bold uppercase tracking-widest leading-loose px-4"></p>
        </div>

        <div class="flex border-t border-slate-100 bg-slate-50/50">
            <button type="button" onclick="closeModal()" class="flex-1 p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-white transition-colors border-r border-slate-100">
                Cancel
            </button>
            <button id="confirm-button" class="flex items-center justify-center flex-1 p-6 text-[10px] font-black uppercase tracking-widest transition-all">
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
    const btnText = document.getElementById('btn-text');

    function openModal(type, name, url) {
        modalForm.action = url;
        methodContainer.innerHTML = ''; 

        if (type === 'purge') {
            modalTitle.innerText = "Critical Security Warning";
            modalMessage.innerText = `You are about to permanently erase ${name} from the master database. This operation is irreversible.`;
            modalIcon.className = "bi bi-exclamation-triangle-fill";
            modalIconContainer.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-8 bg-rose-50 text-rose-600 border-rose-100";
            confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase tracking-widest bg-rose-600 text-white hover:bg-rose-700 transition-colors shadow-inner";
            btnText.innerText = "Purge Permanently";
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
        } else {
            modalTitle.innerText = "Registry Restoration";
            modalMessage.innerText = `Confirm the restoration of ${name} to the active school directory?`;
            modalIcon.className = "bi bi-arrow-counterclockwise";
            modalIconContainer.className = "mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-8 bg-emerald-50 text-emerald-600 border-emerald-100";
            confirmBtn.className = "flex-1 p-6 text-[10px] font-black uppercase tracking-widest bg-slate-900 text-white hover:bg-black transition-colors shadow-inner";
            btnText.innerText = "Authorize Restoration";
        }

        confirmBtn.onclick = function() {
            btnText.innerText = "Processing Security Handshake...";
            confirmBtn.disabled = true;
            confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
            modalForm.submit();
        };

        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    // Close modal on background click
    window.onclick = function(event) {
        if (event.target == modal) closeModal();
    }
</script>
@endsection