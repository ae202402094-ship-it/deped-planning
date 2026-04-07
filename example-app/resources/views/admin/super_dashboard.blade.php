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
            <a href="{{ route('admin.schools.archive') }}" class="fw-semibold text-decoration-none small d-flex align-items-center mt-auto text-danger">
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

        <form method="GET" action="{{ route('superadmin.dashboard') }}" class="row g-2 align-items-center bg-light p-3 rounded-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search name or email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
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
                    
                    <form id="edit-form-{{ $user->id }}" action="{{ route('superadmin.update_user', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <td class="py-3">
                            <select name="role" class="form-select form-select-sm shadow-sm border-secondary-subtle" style="width: 140px;">
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                        </td>

                        <td class="py-3">
                            <select name="status" class="form-select form-select-sm shadow-sm border-secondary-subtle" style="width: 140px;">
                                <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $user->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $user->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </td>

                        <td class="px-4 py-3 text-end">
                            <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm px-3">
                                <i class="bi bi-save me-1"></i> Save
                            </button>
                        </td>
                    </form>
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
    
    @if($users->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection