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
        <div class="card p-4 h-100 bg-transparent shadow-sm" 
             style="border: 2px solid #a52a2a; border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Total Schools</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ $totalSchools ?? 0 }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 50px; height: 50px; background-color: rgba(165, 42, 42, 0.1); color: #a52a2a;">
                    <i class="bi bi-building fs-4"></i>
                </div>
            </div>
            <a href="{{ route('admin.schools') }}" class="fw-semibold text-decoration-none small d-flex align-items-center mt-auto" style="color: #a52a2a;">
                Manage Data <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-4 h-100 bg-transparent shadow-sm" 
             style="border: 2px solid #a52a2a; border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Pending Requests</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ $pendingCount }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 50px; height: 50px; background-color: rgba(165, 42, 42, 0.1); color: #a52a2a;">
                    <i class="bi bi-person-plus-fill fs-4"></i>
                </div>
            </div>
            <a href="{{ route('superadmin.notifications') }}" class="fw-semibold text-decoration-none small d-flex align-items-center mt-auto" style="color: #a52a2a;">
                Review Requests <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-4 h-100 bg-transparent shadow-sm" 
             style="border: 2px solid #a52a2a; border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Active Admins</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ $adminCount }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 50px; height: 50px; background-color: rgba(165, 42, 42, 0.1); color: #a52a2a;">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-4 h-100 bg-transparent shadow-sm" 
             style="border: 2px solid #a52a2a; border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase small fw-bold mb-1 text-muted" style="letter-spacing: 0.5px;">Total Accounts</h6>
                    <h2 class="mb-0 fw-bolder text-dark">{{ $totalUsers }}</h2>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 50px; height: 50px; background-color: rgba(165, 42, 42, 0.1); color: #a52a2a;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm rounded-3" style="border: 1px solid #e2e8f0;">
    <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-bottom-0">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Recent Registrations</h5>
            <small class="text-muted">Latest users to join the platform</small>
        </div>
        <a href="{{ route('superadmin.history') }}" class="btn btn-sm btn-light border fw-semibold shadow-sm text-dark">
            <i class="bi bi-list-ul me-1"></i> View Full History
        </a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="py-3 px-4 fw-semibold border-bottom-0">User Info</th>
                    <th class="py-3 fw-semibold border-bottom-0">Role</th>
                    <th class="py-3 fw-semibold border-bottom-0">Status</th>
                    <th class="py-3 px-4 fw-semibold border-bottom-0 text-end">Joined</th>
                </tr>
            </thead>
            <tbody class="border-top-0 bg-white">
                @foreach($recentUsers as $user)
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
                        <span class="badge bg-light text-dark border px-2 py-1">{{ strtoupper($user->role) }}</span>
                    </td>
                    <td class="py-3">
                        @if($user->status == 'approved')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i class="bi bi-check-circle me-1"></i> Approved</span>
                        @elseif($user->status == 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">{{ ucfirst($user->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-end text-muted small">
                        {{ $user->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection