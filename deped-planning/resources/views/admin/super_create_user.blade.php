@extends('layouts.super_admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="modal-header text-white p-3" style="background-color: #a52a2a; border-bottom: none;">
    <h5 class="modal-title fw-bold">Provision New Administrator Account</h5>
</div>
        <div class="card-body">
            <form action="{{ route('superadmin.users.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Official Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="example@deped.gov.ph">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Assigned Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin">Standard Admin (Planning Officer)</option>
                        <option value="super_admin">Super Admin (System Controller)</option>
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn fw-bold px-4 shadow-sm text-white" 
        style="background-color: #a52a2a; border: none;">
    Create Account
</button>
            </form>
        </div>
    </div>
</div>
@endsection