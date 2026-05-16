<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button class="btn btn-primary w-100">
        Register
    </button>
</form>
