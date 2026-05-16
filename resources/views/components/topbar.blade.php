<div class="topbar d-flex justify-content-between align-items-center">
    <button class="btn btn-light" id="toggleSidebar">
        <i class="bi bi-list fs-4"></i>
    </button>

    <div class="dropdown">
        <button class="btn btn-light d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
            <img src="https://i.pravatar.cc/40" class="rounded-circle" width="36" height="36">
            <div class="text-start d-none d-md-block">
                <div class="fw-semibold">{{ auth()->user()->nama }}</div>
                <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
            </div>
            <i class="bi bi-chevron-down"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
            <li><a class="dropdown-item" href="{{ route('profile.edit', ['tab' => 'password']) }}"><i class="bi bi-key me-2"></i> Ganti Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
