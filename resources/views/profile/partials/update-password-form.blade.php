<section>
    <header class="mb-4">
        <h5 class="fw-bold mb-1">Ubah Password</h5>
        <p class="text-muted small">Gunakan password yang kuat dan aman.</p>
    </header>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        {{-- Current Password --}}
        <div class="mb-3">
            <label class="form-label fw-semibold small">Password Saat Ini</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       placeholder="Masukkan password saat ini"
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                <button type="button" class="btn btn-light border toggle-password" data-target="current_password">
                    <i class="bi bi-eye text-muted"></i>
                </button>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- New Password --}}
        <div class="mb-3">
            <label class="form-label fw-semibold small">Password Baru</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="bi bi-key text-muted"></i>
                </span>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Masukkan password baru"
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                <button type="button" class="btn btn-light border toggle-password" data-target="password">
                    <i class="bi bi-eye text-muted"></i>
                </button>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label class="form-label fw-semibold small">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="bi bi-key-fill text-muted"></i>
                </span>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       placeholder="Ulangi password baru"
                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror">
                <button type="button" class="btn btn-light border toggle-password" data-target="password_confirmation">
                    <i class="bi bi-eye text-muted"></i>
                </button>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-lg me-1"></i> Simpan Password
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success py-2 px-3 mb-0 small">
                    <i class="bi bi-check-circle me-1"></i> Password berhasil diperbarui.
                </div>
            @endif
        </div>
    </form>
</section>

{{-- Toggle Password Script --}}
<script>
document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.target);
        const icon  = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
});
</script>