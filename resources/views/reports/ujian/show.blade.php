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
            .print-card { box-shadow: none !important; border-color: #e5e7eb !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Halaman Cetak</p>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $title }}</h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ $printAllRoute }}" class="rounded-2xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Refresh</a>
                <button onclick="window.print()" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Cetak Semua</button>
            </div>
        </div>

        <section class="print-card rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $ujian->kelas?->nama_kelas }}</p>
                    <h2 class="mt-1 text-3xl font-semibold text-slate-900">{{ $ujian->nama_ujian }}</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $ujian->mataKuliah?->nama_mk }} • Dosen: {{ $ujian->dosen?->user?->name }}</p>
                </div>
                <div class="grid gap-2 text-sm text-slate-600 sm:text-right">
                    <div>Tanggal: {{ $ujian->jadwal_mulai?->format('d M Y H:i') }}</div>
                    <div>Durasi: {{ $ujian->durasi_menit }} menit</div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">No</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">NIM</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nama Mahasiswa</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nilai Akhir</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</th>
                            <th class="no-print px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($students as $student)
                            @php $score = $scores->get($student->id); @endphp
                            <tr>
                                <td class="px-5 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 text-sm font-medium text-slate-900">{{ $student->nim }}</td>
                                <td class="px-5 py-4 text-sm text-slate-700">{{ $student->user?->name }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $score?->nilai_akhir ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm">{{ $score?->status_penilaian ?? 'Belum dinilai' }}</td>
                                <td class="no-print px-5 py-4 text-right text-sm">
                                    <a href="{{ route($printStudentRouteName, [$ujian, $student]) }}" class="rounded-xl border border-slate-300 px-3 py-1.5 text-slate-700">Cetak</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada mahasiswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
