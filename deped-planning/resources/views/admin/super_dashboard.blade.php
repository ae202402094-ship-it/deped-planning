@extends('layouts.super_admin') 

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Super Admin Dashboard</h2>
    <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i> {{ now()->format('F j, Y') }}</span>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

{{-- Notification Area for Security Actions --}}
@php $pwRequests = $users->whereNotNull('pending_password'); @endphp
@if($pwRequests->count() > 0)
    <div class="mb-4">
        <div class="px-3 py-2 bg-warning bg-opacity-10 border-start border-4 border-warning rounded shadow-sm d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2 text-warning">
                <i data-lucide="shield-alert" class="w-4 h-4 animate-pulse"></i>
                <p class="mb-0 small fw-bold">Security Action Required: {{ $pwRequests->count() }} password changes are awaiting finalization.</p>
            </div>
        </div>
    </div>
@endif

<div class="row g-4 mb-5">
    {{-- Total Schools (Non-clickable) --}}
    <div class="col-md-4">
        <div class="card p-4 h-100 bg-transparent shadow-sm border-2 border-slate-200" style="border-radius: 8px;">
            <h6 class="text-uppercase small fw-bold text-muted mb-1">Total Schools</h6>
            <h2 class="fw-black text-dark mb-0">{{ $totalSchools }}</h2>
        </div>
    </div>
    
    {{-- Active Admins KPI (Clickable) --}}
    <div class="col-md-4">
        <a href="{{ route('superadmin.dashboard', ['status' => 'active']) }}" class="text-decoration-none">
            <div class="card p-4 h-100 bg-transparent shadow-sm border-2 {{ request('status') == 'active' ? 'border-success' : 'border-slate-200' }}" style="border-radius: 8px; transition: transform 0.2s;">
                <div class="d-flex justify-content-between">
                    <h6 class="text-uppercase small fw-bold {{ request('status') == 'active' ? 'text-success' : 'text-muted' }} mb-1">Active Admins</h6>
                    @if(request('status') == 'active')
                        <span class="badge bg-success small">Filtered</span>
                    @endif
                </div>
                <h2 class="fw-black {{ request('status') == 'active' ? 'text-success' : 'text-dark' }} mb-0">{{ $activeCount }}</h2>
            </div>
        </a>
    </div>
    
    {{-- Inactive Accounts KPI (Clickable) --}}
    <div class="col-md-4">
        <a href="{{ route('superadmin.dashboard', ['status' => 'inactive']) }}" class="text-decoration-none">
            <div class="card p-4 h-100 bg-transparent shadow-sm border-2 {{ request('status') == 'inactive' ? 'border-danger' : 'border-slate-200' }}" style="border-radius: 8px; transition: transform 0.2s;">
                <div class="d-flex justify-content-between">
                    <h6 class="text-uppercase small fw-bold {{ request('status') == 'inactive' ? 'text-danger' : 'text-muted' }} mb-1">Inactive Accounts</h6>
                    @if(request('status') == 'inactive')
                        <span class="badge bg-danger small">Filtered</span>
                    @endif
                </div>
                <h2 class="fw-black {{ request('status') == 'inactive' ? 'text-danger' : 'text-dark' }} mb-0">{{ $inactiveCount }}</h2>
            </div>
        </a>
    </div>
</div>

@if(request()->has('status') || request()->has('search'))
    <div class="mb-3">
        <a href="{{ route('superadmin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-x-circle me-1"></i> Clear All Filters
        </a>
    </div>
@endif
<div class="card shadow-sm rounded-3" style="border: 1px solid #e2e8f0;">
    <div class="card-header bg-white py-4 border-bottom-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark">User Management</h5>
                <small class="text-muted">Directly manage Active/Inactive status and roles</small>
            </div>
            <a href="{{ route('superadmin.history') }}" class="btn btn-sm btn-light border fw-semibold shadow-sm text-dark">
                <i class="bi bi-list-ul me-1"></i> View Full History
            </a>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('superadmin.dashboard') }}" class="row g-2 align-items-center bg-light p-3 rounded-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search name or email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm shadow-sm border-secondary-subtle">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm shadow-sm border-secondary-subtle">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">Filter</button>
                <a href="{{ route('superadmin.dashboard') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="py-3 px-4 fw-semibold border-bottom-0">User Info</th>
                    <th class="py-3 fw-semibold border-bottom-0">Role</th>
                    <th class="py-3 fw-semibold border-bottom-0">Status</th>
                    <th class="py-3 px-4 fw-semibold border-bottom-0 text-end">Action</th>
                </tr>
            </thead>
            <tbody class="border-top-0 bg-white">
                @forelse($users as $user)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm {{ $user->status == 'inactive' ? 'opacity-50' : '' }}" style="width: 40px; height: 40px; background-color: #a52a2a;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold {{ $user->status == 'inactive' ? 'text-muted' : 'text-dark' }}">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    
                    <td class="py-3">
                        <form action="{{ route('superadmin.update_user', $user->id) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PUT')
                            
                            @if(auth()->id() == $user->id)
                                <span class="badge bg-dark text-[9px] uppercase tracking-wider">Self Record</span>
                            @else
                                <select name="role" class="form-select form-select-sm shadow-sm border-secondary-subtle" style="width: 130px;">
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                            @endif
                    </td>

                    <td class="py-3">
                        @if(auth()->id() == $user->id)
                            <span class="text-success small fw-bold uppercase"><i class="bi bi-circle-fill me-1 small"></i> Online</span>
                        @else
                            <select name="status" class="form-select form-select-sm shadow-sm border-secondary-subtle" style="width: 110px;">
                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            @if(auth()->id() != $user->id)
                                <button type="submit" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 fw-bold shadow-sm px-3">
                                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                                    <span>Save</span>
                                </button>
                                </form>

                                <div class="vr mx-1 opacity-20" style="height: 20px;"></div>

                                @if($user->pending_password)
                                    <form action="{{ route('superadmin.approve_password', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1 fw-bold shadow-sm px-3">
                                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                            <span>Finalize</span>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm px-3 transition-all hover:bg-slate-800 hover:text-white" 
                                            onclick="openResetModal('{{ $user->id }}', '{{ route('superadmin.users.reset_password', $user->id) }}')">
                                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                        <span>Reset</span>
                                    </button>
                                @endif

                                @if($user->status == 'inactive')
                                    <button type="button" 
                                            class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1 fw-bold shadow-sm px-3"
                                            onclick="openDeleteModal('{{ $user->id }}', '{{ $user->name }}', '{{ route('superadmin.users.destroy', $user->id) }}')">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        <span>Delete</span>
                                    </button>
                                @endif
                            @else
                                <span class="text-muted italic small">Protected Profile</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-1 d-block mb-2 opacity-50"></i>
                        No users found matching your search.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 
    MODALS ARE PLACED OUTSIDE THE TABLE FOR ACCESSIBILITY 
    AND TO PREVENT ID DUPLICATION 
--}}

{{-- Password Reset Modal --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form id="resetPasswordForm" method="POST" class="w-full">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-2xl overflow-hidden">
                <div class="p-3 text-white flex items-center justify-between" style="background-color: #a52a2a;">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        <h6 class="modal-title text-[10px] uppercase tracking-[0.1em] font-black mb-0">System Override</h6>
                    </div>
                    <button type="button" class="text-white/70 hover:text-white transition" data-bs-dismiss="modal">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <p class="text-[11px] text-slate-500 mb-4 italic">The password will be stored as 'Pending' until finalized.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="text-[9px] font-black uppercase text-slate-400 mb-1 block">New Password</label>
                            <input type="password" name="password" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded text-sm outline-none" required minlength="8">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-slate-400 mb-1 block">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded text-sm outline-none" required minlength="8">
                        </div>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 flex justify-end gap-2 border-t">
                    <button type="button" class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-slate-900 text-white text-[10px] font-bold uppercase rounded">Initiate</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Permanent Delete Confirmation Modal --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden">
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="p-4 text-center">
                    <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle mb-3" 
                         style="width: 60px; height: 60px; background-color: #fee2e2; color: #dc2626;">
                        <i data-lucide="alert-triangle" style="width: 30px; height: 30px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Delete Account?</h5>
                    <p class="text-muted small px-4">
                        This action is <span class="text-danger fw-bold">permanent</span>. All data for 
                        <span id="deleteTargetName" class="fw-bold text-dark"></span> will be erased.
                    </p>
                </div>

                <div class="modal-body px-4 pt-0">
                    <div class="bg-light p-3 rounded-3 mb-3 border">
                        <label class="text-[10px] font-black uppercase text-slate-500 mb-2 d-block tracking-wider text-center">
                            Type <span class="text-danger">DELETE</span> to confirm
                        </label>
                        {{-- Added onkeyup to force verification check --}}
                        <input type="text" id="deleteConfirmInput" class="form-control form-control-sm text-center fw-bold border-2 shadow-none" 
                               placeholder="Verification required" autocomplete="off"
                               onkeyup="document.getElementById('finalDeleteBtn').disabled = (this.value.trim().toUpperCase() !== 'DELETE');">
                    </div>
                </div>

                <div class="p-3 bg-slate-50 d-flex gap-2 justify-content-center border-top">
                    <button type="button" class="btn btn-light btn-sm fw-bold px-4 border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="finalDeleteBtn" class="btn btn-danger btn-sm fw-bold px-4 shadow-sm" disabled>
                        Confirm Purge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openResetModal(userId, actionUrl) {
        const form = document.getElementById('resetPasswordForm');
        form.action = actionUrl;
        const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        modal.show();
    }

    function openDeleteModal(userId, userName, actionUrl) {
        const form = document.getElementById('deleteUserForm');
        const nameSpan = document.getElementById('deleteTargetName');
        const input = document.getElementById('deleteConfirmInput');
        const btn = document.getElementById('finalDeleteBtn');

        form.action = actionUrl;
        nameSpan.innerText = userName;
        input.value = '';
        btn.disabled = true;

        const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        modal.show();
    }
</script>
@endsection