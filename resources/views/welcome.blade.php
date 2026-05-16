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

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
          <div class="input-group login-input">
            <span class="input-group-text">
              <i class="bi bi-person"></i>
            </span>
            <input type="text" name="username" class="form-control" placeholder="Username">
          </div>
        </div>

        <div class="mb-3">
          <div class="input-group login-input">
            <span class="input-group-text">
              <i class="bi bi-lock"></i>
            </span>
            <input type="password" name="password" class="form-control" placeholder="Password">
          </div>
        </div>

        <button class="btn btn-login w-100 mb-3">Login</button>
      </form>

      <a class="btn btn-social w-100 mb-2" href="{{ url('dashboard') }}">Masuk Pegawai</a>
      <a class="btn btn-social w-100 mb-2" href="{{ url('dashboard-admin') }}">Masuk Admin</a>
      <a class="btn btn-social w-100" href="{{ url('dashboard-kepalarm') }}">Masuk Kepala RM</a>
    </div>

    <div class="login-right d-flex align-items-center justify-content-center p-5">
      <img src="https://i.imgur.com/LLVoK9S.png" class="img-fluid rounded-4">
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></cript>
</body>
</html>
