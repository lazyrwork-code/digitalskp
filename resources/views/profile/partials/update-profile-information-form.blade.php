<section>
    @php
    function formatNip($nip) {
        $raw = preg_replace('/\D/', '', $nip ?? '');
        if (strlen($raw) === 0) return '-';
        $formatted = substr($raw, 0, 8);
        if (strlen($raw) > 8)  $formatted .= ' ' . substr($raw, 8, 6);
        if (strlen($raw) > 14) $formatted .= ' ' . substr($raw, 14, 1);
        if (strlen($raw) > 15) $formatted .= ' ' . substr($raw, 15, 3);
        return $formatted;
    }
    @endphp
    <div class="page-header mb-4">
        <div class="page-header-icon">
            <i class="bi bi-person"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-0">Informasi Profil</h5>
            <p class="text-muted small mb-0">Data akun kamu di sistem SIGMA RM</p>
        </div>
    </div>

    <div class="row g-3">

        {{-- Nama --}}
        <div class="col-md-6">
            <label class="form-label-custom">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-start-0 ps-0 bg-light"
                       value="{{ auth()->user()->nama }}"
                       readonly>
            </div>
        </div>

        {{-- NIP --}}
        <div class="col-md-6">
            <label class="form-label-custom">NIP</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-credit-card text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-start-0 ps-0 bg-light"
                       value="{{ formatNip(auth()->user()->nip) }}"
                       readonly>
            </div>
        </div>

        {{-- Username --}}
        <div class="col-md-6">
            <label class="form-label-custom">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-at text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-start-0 ps-0 bg-light"
                       value="{{ auth()->user()->username }}"
                       readonly>
            </div>
        </div>

        {{-- Email --}}
        <div class="col-md-6">
            <label class="form-label-custom">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-start-0 ps-0 bg-light"
                       value="{{ auth()->user()->email }}"
                       readonly>
            </div>
        </div>

        {{-- Role --}}
        <div class="col-md-6">
            <label class="form-label-custom">Role</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-shield text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-start-0 ps-0 bg-light"
                       value="{{ ucfirst(auth()->user()->role) }}"
                       readonly>
            </div>
        </div>

    </div>

</section>