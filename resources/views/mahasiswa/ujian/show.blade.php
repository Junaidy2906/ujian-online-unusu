<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail Ujian</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ujian->nama_ujian }}</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ujian->kelas?->nama_kelas }} • {{ $ujian->mataKuliah?->nama_mk }}</p>
                <div class="mt-4 grid gap-3 text-sm text-gray-700 dark:text-gray-200 sm:grid-cols-2">
                    <div>Durasi: {{ $ujian->durasi_menit }} menit</div>
                    <div>Batas lulus: {{ $ujian->nilai_minimum_lulus }}</div>
                    <div>Max percobaan: {{ $allowedAttempts ?? $ujian->maksimal_percobaan }}</div>
                    <div>Percobaan Anda: {{ $attemptCount }}</div>
                </div>
                @if ($lastScore)
                    <div class="mt-4 rounded-2xl bg-gray-50 p-4 text-sm text-gray-700 dark:bg-gray-700/60 dark:text-gray-200">
                        Nilai terakhir: {{ $lastScore->nilai_akhir ?? '-' }} | Status: {{ $lastScore->status_penilaian }}
                    </div>
                @endif
                <div class="mt-6 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('mahasiswa.ujian.start', $ujian) }}">
                        @csrf
                        <button class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300">Mulai Ujian</button>
                    </form>
                    <a href="{{ route('mahasiswa.ujian.index') }}" class="rounded-2xl border border-gray-300 px-5 py-2 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
