@extends('layouts.super_admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Pending Account Requests</h2>
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Action Required</h5>
        <small class="text-muted">Review and approve or reject new administrator registrations.</small>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>User Profile</th>
                    <th>Email Address</th>
                    <th>Requested Role</th>
                    <th>Date Applied</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center me-2 fw-bold" style="width: 35px; height: 35px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $user->name }}</strong><br>
                                <span class="badge bg-warning text-dark small mt-1">Pending Approval</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-secondary">{{ strtoupper($user->role) }}</span></td>
                    <td>
                        <div>{{ $user->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <form action="{{ route('superadmin.approve', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                            </form>

                            <form action="{{ route('superadmin.reject', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject and delete this request?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">
                                    <i class="bi bi-trash"></i> Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-person-check fs-1 d-block mb-3 text-success opacity-50"></i>
                        <h6 class="fw-bold">All caught up!</h6>
                        <p class="mb-0">There are no pending account requests at this time.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection