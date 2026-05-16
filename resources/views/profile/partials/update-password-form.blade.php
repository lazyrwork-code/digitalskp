<section>
    <header class="mb-3">
        <h5 class="fw-bold mb-1">Ubah Password</h5>
        <p class="text-muted small">
            Gunakan password yang kuat dan aman.
        </p>
    </header>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        {{-- Current Password --}}
        <div class="mb-3">
            <label class="form-label">Password Saat Ini</label>
            <input type="password"
                   name="current_password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">

            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- New Password --}}
        <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password"
                   name="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror">

            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror">

            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-prima">
            Simpan Password
        </button>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success mt-3 mb-0">
                Password berhasil diperbarui.
            </div>
        @endif
    </form>
</section>
