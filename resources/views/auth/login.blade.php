@php
    $setting = \App\Models\SiteSetting::first();
    $appName = $setting->app_name ?? 'UJIAN ONLINE UNUSU';
    $logoUrl = $setting?->logo_path ? asset('storage/' . $setting->logo_path) : asset('images/default-unusu-logo.svg');
    $campusImageUrl = $setting?->campus_image_path ? asset('storage/' . $setting->campus_image_path) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ $appName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: #f3f6f9; color: #0f172a; }
        .wrap { max-width: 1360px; margin: 0 auto; padding: clamp(10px, 2vw, 18px); }
        .shell { border-radius: 22px; overflow: hidden; border: 1px solid #dbe3ee; background: #fff; display: grid; grid-template-columns: 1.25fr 0.95fr; }
        .left { position: relative; min-height: min(70vh, 760px); background: linear-gradient(150deg, #064e3b, #166534); color: #fff; }
        .left .bg { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0.88; }
        .left .shade { position: absolute; inset: 0; background: linear-gradient(95deg, rgba(3, 30, 20, 0.85), rgba(6, 78, 59, 0.3)); }
        .left .content { position: relative; z-index: 2; padding: clamp(18px, 3vw, 46px); display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
        .brand-mark {
            width: 132px;
            height: 132px;
            border-radius: 30px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, rgba(255,255,255,0.94), rgba(255,255,255,0.78));
            border: 1px solid rgba(255,255,255,0.45);
            box-shadow: 0 18px 34px rgba(2, 44, 34, 0.35), inset 0 0 0 1px rgba(255,255,255,0.58);
            backdrop-filter: blur(4px);
        }
        .brand-mark img {
            width: 104px;
            height: 104px;
            object-fit: contain;
            border-radius: 20px;
            filter: drop-shadow(0 8px 12px rgba(15, 23, 42, 0.18));
        }
        .brand h1 { margin: 16px 0 8px; font-size: clamp(28px, 4.6vw, 56px); line-height: 1.05; letter-spacing: -0.02em; }
        .brand p { margin: 0; color: #d1fae5; font-size: clamp(15px, 2.1vw, 27px); line-height: 1.5; max-width: 560px; }
        .right { padding: clamp(18px, 3vw, 48px) clamp(16px, 3vw, 44px); }
        .head { text-align: center; }
        .head h2 { margin: 0; color: #0f7a41; font-size: clamp(30px, 4.2vw, 48px); }
        .head p { margin: 10px 0 0; color: #475569; font-size: clamp(15px, 2vw, 23px); }
        .row { margin-top: 24px; }
        label { display: block; margin-bottom: 8px; font-size: clamp(14px, 1.7vw, 19px); font-weight: 600; color: #334155; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; border: 1px solid #d4dde8; border-radius: 12px; padding: clamp(12px, 1.8vw, 16px); font-size: clamp(15px, 1.9vw, 20px); outline: none; }
        input:focus { border-color: #0f7a41; box-shadow: 0 0 0 4px rgba(15,122,65,0.16); }
        .meta { margin-top: 12px; display: flex; justify-content: space-between; align-items: center; gap: 10px; font-size: clamp(13px, 1.5vw, 18px); }
        .meta a { color: #0f7a41; text-decoration: none; font-weight: 600; }
        .submit { margin-top: 18px; width: 100%; border: 0; border-radius: 14px; background: #0f7a41; color: #fff; font-weight: 700; padding: clamp(12px, 1.9vw, 16px); font-size: clamp(16px, 2vw, 21px); cursor: pointer; }
        .error { margin-top: 7px; color: #dc2626; font-size: 15px; }
        .landing { display: inline-block; margin-top: 16px; text-decoration: none; color: #334155; font-weight: 700; }
        @media (max-width: 1100px) {
            .shell { grid-template-columns: 1fr; }
            .left { min-height: 320px; }
            .brand-mark { width: 96px; height: 96px; border-radius: 22px; }
            .brand-mark img { width: 76px; height: 76px; }
        }
        @media (max-width: 640px) {
            .meta { flex-direction: column; align-items: flex-start; }
            .landing { margin-top: 18px; font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="shell">
            <section class="left">
                <div class="bg" style="{{ $campusImageUrl ? "background-image:url('{$campusImageUrl}')" : '' }}"></div>
                <div class="shade"></div>
                <div class="content">
                    <div class="brand">
                        <div class="brand-mark">
                            <img src="{{ $logoUrl }}" alt="Logo {{ $appName }}">
                        </div>
                        <h1>{{ $appName }}</h1>
                        <p>Sistem Ujian Tengah Semester Online yang modern, mudah, dan terpercaya.</p>
                    </div>
                    <small>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</small>
                </div>
            </section>

            <section class="right">
                <div class="head">
                    <h2>Selamat Datang Kembali</h2>
                    <p>Silakan masuk ke akun Anda</p>
                </div>

                @if (session('status'))
                    <div style="margin-top:16px;border:1px solid #a7f3d0;background:#ecfdf5;color:#065f46;border-radius:10px;padding:10px 12px;font-size:14px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="row">
                    @csrf
                    <div>
                        <label for="login">Email / NIM</label>
                        <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username" placeholder="Masukkan email atau NIM">
                        @error('login')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div style="margin-top:14px;">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                        @error('password')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="meta">
                        <label style="display:inline-flex;gap:8px;align-items:center;margin:0;font-size:16px;font-weight:500;">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit" class="submit">Masuk</button>
                </form>

                <a href="{{ url('/') }}" class="landing">Kembali ke Landing Page</a>
            </section>
        </div>
    </div>
</body>
</html>
