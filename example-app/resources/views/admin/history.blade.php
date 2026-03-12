@extends('layouts.super_admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">System History Log</h2>
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">User Audit Log</h5>
        <small class="text-muted">Tracks all user accounts sorted by their latest activity/status change.</small>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>User Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Current Status</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <strong>{{ $user->name }}</strong>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-dark">{{ strtoupper($user->role) }}</span></td>
                    <td>
                        @if($user->status == 'approved')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Approved</span>
                        @elseif($user->status == 'pending')
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                        @else
                            <span class="badge bg-danger">{{ ucfirst($user->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div>{{ $user->updated_at->format('M d, Y h:i A') }}</div>
                        <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                        No history records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($history->hasPages())
        <div class="card-footer bg-white py-3 d-flex justify-content-center">
            {{ $history->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection