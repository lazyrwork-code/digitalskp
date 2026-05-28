<div class="topbar d-flex justify-content-between align-items-center">
    <button class="btn btn-light" id="toggleSidebar">
        <i class="bi bi-list fs-4"></i>
    </button>

    <div class="dropdown">
        <button class="btn btn-light d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
            
            {{-- Avatar Inisial --}}
            @php
                $nama = auth()->user()->nama;
                $kata = explode(' ', trim($nama));
                $inisial = strtoupper(substr($kata[0], 0, 1));
                if (count($kata) > 1) $inisial .= strtoupper(substr($kata[1], 0, 1));
            @endphp
            <div style="
                width: 36px; height: 36px;
                border-radius: 50%;
                background: #1D9E75;
                color: #fff;
                font-size: 13px;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                letter-spacing: 0.5px;
            ">{{ $inisial }}</div>

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
