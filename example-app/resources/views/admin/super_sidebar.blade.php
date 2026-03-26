<div class="col-md-3 bg-dark min-vh-100 p-3 text-white shadow">
    <div class="text-center mb-4">
        <h4 class="fw-bold">Super Admin</h4>
        <small class="text-muted">Master Control Panel</small>
    </div>
    <hr class="bg-secondary">
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="{{ route('superadmin.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('superadmin.dashboard') ? 'active bg-primary' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('superadmin.notifications') }}" 
               class="nav-link text-white d-flex justify-content-between align-items-center {{ request()->routeIs('superadmin.notifications') ? 'active bg-primary' : '' }}">
                <span><i class="bi bi-person-lines-fill me-2"></i> Account Requests</span>
                
                @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('admin.schools.archive') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.schools.archive') ? 'active bg-primary' : '' }}">
                <i class="bi bi-archive me-2"></i> School Archive
            </a>
        </li>

      

        <li class="nav-item mb-2">
            <a href="{{ route('superadmin.history') }}" 
               class="nav-link text-white {{ request()->routeIs('superadmin.history') ? 'active bg-primary' : '' }}">
                <i class="bi bi-clock-history me-2"></i> System History
            </a>
        </li>

        <hr class="bg-secondary mt-3">
        <small class="text-uppercase text-muted fw-bold mb-2 d-block px-3">Registry Management</small>

        <li class="nav-item mb-2">
            <a href="{{ route('admin.schools') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.schools') ? 'active bg-primary' : '' }}">
                <i class="bi bi-building me-2"></i> Manage Schools
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a href="{{ route('admin.map') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.map') ? 'active bg-primary' : '' }}">
                <i class="bi bi-geo-alt-fill me-2"></i> Manage Maps
            </a>
        </li>
    </ul>

    <div class="mt-5">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>