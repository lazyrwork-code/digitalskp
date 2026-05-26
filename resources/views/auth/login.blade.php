<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>SIGMA RM | Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/logo_rsds2.svg') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body class="login-body">

  <canvas id="particles-canvas"></canvas>

  <div class="login-wrapper">

    {{-- Panel Kiri --}}
    <div class="login-left">
      <div class="login-card">

        <div class="text-center mb-4">
          <img
            src="{{ asset('assets/Logo-RSUD-Dr.-Soetomo.png') }}"
            alt="Logo RSUD Dr. Soetomo"
            class="login-logo"
          >
        </div>

        <h4 class="login-title">Selamat Datang</h4>
        <p class="login-subtitle" id="typing-text"></p>

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <div class="input-group input-group-login">
              <span class="input-group-text">
                <i class="bi bi-person"></i>
              </span>
              <input
                type="text"
                name="username"
                class="form-control @error('username') is-invalid @enderror"
                placeholder="Masukkan username"
                value="{{ old('username') }}"
                autocomplete="username"
                required
              >
            </div>
            @error('username')
              <small class="text-danger mt-1 d-block">
                <i class="bi bi-exclamation-circle"></i> Username tidak ditemukan
              </small>
            @enderror
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group input-group-login">
              <span class="input-group-text">
                <i class="bi bi-lock"></i>
              </span>
              <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Masukkan password"
                autocomplete="current-password"
                required
              >
              <button
                type="button"
                class="input-group-text toggle-password"
                onclick="togglePassword()"
                aria-label="Tampilkan password"
              >
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
            @error('password')
              <small class="text-danger mt-1 d-block">
                <i class="bi bi-exclamation-circle"></i> Password salah
              </small>
            @enderror
          </div>

          {{-- Lupa Password (pending, hanya placeholder) --}}
          <div class="text-end mb-4">
            <a href="#" class="lupa-password" data-bs-toggle="tooltip" title="Hubungi Administrator">
              Lupa password?
            </a>
          </div>

          <button type="submit" class="btn btn-login w-100" id="btnLogin">
            <span class="btn-login-text">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </span>
            <span class="btn-login-loading d-none">
              <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
            </span>
          </button>
        </form>

        <p class="login-footer-text mt-4">
          &copy; {{ date('Y') }} RSUD Dr. Soetomo &mdash; SIGMA RM
        </p>
      </div>
    </div>

    {{-- Panel Kanan --}}
    <div class="login-right">
      <div class="login-right-content">

        <svg viewBox="0 0 420 420" xmlns="http://www.w3.org/2000/svg" class="login-svg-illustration" aria-hidden="true">
          <defs>
            <radialGradient id="circleGrad" cx="50%" cy="50%" r="50%">
              <stop offset="0%" stop-color="rgba(255,255,255,0.18)"/>
              <stop offset="100%" stop-color="rgba(255,255,255,0.04)"/>
            </radialGradient>
          </defs>

          {{-- Lingkaran latar dekoratif --}}
          <circle cx="210" cy="210" r="180" fill="url(#circleGrad)" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>
          <circle cx="210" cy="210" r="140" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="1" stroke-dasharray="6 4"/>
          <circle cx="210" cy="210" r="100" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>

          {{-- Palang Plus / Cross medis --}}
          <g class="svg-pulse">
            <rect x="178" y="148" width="64" height="124" rx="18" fill="rgba(255,255,255,0.22)" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
            <rect x="148" y="178" width="124" height="64" rx="18" fill="rgba(255,255,255,0.22)" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
          </g>

          {{-- Garis EKG / heartbeat --}}
          <g class="svg-ecg">
            <polyline
              points="60,300 90,300 105,270 120,330 138,255 155,345 170,300 360,300"
              fill="none"
              stroke="rgba(255,255,255,0.7)"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-dasharray="500"
              stroke-dashoffset="500"
            />
          </g>

          {{-- Ikon hati kecil kiri atas --}}
          <g transform="translate(80,95)" class="svg-float-1">
            <path d="M20,8 C20,3.6 16.4,0 12,0 C9.6,0 7.4,1.1 6,2.9 C4.6,1.1 2.4,0 0,0 C-4.4,0 -8,3.6 -8,8 C-8,16 6,24 6,24 C6,24 20,16 20,8 Z"
              fill="rgba(255,255,255,0.35)" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
          </g>

          {{-- Ikon obat/kapsul kanan atas --}}
          <g transform="translate(315,88)" class="svg-float-2">
            <rect x="-22" y="-8" width="44" height="16" rx="8" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
            <rect x="-22" y="-8" width="22" height="16" rx="8" fill="rgba(255,255,255,0.25)"/>
            <line x1="0" y1="-8" x2="0" y2="8" stroke="rgba(255,255,255,0.4)" stroke-width="1.5"/>
          </g>

          {{-- Ikon dokter / stetoskop kiri bawah --}}
          <g transform="translate(88,318)" class="svg-float-3">
            <circle cx="0" cy="0" r="10" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
            <path d="M-10,0 C-10,10 10,10 10,0" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="10" y1="0" x2="10" y2="-14" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="10" cy="-17" r="3" fill="rgba(255,255,255,0.5)"/>
          </g>

          {{-- Bintang / sparkle pojok kanan bawah --}}
          <g transform="translate(335,330)" class="svg-float-1">
            <line x1="0" y1="-10" x2="0" y2="10" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="-10" y1="0" x2="10" y2="0" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="-7" y1="-7" x2="7" y2="7" stroke="rgba(255,255,255,0.25)" stroke-width="1" stroke-linecap="round"/>
            <line x1="7" y1="-7" x2="-7" y2="7" stroke="rgba(255,255,255,0.25)" stroke-width="1" stroke-linecap="round"/>
          </g>

          {{-- Dot dekoratif --}}
          <circle cx="150" cy="108" r="4" fill="rgba(255,255,255,0.3)" class="svg-float-2"/>
          <circle cx="295" cy="320" r="3" fill="rgba(255,255,255,0.25)" class="svg-float-3"/>
          <circle cx="320" cy="160" r="5" fill="rgba(255,255,255,0.2)" class="svg-float-1"/>
          <circle cx="100" cy="240" r="3" fill="rgba(255,255,255,0.2)" class="svg-float-2"/>
        </svg>

        <div class="login-tagline text-center text-white mt-2">
          <h5 class="fw-bold mb-1">SIGMA RM</h5>
          <p class="mb-0 opacity-75" style="font-size: 0.88rem;">
            Sistem Digital SKP Rekam Medis<br>RSUD Dr. Soetomo Surabaya
          </p>
        </div>

      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>
  <script>
    // ── Toggle Password ──────────────────────────────────────────
    function togglePassword() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    }

    // ── Typing Effect ─────────────────────────────────────────────
    const phrases = [
      'Masukkan akun anda untuk melanjutkan.',
      'SIGMA RM – Sistem Digital SKP Rekam Medis.',
      'Profesional. Berintegritas. Penuh Cinta Kasih.',
    ];

    let pi = 0, ci = 0, deleting = false;
    const el = document.getElementById('typing-text');

    function typeEffect() {
      const current = phrases[pi];
      if (!deleting) {
        el.textContent = current.slice(0, ++ci);
        if (ci === current.length) {
          deleting = true;
          setTimeout(typeEffect, 2000);
          return;
        }
      } else {
        el.textContent = current.slice(0, --ci);
        if (ci === 0) {
          deleting = false;
          pi = (pi + 1) % phrases.length;
        }
      }
      setTimeout(typeEffect, deleting ? 40 : 70);
    }
    typeEffect();

    // ── Particles Background ──────────────────────────────────────
    const canvas = document.getElementById('particles-canvas');
    const ctx    = canvas.getContext('2d');
    let W, H, particles = [];

    function resize() {
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', () => { resize(); initParticles(); });

    function initParticles() {
      particles = Array.from({ length: 60 }, () => ({
        x: Math.random() * W,
        y: Math.random() * H,
        r: Math.random() * 3 + 1,
        dx: (Math.random() - 0.5) * 0.6,
        dy: (Math.random() - 0.5) * 0.6,
        alpha: Math.random() * 0.5 + 0.1,
      }));
    }
    initParticles();

    function drawParticles() {
      ctx.clearRect(0, 0, W, H);
      particles.forEach(p => {
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(255,255,255,${p.alpha})`;
        ctx.fill();

        p.x += p.dx;
        p.y += p.dy;
        if (p.x < 0 || p.x > W) p.dx *= -1;
        if (p.y < 0 || p.y > H) p.dy *= -1;
      });

      // garis antar partikel terdekat
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          const dist = Math.hypot(particles[i].x - particles[j].x, particles[i].y - particles[j].y);
          if (dist < 120) {
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.strokeStyle = `rgba(255,255,255,${0.12 * (1 - dist / 120)})`;
            ctx.lineWidth = 0.5;
            ctx.stroke();
          }
        }
      }
      requestAnimationFrame(drawParticles);
    }
    drawParticles();

    // ── Loading state saat submit ─────────────────────────────────
    document.getElementById('loginForm').addEventListener('submit', function () {
      const btn = document.getElementById('btnLogin');
      btn.disabled = true;
      btn.querySelector('.btn-login-text').classList.add('d-none');
      btn.querySelector('.btn-login-loading').classList.remove('d-none');
    });

    // ── Bootstrap Tooltip untuk Lupa Password ────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  </script>
</body>
</html>