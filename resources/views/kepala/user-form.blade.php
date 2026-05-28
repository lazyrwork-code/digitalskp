@extends('layouts.app')

@section('title', $user ? 'Edit User' : 'Tambah User')

@section('content')

<style>
.skp-card {
    background: #fff;
    border: 0.5px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.5rem;
}
.form-label-custom {
    font-size: 12.5px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}
.form-control, .form-select {
    font-size: 13.5px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 8px 12px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.form-control:focus, .form-select:focus {
    border-color: #1D9E75;
    box-shadow: 0 0 0 3px rgba(29,158,117,0.1);
    outline: none;
}
.form-control:disabled, .form-control[readonly] {
    background: #f9fafb;
    color: #6b7280;
}
.section-title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #f3f4f6;
}
.page-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.5rem;
}
.page-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: #e8f5f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1D9E75;
    font-size: 18px;
}
.btn-submit {
    background: #1D9E75;
    color: #fff;
    border: none;
    padding: 10px 28px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-submit:hover {
    background: #178a65;
}
.input-group .form-control {
    border-radius: 8px !important;
}
.input-group .btn {
    border-radius: 8px !important;
    border: 1px solid #e5e7eb;
    font-size: 13px;
}
</style>

{{-- Header --}}
<div class="page-header">
    <div class="page-header-icon">
        <i class="bi bi-person-{{ $user ? 'gear' : 'plus' }}"></i>
    </div>
    <div>
        <h4 class="fw-bold mb-0" style="font-size:18px;">
            {{ $user ? 'Edit User' : 'Tambah User' }}
        </h4>
        <div style="font-size:12.5px; color:#6b7280;">
            {{ $user ? 'Perbarui data akun pegawai' : 'Tambahkan akun pegawai baru ke sistem' }}
        </div>
    </div>
</div>

<div class="skp-card">
    <form method="POST"
          action="{{ $user ? route('kepala.user.update', $user->id) : route('kepala.user.store') }}">
        @csrf
        @if($user) @method('PUT') @endif

        {{-- Informasi Akun --}}
        <div class="section-title">
            <i class="bi bi-person-vcard text-muted" style="font-size:14px;"></i>
            Informasi Akun
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label-custom">Nama Lengkap</label>
                <input type="text" name="nama"
                       value="{{ old('nama', $user->nama ?? '') }}"
                       class="form-control @error('nama') is-invalid @enderror"
                       placeholder="Nama lengkap pegawai">
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-custom">NIP</label>
                <input type="text" name="nip" id="nip"
                       value="{{ old('nip', $user->nip ?? '') }}"
                       class="form-control @error('nip') is-invalid @enderror"
                       placeholder="NIP (opsional)">
                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-custom">Username</label>
                <input type="text" name="username"
                       value="{{ old('username', $user->username ?? '') }}"
                       class="form-control @error('username') is-invalid @enderror"
                       placeholder="Username untuk login">
                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-custom">Email</label>
                <input type="email" name="email" id="email"
                       value="{{ old('email', $user->email ?? '') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="Alamat email" readonly>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-custom">Role</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror">
                    <option value="pegawai" {{ old('role', $user->role ?? 'pegawai') === 'pegawai' ? 'selected' : '' }}>
                        Pegawai
                    </option>
                    <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="section-title">
            <i class="bi bi-lock text-muted" style="font-size:14px;"></i>
            {{ $user ? 'Ganti Password' : 'Password' }}
            @if($user)
                <span style="font-size:12px; font-weight:400; color:#9ca3af;">
                    (kosongkan jika tidak diubah)
                </span>
            @endif
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label-custom">
                    {{ $user ? 'Password Baru' : 'Password' }}
                </label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="{{ $user ? 'Isi jika ingin ganti password' : 'Buat password' }}">
                    <button type="button" class="btn bg-light toggle-password" data-target="password">
                        <i class="bi bi-eye text-muted"></i>
                    </button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label-custom">Konfirmasi Password</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi password">
                    <button type="button" class="btn bg-light toggle-password" data-target="password_confirmation">
                        <i class="bi bi-eye text-muted"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="d-flex align-items-center justify-content-between pt-3"
             style="border-top: 1px solid #f3f4f6;">
            <div style="font-size:12px; color:#9ca3af; display:flex; align-items:center; gap:6px;">
                <i class="bi bi-info-circle"></i>
                Semua field wajib diisi kecuali NIP dan Password (saat edit)
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('kepala.user.index') }}"
                   style="padding:10px 20px; border-radius:9px; font-size:14px; font-weight:500;
                          border:1px solid #e5e7eb; background:#fff; color:#374151;
                          display:inline-flex; align-items:center; gap:7px; text-decoration:none;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-lg"></i>
                    {{ $user ? 'Simpan Perubahan' : 'Tambah User' }}
                </button>
            </div>
        </div>

    </form>
</div>

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
<script>
document.getElementById('nip').addEventListener('input', function () {
    let raw = this.value.replace(/\D/g, '').slice(0, 18);

    // Format: 8 - 6 - 1 - 3
    let formatted = '';
    if (raw.length > 0)  formatted  = raw.slice(0, 8);
    if (raw.length > 8)  formatted += ' ' + raw.slice(8, 14);
    if (raw.length > 14) formatted += ' ' + raw.slice(14, 15);
    if (raw.length > 15) formatted += ' ' + raw.slice(15, 18);

    this.value = formatted;

    // Auto-isi email pakai raw (tanpa spasi)
    const emailField = document.getElementById('email');
    if (raw !== '') {
        emailField.value = raw + '@pegawai.com';
    } else {
        emailField.value = '';
    }
});
</script>

@endsection