@extends('layouts.super_admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">School Archive</h2>
        <p class="text-muted mb-0">Manage and restore previously deleted school records from the registry.</p>
    </div>
    <div class="text-end">
        <span class="badge bg-secondary px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-archive me-1"></i> {{ $archivedSchools->count() }} Archived Records
        </span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<div class="card shadow-sm border-0 rounded-3">
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="py-3 px-4 fw-semibold border-bottom-0">School Details</th>
                    <th class="py-3 fw-semibold border-bottom-0">School ID</th>
                    <th class="py-3 fw-semibold border-bottom-0">Deletion Date</th>
                    <th class="py-3 px-4 fw-semibold border-bottom-0 text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="border-top-0 bg-white">
                @forelse($archivedSchools as $school)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" 
                                     style="width: 40px; height: 40px; background-color: #6c757d;">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $school->name }}</h6>
                                    <small class="text-muted">Archived Protocol</small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <code class="fw-bold text-secondary bg-light px-2 py-1 rounded">{{ $school->school_id }}</code>
                        </td>
                        <td class="py-3">
                            <div class="d-flex flex-column">
                                <span class="text-dark fw-medium">{{ $school->deleted_at->format('M d, Y') }}</span>
                                <small class="text-muted">{{ $school->deleted_at->format('h:i A') }}</small>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <form action="{{ route('superadmin.restore_school', $school->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm px-3">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('superadmin.force_delete_school', $school->id) }}" 
                                  method="POST" 
                                  class="d-inline" 
                                  onsubmit="return confirm('CRITICAL: This will permanently erase this school from the database. This action cannot be undone. Proceed?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm px-3">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Permanent Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="fw-bold">No Archived Records</h6>
                            <p class="small mb-0">All schools are currently active in the registry.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection