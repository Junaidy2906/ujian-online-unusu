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
    <title>{{ $appName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f6f9; color: #0f172a; }
        .wrap { max-width: 1360px; margin: 0 auto; padding: clamp(10px, 2vw, 18px); }
        .hero { position: relative; min-height: min(78vh, 760px); border-radius: 22px; overflow: hidden; background: linear-gradient(140deg, #dbeafe, #ecfeff); border: 1px solid #dbe3ee; }
        .bg { position: absolute; inset: 0; background-size: cover; background-position: center; }
        .bg::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.78) 42%, rgba(255,255,255,0.08) 72%, rgba(255,255,255,0.04) 100%); }
        .content { position: relative; z-index: 2; padding: 18px 26px 26px; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 16px; background: #ffffffd1; backdrop-filter: blur(4px); border: 1px solid #e2e8f0; }
        .brand { display: inline-flex; align-items: center; gap: 10px; font-size: clamp(16px, 2.4vw, 30px); font-weight: 800; line-height: 1.1; }
        .brand img { width: clamp(36px, 5vw, 54px); height: clamp(36px, 5vw, 54px); object-fit: contain; }
        .login-btn { background: #0a7a3f; color: #fff; font-weight: 700; text-decoration: none; border-radius: 999px; padding: 11px 30px; }
        .grid { margin-top: 34px; display: grid; grid-template-columns: minmax(0, 470px) 1fr; gap: 12px; align-items: end; }
        h1 { margin: 0; font-size: clamp(2rem, 4.2vw, 4rem); line-height: 1.1; color: #0b4f2f; }
        .subtitle { margin-top: 16px; color: #334155; line-height: 1.65; font-size: clamp(15px, 2vw, 28px); }
        .cta-row { margin-top: 24px; display: grid; gap: 10px; max-width: 430px; }
        .cta { display: block; border-radius: 12px; padding: 16px 18px; font-weight: 700; text-decoration: none; border: 1px solid #cbd5e1; background: #fff; color: #14532d; }
        .cta.primary { background: #0a7a3f; color: white; border-color: #0a7a3f; }
        .features { margin-top: 24px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; background: #065f46; border-radius: 16px; padding: 14px; color: #ecfdf5; }
        .feature { background: #047857; border-radius: 12px; padding: 12px; }
        .feature b { display: block; margin-bottom: 6px; }
        @media (max-width: 1050px) {
            .hero { min-height: auto; }
            .grid { grid-template-columns: 1fr; }
            .features { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 720px) {
            .top { flex-wrap: wrap; }
            .login-btn { width: 100%; text-align: center; padding: 11px 16px; }
            .content { padding: 12px; }
            .cta-row { max-width: 100%; }
            .features { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="hero">
            <div class="bg" style="{{ $campusImageUrl ? "background-image:url('{$campusImageUrl}')" : '' }}"></div>
            <div class="content">
                <div class="top">
                    <div class="brand">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo {{ $appName }}">
                        @endif
                        <span>{{ $appName }}</span>
                    </div>
                    @if (Route::has('login'))
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="login-btn">{{ auth()->check() ? 'Dashboard' : 'Login' }}</a>
                    @endif
                </div>

                <div class="grid">
                    <div>
                        <h1>UJIAN Online<br>Management System</h1>
                        <p class="subtitle">Sistem Ujian Tengah Semester Online yang modern, mudah, dan terpercaya.</p>
                        <div class="cta-row">
                            <a href="{{ route('login') }}" class="cta primary">Login Mahasiswa</a>
                            <a href="{{ route('login') }}" class="cta">Login Dosen</a>
                            <a href="{{ route('login') }}" class="cta">Login Admin</a>
                        </div>
                    </div>
                </div>

                <div class="features">
                    <div class="feature"><b>Ujian Online</b>Kerjakan ujian kapan saja sesuai jadwal.</div>
                    <div class="feature"><b>Aman</b>Data tersimpan rapi dan terstruktur.</div>
                    <div class="feature"><b>Realtime</b>Hasil ujian dapat dipantau cepat.</div>
                    <div class="feature"><b>Mudah</b>Tampilan sederhana dan mudah dipakai.</div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
