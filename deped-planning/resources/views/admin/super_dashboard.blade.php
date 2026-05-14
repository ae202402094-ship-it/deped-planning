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

{{-- Stats Row --}}
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card p-4 h-100 bg-transparent shadow-sm" style="border: 2px solid #a52a2a; border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Total Schools</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ $totalSchools ?? 0 }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(165, 42, 42, 0.1); color: #a52a2a;">
                    <i data-lucide="school"></i>
                </div>
            </div>
            <a href="{{ route('admin.schools') }}" class="fw-semibold text-decoration-none small d-flex align-items-center mt-auto" style="color: #a52a2a;">
                Manage Data <i data-lucide="arrow-right" class="ms-1" style="width: 14px; height: 14px;"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-4 h-100 bg-transparent shadow-sm" style="border: 2px solid #dc3545; border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Archived</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ \App\Models\School::onlyTrashed()->count() }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                    <i data-lucide="archive"></i>
                </div>
            </div>
            <a href="{{ route('schools.archive') }}" class="fw-semibold text-decoration-none small d-flex align-items-center mt-auto text-danger">
                View Archive <i data-lucide="arrow-right" class="ms-1" style="width: 14px; height: 14px;"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-4 h-100 bg-transparent shadow-sm" style="border: 2px solid #a52a2a; border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Pending</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ $pendingCount }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(165, 42, 42, 0.1); color: #a52a2a;">
                    <i data-lucide="user-plus"></i>
                </div>
            </div>
            <a href="{{ route('superadmin.notifications') }}" class="fw-semibold text-decoration-none small d-flex align-items-center mt-auto" style="color: #a52a2a;">
                Review Requests <i data-lucide="arrow-right" class="ms-1" style="width: 14px; height: 14px;"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-4 h-100 bg-transparent shadow-sm" style="border: 2px solid #a52a2a; border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Total Users</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ $totalUsers }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(165, 42, 42, 0.1); color: #a52a2a;">
                    <i data-lucide="users"></i>
                </div>
            </div>
            <div class="small text-muted mt-auto">Admins: {{ $adminCount }}</div>
        </div>
    </div>
</div>

<div class="card shadow-sm rounded-3" style="border: 1px solid #e2e8f0;">
    <div class="card-header bg-white py-4 border-bottom-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark">User Management</h5>
                <small class="text-muted">Edit roles and statuses directly</small>
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
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 40px; height: 40px; background-color: #a52a2a;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
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
                                <select name="role" class="form-select form-select-sm shadow-sm border-secondary-subtle" style="width: 140px;">
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                            @endif
                    </td>

                    <td class="py-3">
                        @if(auth()->id() == $user->id)
                            <span class="text-success small fw-bold uppercase">Active Session</span>
                        @else
                            <select name="status" class="form-select form-select-sm shadow-sm border-secondary-subtle" style="width: 140px;">
                                <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $user->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $user->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            @if(auth()->id() != $user->id)
                                {{-- Save Changes Button --}}
                                <button type="submit" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 fw-bold shadow-sm px-3">
                                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                                    <span>Save</span>
                                </button>
                                </form> {{-- Close form for Role/Status update --}}

                                <div class="vr mx-1 opacity-20" style="height: 20px;"></div>

                                {{-- Two-Step Password Management --}}
                                @if($user->pending_password)
                                    <form action="{{ route('superadmin.approve_password', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1 fw-bold shadow-sm px-3" title="Finalize Password Change">
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
                            @else
                                <span class="text-muted italic small">No actions available</span>
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

{{-- Final Optimized Reset Password Modal --}}
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
                    <p class="text-[11px] text-slate-500 mb-4 italic">The new password will be stored as 'Pending' until you click Finalize in the dashboard.</p>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-[9px] font-black uppercase text-slate-400 tracking-wider mb-1 block">New Secure Password</label>
                            <input type="password" name="password" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all" required minlength="8">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-slate-400 tracking-wider mb-1 block">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all" required minlength="8">
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase hover:text-slate-600 transition" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-slate-900 text-white text-[10px] font-bold uppercase rounded shadow-sm hover:bg-black transition-all">
                        Initiate Change
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openResetModal(userId, actionUrl) {
        const form = document.getElementById('resetPasswordForm');
        form.action = actionUrl;
        const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        modal.show();
    }
</script>
@endsection