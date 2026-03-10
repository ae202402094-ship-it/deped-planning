@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('admin.partials.super_sidebar')
        </div>

        <div class="col-md-9">
            <h2 class="mb-4 fw-bold text-dark">Super Admin Dashboard</h2>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row g-3 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase small fw-bold">Total Accounts</h6>
                                <h2 class="mb-0">{{ $totalUsers }}</h2>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-warning text-dark p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase small fw-bold">Pending Requests</h6>
                                <h2 class="mb-0">{{ $pendingCount }}</h2>
                            </div>
                            <i class="bi bi-person-plus-fill fs-1 opacity-50"></i>
                        </div>
                        <a href="{{ route('superadmin.notifications') }}" class="text-dark small mt-2 fw-bold text-decoration-none">
                            View Requests <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-dark text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase small fw-bold">Active Admins</h6>
                                <h2 class="mb-0">{{ $adminCount }}</h2>
                            </div>
                            <i class="bi bi-shield-lock-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Recent Registrations</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($user->role) }}</span></td>
                                <td>
                                    <span class="badge {{ $user->status == 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection