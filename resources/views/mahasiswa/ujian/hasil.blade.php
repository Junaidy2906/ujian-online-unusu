<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Hasil Ujian</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $percobaan->ujian?->nama_ujian }}</p>
        </div>
    </x-slot>

    @php
        $nilaiAkhir = $nilai?->nilai_akhir ?? $percobaan->nilai_akhir;
        $nilaiPg = $nilai?->nilai_pg ?? $percobaan->nilai_pg;
        $nilaiEssay = $nilai?->nilai_essay ?? $percobaan->nilai_essay;
        $statusPenilaian = $nilai?->status_penilaian ?? $percobaan->status;
        $isLulus = (bool) ($nilai?->status_lulus ?? $percobaan->is_lulus);
        $statusLabel = match ($statusPenilaian) {
            'menunggu_koreksi' => 'Menunggu Koreksi',
            'selesai' => 'Selesai',
            default => ucfirst((string) $statusPenilaian),
        };
    @endphp

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-[2rem] border border-gray-200 bg-gradient-to-br from-white via-sky-50 to-emerald-50 p-6 shadow-sm dark:border-gray-700 dark:from-gray-800 dark:via-gray-800 dark:to-gray-900">
                <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-blue-300/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-16 h-52 w-52 rounded-full bg-emerald-300/20 blur-3xl"></div>

                <div class="relative flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600 dark:text-blue-300">Ringkasan Hasil</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $percobaan->ujian?->nama_ujian }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $percobaan->ujian?->kelas?->nama_kelas }} • {{ $percobaan->ujian?->mataKuliah?->nama_mk }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusPenilaian === 'menunggu_koreksi' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $statusLabel }}
                        </span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $isLulus ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $isLulus ? 'LULUS' : 'BELUM LULUS' }}
                        </span>
                    </div>
                </div>

                <div class="relative mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/70 bg-white/80 p-4 backdrop-blur dark:border-gray-700 dark:bg-gray-800/80">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai Akhir</p>
                        <p class="mt-1 text-4xl font-bold text-gray-900 dark:text-gray-100">{{ $nilaiAkhir !== null ? number_format((float) $nilaiAkhir, 2) : '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 p-4 backdrop-blur dark:border-gray-700 dark:bg-gray-800/80">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai PG</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $nilaiPg !== null ? number_format((float) $nilaiPg, 2) : '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 p-4 backdrop-blur dark:border-gray-700 dark:bg-gray-800/80">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai Essai</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $nilaiEssay !== null ? number_format((float) $nilaiEssay, 2) : '-' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Keterangan</h4>
                <div class="mt-3 rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-700/40 dark:text-gray-200">
                    @if ($statusPenilaian === 'menunggu_koreksi')
                        Nilai essai masih menunggu koreksi dosen. Nilai akhir dapat berubah setelah proses koreksi selesai.
                    @else
                        Nilai Anda sudah tersimpan.
                    @endif

                    @if (! empty($nilai?->catatan))
                        <div class="mt-2 border-t border-gray-200 pt-2 text-xs text-gray-500 dark:border-gray-600 dark:text-gray-300">
                            Catatan: {{ $nilai->catatan }}
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('mahasiswa.ujian.index') }}" class="rounded-2xl border border-gray-300 px-5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Kembali ke Daftar Ujian</a>
                    <a href="{{ route('mahasiswa.ujian.cetak', $percobaan) }}" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300 hover:bg-slate-800">Cetak Hasil</a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
