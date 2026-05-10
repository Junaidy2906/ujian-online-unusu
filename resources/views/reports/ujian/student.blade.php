<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ config('app.name', 'UJIAN ONLINE UNUSU') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900">
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="no-print mb-6 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Cetak Per Mahasiswa</p>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $student->user?->name }}</h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ $printBackRoute }}" class="rounded-2xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                <button onclick="window.print()" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Cetak</button>
            </div>
        </div>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="border-b border-slate-200 pb-5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $ujian->kelas?->nama_kelas }}</p>
                <h2 class="mt-1 text-3xl font-semibold text-slate-900">{{ $ujian->nama_ujian }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $ujian->mataKuliah?->nama_mk }} • {{ $student->nim }}</p>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Mahasiswa</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ $student->user?->name }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">NIM</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ $student->nim }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kelas</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ $ujian->kelas?->nama_kelas }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ $scores->first()?->status_penilaian ?? 'Belum dinilai' }}</div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Percobaan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nilai PG</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nilai Essai</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nilai Akhir</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($scores as $score)
                            <tr>
                                <td class="px-5 py-4 text-sm text-slate-700">#{{ $score->percobaanUjian?->percobaan_ke ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $score->nilai_pg ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $score->nilai_essay ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $score->nilai_akhir ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm">{{ $score->status_penilaian }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada nilai untuk mahasiswa ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
