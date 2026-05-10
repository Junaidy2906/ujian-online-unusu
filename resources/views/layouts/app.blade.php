<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        @php
            $currentUser = auth()->user();
            $currentRole = $currentUser?->role ?? 'mahasiswa';

            $sidebarItems = match ($currentRole) {
                'admin' => [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
                    ['label' => 'Data Dosen', 'route' => 'admin.dosen.index', 'icon' => 'users'],
                    ['label' => 'Data Mahasiswa', 'route' => 'admin.mahasiswa.index', 'icon' => 'students'],
                    ['label' => 'Data Kelas', 'route' => 'admin.kelas.index', 'icon' => 'class'],
                    ['label' => 'Mata Kuliah', 'route' => 'admin.mata-kuliah.index', 'icon' => 'book'],
                    ['label' => 'Tahun Akademik', 'route' => 'admin.tahun-akademik.index', 'icon' => 'calendar'],
                    ['label' => 'Semester', 'route' => 'admin.semester.index', 'icon' => 'stack'],
                    ['label' => 'Rekap Nilai', 'route' => 'admin.laporan-nilai.index', 'icon' => 'chart'],
                ],
                'dosen' => [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
                    ['label' => 'Ujian Saya', 'route' => 'dosen.ujian.index', 'icon' => 'clipboard'],
                    ['label' => 'Buat Ujian', 'route' => 'dosen.ujian.create', 'icon' => 'plus'],
                    ['label' => 'Hasil Ujian', 'route' => 'dosen.ujian.index', 'icon' => 'chart'],
                    ['label' => 'Rekap Nilai', 'route' => 'dosen.laporan-nilai.index', 'icon' => 'chart'],
                    ['label' => 'Pengaturan', 'route' => 'profile.edit', 'icon' => 'gear'],
                ],
                default => [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
                    ['label' => 'Daftar Ujian', 'route' => 'mahasiswa.ujian.index', 'icon' => 'clipboard'],
                    ['label' => 'Riwayat Ujian', 'route' => 'mahasiswa.ujian.index', 'icon' => 'history'],
                    ['label' => 'Nilai Saya', 'route' => 'mahasiswa.ujian.index', 'icon' => 'score'],
                    ['label' => 'Pengaturan', 'route' => 'profile.edit', 'icon' => 'gear'],
                ],
            };

            $roleBadge = match ($currentRole) {
                'admin' => 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/20',
                'dosen' => 'bg-sky-500/15 text-sky-200 ring-sky-400/20',
                default => 'bg-amber-500/15 text-amber-200 ring-amber-400/20',
            };
        @endphp

        <div class="min-h-screen bg-slate-100">
            <div class="flex min-h-screen">
                <aside class="hidden w-72 flex-col bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white xl:flex">
                    <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-sm font-black text-amber-300 ring-1 ring-white/15">UTS</div>
                        <div>
                            <div class="text-sm font-semibold tracking-wide">UJIAN ONLINE UNUSU</div>
                            <div class="text-xs text-white/60">{{ ucfirst($currentRole) }} panel</div>
                        </div>
                    </div>

                    <div class="flex-1 px-4 py-5">
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.25em] text-white/45">Menu Utama</p>
                        <nav class="mt-4 space-y-2">
                            @foreach ($sidebarItems as $item)
                                @php
                                    $isActive = request()->routeIs($item['route']) || ($item['route'] === 'dashboard' && request()->routeIs('dashboard'));
                                @endphp
                                <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ $isActive ? 'bg-white/12 text-white shadow-lg ring-1 ring-white/10' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/8 ring-1 ring-white/10 group-hover:bg-white/12">
                                        @switch($item['icon'] ?? 'home')
                                            @case('users')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M17 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="11" cy="7" r="4"/></svg>
                                                @break
                                            @case('students')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M17 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><path d="M9 7l3 2 3-2"/><circle cx="11" cy="7" r="3"/></svg>
                                                @break
                                            @case('class')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><rect x="4" y="5" width="16" height="14" rx="2"/><path d="M8 9h8M8 13h8"/></svg>
                                                @break
                                            @case('book')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M5 4h10a2 2 0 0 1 2 2v14H7a2 2 0 0 1-2-2z"/><path d="M15 4v16"/></svg>
                                                @break
                                            @case('calendar')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
                                                @break
                                            @case('stack')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 16 9 5 9-5"/></svg>
                                                @break
                                            @case('clipboard')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><rect x="6" y="4" width="12" height="16" rx="2"/><path d="M9 4V2h6v2"/><path d="M9 10h6M9 14h6"/></svg>
                                                @break
                                            @case('plus')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M12 5v14M5 12h14"/></svg>
                                                @break
                                            @case('chart')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M4 19h16"/><path d="M7 17V10"/><path d="M12 17V6"/><path d="M17 17v-7"/></svg>
                                                @break
                                            @case('history')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                                                @break
                                            @case('score')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M12 3v18"/><path d="M5 7h14M5 12h14M5 17h14"/></svg>
                                                @break
                                            @case('gear')
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1 1 0 0 0 .2-1l-1-1.7a1 1 0 0 0-.9-.5l-1.7.2c-.3-.2-.5-.4-.8-.6l-.2-1.7a1 1 0 0 0-.5-.9l-1.7-1a1 1 0 0 0-1 0l-1.7 1a1 1 0 0 0-.5.9l-.2 1.7c-.3.2-.6.4-.8.6l-1.7-.2a1 1 0 0 0-.9.5l-1 1.7a1 1 0 0 0 .2 1l1.4 1.1c0 .3 0 .6 0 .9L4.7 18a1 1 0 0 0 .5.9l1.7 1a1 1 0 0 0 1 0l1.7-1c.3.2.5.4.8.6l.2 1.7a1 1 0 0 0 .5.9l1.7 1a1 1 0 0 0 1 0l1.7-1a1 1 0 0 0 .5-.9l.2-1.7c.3-.2.6-.4.8-.6l1.7.2a1 1 0 0 0 .9-.5l1-1.7a1 1 0 0 0-.2-1L19.4 15Z"/></svg>
                                                @break
                                            @default
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-2"><path d="M4 10.5 12 4l8 6.5V20H4z"/></svg>
                                        @endswitch
                                    </span>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>

                        <div class="mt-6 rounded-3xl border border-white/10 bg-white/8 p-4 shadow-2xl shadow-black/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/45">Sistem Ujian</p>
                            <p class="mt-3 text-lg font-semibold leading-snug text-white">Tengah Semester Online</p>
                            <p class="mt-2 text-sm leading-6 text-white/70">Kelola ujian, soal, jawaban, koreksi, dan rekap nilai dalam satu sistem.</p>
                        </div>
                    </div>
                </aside>

                <main class="flex-1">
                    <div class="border-b border-slate-200 bg-white/80 backdrop-blur-xl">
                        <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-white xl:hidden">UTS</div>
                                @isset($header)
                                    <div class="hidden md:block">
                                        {{ $header }}
                                    </div>
                                @else
                                    <div class="hidden md:block text-sm font-semibold text-slate-700">{{ config('app.name', 'UJIAN ONLINE UNUSU') }}</div>
                                @endisset
                            </div>

                            <div class="hidden flex-1 max-w-xl items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-500 shadow-sm md:flex">
                                <span class="text-slate-400">⌕</span>
                                <span>Cari sesuatu...</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <button class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm">
                                    <span>🔔</span>
                                    <span class="absolute right-1 top-1 h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                </button>

                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">{{ strtoupper(substr($currentUser?->name ?? 'U', 0, 1)) }}</div>
                                        <div class="hidden text-left sm:block">
                                            <div class="text-sm font-semibold text-slate-900">{{ $currentUser?->name }}</div>
                                            <div class="text-xs text-slate-500">{{ ucfirst($currentRole) }}</div>
                                        </div>
                                        <span class="text-slate-400">▾</span>
                                    </button>

                                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-20 mt-3 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="block w-full px-4 py-3 text-left text-sm text-red-600 hover:bg-red-50">Log Out</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-6 sm:px-6 lg:px-8">
                        @isset($header)
                            <div class="mb-6 rounded-[1.8rem] border border-slate-200 bg-white px-6 py-5 shadow-sm md:hidden">
                                {{ $header }}
                            </div>
                        @endisset

                        <div class="space-y-6">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
