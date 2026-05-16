<section>
    <header>
        <h5 class="fw-bold mb-1">Informasi Profil</h5>
        <p class="text-muted small">
            Perbarui data akun kamu.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        {{-- Nama --}}
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text"
                   name="nama"
                   value="{{ old('nama', auth()->user()->nama) }}"
                   class="form-control @error('nama') is-invalid @enderror">

            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Username --}}
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text"
                   name="username"
                   value="{{ old('username', auth()->user()->username) }}"
                   class="form-control @error('username') is-invalid @enderror">

            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', auth()->user()->email) }}"
                   class="form-control @error('email') is-invalid @enderror">

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- NIP --}}
        <div class="mb-3">
            <label class="form-label">NIP</label>
            <input type="text"
                   name="nip"
                   value="{{ old('nip', auth()->user()->nip) }}"
                   class="form-control @error('nip') is-invalid @enderror">

            @error('nip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Role --}}
        <div class="mb-4">
            <label class="form-label">Role</label>
            <input type="text"
                   value="{{ auth()->user()->role }}"
                   class="form-control bg-light"
                   readonly>
        </div>

        <button class="btn btn-prima">
            Simpan Perubahan
        </button>

        @if (session('status') === 'profile-updated')
            <p class="text-success small mt-2">
                Profil berhasil diperbarui.
            </p>
        @endif
    </form>
</section>
