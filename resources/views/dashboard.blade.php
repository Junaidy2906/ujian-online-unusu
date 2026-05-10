<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'UJIAN ONLINE UNUSU') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
@php
    $sidebarItems = match ($role) {
        \App\Models\User::ROLE_ADMIN => [
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Data Dosen', 'route' => 'admin.dosen.index'],
            ['label' => 'Data Mahasiswa', 'route' => 'admin.mahasiswa.index'],
            ['label' => 'Data Kelas', 'route' => 'admin.kelas.index'],
            ['label' => 'Mata Kuliah', 'route' => 'admin.mata-kuliah.index'],
            ['label' => 'Tahun Akademik', 'route' => 'admin.tahun-akademik.index'],
            ['label' => 'Semester', 'route' => 'admin.semester.index'],
        ],
        \App\Models\User::ROLE_DOSEN => [
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Ujian Saya', 'route' => 'dosen.ujian.index'],
            ['label' => 'Buat Ujian', 'route' => 'dosen.ujian.create'],
            ['label' => 'Hasil Ujian', 'route' => 'dosen.ujian.index'],
            ['label' => 'Rekap Nilai', 'route' => 'dosen.ujian.index'],
            ['label' => 'Pengaturan', 'route' => 'profile.edit'],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Daftar Ujian', 'route' => 'mahasiswa.ujian.index'],
            ['label' => 'Riwayat Ujian', 'route' => 'mahasiswa.ujian.index'],
            ['label' => 'Nilai Saya', 'route' => 'mahasiswa.ujian.index'],
            ['label' => 'Pengaturan', 'route' => 'profile.edit'],
        ],
    };

    $roleAccent = match ($role) {
        \App\Models\User::ROLE_ADMIN => [
            'bg' => 'from-emerald-950 via-emerald-900 to-slate-950',
            'pill' => 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/30',
            'button' => 'bg-emerald-500 text-emerald-950',
        ],
        \App\Models\User::ROLE_DOSEN => [
            'bg' => 'from-blue-950 via-sky-900 to-slate-950',
            'pill' => 'bg-sky-500/15 text-sky-200 ring-sky-400/30',
            'button' => 'bg-sky-400 text-slate-950',
        ],
        default => [
            'bg' => 'from-amber-950 via-amber-900 to-slate-950',
            'pill' => 'bg-amber-500/15 text-amber-200 ring-amber-400/30',
            'button' => 'bg-amber-400 text-slate-950',
        ],
    };

    $totalBars = collect($chart['bars'] ?? [])->sum('value');
    $palette = ['#22c55e', '#38bdf8', '#f59e0b', '#ef4444'];
    $cursor = 0;
    $gradientParts = [];

    foreach (collect($chart['bars'] ?? [])->values() as $index => $bar) {
        $percent = $totalBars > 0 ? (($bar['value'] / $totalBars) * 100) : 0;
        $start = $cursor;
        $end = $cursor + $percent;
        $gradientParts[] = $palette[$index % count($palette)] . ' ' . number_format($start, 2) . '% ' . number_format($end, 2) . '%';
        $cursor = $end;
    }

    $donutStyle = $gradientParts !== []
        ? 'background: conic-gradient(' . implode(', ', $gradientParts) . ');'
        : 'background: conic-gradient(#e2e8f0 0% 100%);';

    $maxBar = max(1, (int) collect($chart['bars'] ?? [])->max('value'));
    $quickLinks = collect($quickActions ?? [])->map(function ($item) {
        if (! isset($item['route']) || ! \Illuminate\Support\Facades\Route::has($item['route'])) {
            return null;
        }

        return ['label' => $item['label'], 'url' => route($item['route'])];
    })->filter()->values();
@endphp

<div class="min-h-screen bg-slate-100 text-slate-900">
    <div class="flex min-h-screen">
        <aside class="hidden w-72 flex-col bg-gradient-to-b {{ $roleAccent['bg'] }} text-white xl:flex">
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-sm font-black text-amber-300 ring-1 ring-white/15">UTS</div>
                <div>
                    <div class="text-sm font-semibold tracking-wide">UJIAN ONLINE UNUSU</div>
                    <div class="text-xs text-white/60">{{ $roleLabel }} panel</div>
                </div>
            </div>

            <div class="flex-1 px-4 py-5">
                <p class="px-3 text-xs font-semibold uppercase tracking-[0.25em] text-white/45">Menu Utama</p>
                <nav class="mt-4 space-y-2">
                    @foreach ($sidebarItems as $item)
                        <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs($item['route']) || ($item['route'] === 'dashboard' && request()->routeIs('dashboard')) ? 'bg-white/12 text-white shadow-lg ring-1 ring-white/10' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/8 text-xs font-black ring-1 ring-white/10 group-hover:bg-white/12">{{ strtoupper(substr($item['label'], 0, 1)) }}</span>
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
                        <button type="button" class="hidden rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 shadow-sm xl:inline-flex">
                            <span class="mr-2">☰</span> Dashboard {{ $roleLabel }}
                        </button>
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
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">{{ strtoupper(substr($roleLabel, 0, 1)) }}</div>
                                <div class="hidden text-left sm:block">
                                    <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $roleLabel }}</div>
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
                <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                    <div class="grid gap-8 p-6 xl:grid-cols-[1.45fr_0.95fr] xl:p-8">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">{{ $heroBadge }}</p>
                            <h1 class="mt-3 max-w-3xl text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                                Selamat datang kembali,
                                <span class="text-slate-950">{{ auth()->user()->name }}</span> 👋
                            </h1>
                            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">{{ $heroText }}</p>

                            <div class="mt-6 flex flex-wrap gap-3">
                                @foreach ($quickLinks as $link)
                                    <a href="{{ $link['url'] }}" class="rounded-2xl px-4 py-2 text-sm font-semibold text-white shadow-sm {{ $roleAccent['button'] }}">
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-emerald-50 via-white to-slate-50 p-6 ring-1 ring-slate-200">
                            <div class="absolute -right-8 -top-10 h-40 w-40 rounded-full bg-emerald-200/50 blur-3xl"></div>
                            <div class="absolute -bottom-6 left-4 h-28 w-28 rounded-full bg-sky-200/50 blur-3xl"></div>

                            <div class="relative flex h-full min-h-[220px] items-end justify-between gap-4">
                                <div class="space-y-3">
                                    <div class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white shadow-sm">{{ $roleLabel }} mode</div>
                                    <div class="max-w-xs rounded-3xl border border-slate-200 bg-white p-4 shadow-lg">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Fokus hari ini</p>
                                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $chart['title'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">Pantau distribusi nilai dan progres aktivitas ujian.</p>
                                    </div>
                                </div>

                                <div class="relative flex h-48 w-48 items-center justify-center">
                                    <div class="absolute inset-0 rounded-full bg-slate-900/5"></div>
                                    <div class="absolute inset-7 rounded-full bg-white shadow-inner ring-1 ring-slate-200"></div>
                                    <div class="absolute inset-12 rounded-full" style="{{ $donutStyle }}"></div>
                                    <div class="absolute inset-[3.8rem] rounded-full bg-white"></div>
                                    <div class="absolute inset-0 flex items-center justify-center text-center">
                                        <div>
                                            <div class="text-2xl font-bold text-slate-900">{{ $totalBars }}</div>
                                            <div class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Total Data</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($summaryCards as $card)
                        <article class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $card['value'] }}</p>
                                </div>
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">◌</div>
                            </div>
                            <p class="mt-3 text-sm text-slate-500">{{ $card['note'] }}</p>
                        </article>
                    @endforeach
                </section>

                <section class="mt-6 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <article class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $listTitle }}</h3>
                                <p class="mt-1 text-sm text-slate-500">Ringkasan yang paling relevan untuk role ini.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $roleLabel }}</span>
                        </div>

                        <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200">
                            <div class="grid grid-cols-[1fr_auto] bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 sm:px-5">
                                <div>Informasi</div>
                                <div>Status</div>
                            </div>
                            <div class="divide-y divide-slate-200 bg-white">
                                @foreach ($listItems as $item)
                                    <div class="grid grid-cols-1 gap-2 px-4 py-4 text-sm text-slate-700 sm:grid-cols-[1fr_auto] sm:items-center sm:px-5">
                                        <div class="pr-0 leading-6 sm:pr-4">{{ $item }}</div>
                                        <div class="justify-self-start rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>

                    <article class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $chart['title'] }}</h3>
                        <p class="mt-1 text-sm text-slate-500">Distribusi nilai berdasarkan rentang skor.</p>

                        <div class="mt-6 flex flex-col items-start gap-5 sm:flex-row sm:items-center sm:gap-6">
                            <div class="relative mx-auto flex h-40 w-40 items-center justify-center rounded-full sm:mx-0 sm:h-44 sm:w-44" style="{{ $donutStyle }}">
                                <div class="h-24 w-24 rounded-full bg-white shadow-inner ring-1 ring-slate-200"></div>
                                <div class="absolute inset-0 rounded-full ring-1 ring-black/5"></div>
                            </div>

                            <div class="flex-1 space-y-3">
                                @foreach ($chart['bars'] as $bar)
                                    <div class="flex items-center gap-3">
                                        <span class="h-3 w-3 rounded-full" style="background: {{ $palette[$loop->index % count($palette)] }}"></span>
                                        <div class="flex-1 text-sm text-slate-600">{{ $bar['label'] }}</div>
                                        <div class="text-sm font-semibold text-slate-900">{{ $bar['value'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            @foreach ($statusItems as $item)
                                <div>
                                    <div class="mb-2 flex items-center justify-between text-sm">
                                        <span class="font-medium text-slate-600">{{ $item['label'] }}</span>
                                        <span class="font-semibold text-slate-900">{{ $item['value'] }}</span>
                                    </div>
                                    <div class="h-3 rounded-full bg-slate-100">
                                        <div class="h-3 rounded-full bg-gradient-to-r from-emerald-500 to-sky-500" style="width: {{ min(100, max(10, (int) $item['value'] * 10)) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </section>
            </div>
        </main>
    </div>
</div>
</body>
</html>
