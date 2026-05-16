<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Digital SKP | Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body>
  <div class="login-wrapper d-flex">
    <div class="login-left d-flex flex-column justify-content-center p-5">
      <h3 class="fw-bold text-center mb-2">LOGIN</h3>
      <p class="text-muted text-center mb-4">
        Masukkan akun anda untuk melanjutkan
      </p>

      @if ($errors->any())
          <div class="alert alert-danger">
              {{ $errors->first() }}
          </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-person"></i>
                </span>

                <input 
                    type="text"
                    name="username"
                    class="form-control border-start-0"
                    placeholder="Masukkan username"
                    value="{{ old('username') }}"
                    required
                >
              </div>
              @error('username')
                <small class="text-danger small mt-1">
                  Username salah
                </small>
              @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>

            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-lock"></i>
                </span>

                <input 
                    type="password"
                    id="password"
                    name="password"
                    class="form-control border-start-0 border-end-0"
                    placeholder="Masukkan password"
                    required
                >

                <span class="input-group-text bg-white border-start-0"
                      style="cursor:pointer"
                      onclick="togglePassword()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </span>
            </div>
            @error('password')
                <small class="text-danger small mt-1">
                  Password salah
                </small>
              @enderror
        </div>

        <button class="btn btn-login w-100 mb-3">Login</button>
      </form>
    </div>

    <div class="login-right d-flex align-items-center justify-content-center p-5">
      <img src="https://i.imgur.com/LLVoK9S.png" class="img-fluid rounded-4">
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
<script>
function togglePassword() {
    let input = document.getElementById("password");
    let icon = document.getElementById("eyeIcon");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye","bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("bi-eye-slash","bi-eye");
    }
}
</script>
