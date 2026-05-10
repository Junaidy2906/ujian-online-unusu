<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Hasil Ujian</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $percobaan->ujian?->nama_ujian }}</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $percobaan->ujian?->nama_ujian }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $percobaan->ujian?->kelas?->nama_kelas }}</p>
                    </div>
                    <a href="{{ route('mahasiswa.ujian.cetak', $percobaan) }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Cetak Hasil</a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-700/60">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Nilai Akhir</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $nilai?->nilai_akhir ?? $percobaan->nilai_akhir ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-700/60">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Status</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $nilai?->status_penilaian ?? $percobaan->status }}</div>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-gray-200 p-4 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-200">
                    @if ($nilai?->status_penilaian === 'menunggu_koreksi')
                        Nilai essai masih menunggu koreksi dosen. Nilai akhir bisa berubah setelah koreksi selesai.
                    @else
                        Nilai Anda sudah tersimpan.
                    @endif
                </div>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('mahasiswa.ujian.index') }}" class="rounded-2xl border border-gray-300 px-5 py-2 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">Kembali ke Daftar Ujian</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>