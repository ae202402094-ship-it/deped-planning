@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Pending Admin Approvals</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingAdmins as $admin)
            <tr>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td><span class="badge bg-warning">{{ $admin->status }}</span></td>
                <td>
                    <form action="{{ route('admin.approve.action', $admin->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Approve Account</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No pending admin approvals.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection