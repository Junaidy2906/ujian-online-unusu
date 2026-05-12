<x-admin-layout :title="'Koreksi Essai'">
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ujian->nama_ujian }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $ujian->kelas?->nama_kelas }} - {{ $ujian->mataKuliah?->nama_mk }}</p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mahasiswa: {{ $percobaan->mahasiswa?->user?->name }} | Percobaan ke-{{ $percobaan->percobaan_ke }}</p>
    </div>

    <form method="POST" action="{{ route('dosen.ujian.hasil.koreksi-essay.simpan', [$ujian, $nilai]) }}" class="space-y-4">
        @csrf

        @forelse ($jawabanEssay as $jawaban)
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-700">Soal #{{ $jawaban->soal?->nomor }}</span>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Maks {{ rtrim(rtrim(number_format((float) ($jawaban->soal?->poin ?? 0), 2, '.', ''), '0'), '.') }} poin</span>
                </div>

                <div class="prose prose-sm max-w-none text-gray-900 dark:prose-invert">
                    <p><strong>Pertanyaan:</strong></p>
                    {!! $jawaban->soal?->pertanyaan !!}
                </div>

                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="mb-2 text-sm font-semibold text-slate-700">Jawaban Mahasiswa</p>
                    <div class="text-sm text-slate-700">{!! nl2br(e($jawaban->jawaban_text ?: '-')) !!}</div>
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Nilai Soal Ini</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="{{ (float) ($jawaban->soal?->poin ?? 0) }}"
                        name="nilai[{{ $jawaban->id }}]"
                        value="{{ old('nilai.'.$jawaban->id, $jawaban->nilai ?? 0) }}"
                        class="w-40 rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    >
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">
                Tidak ada jawaban essai untuk percobaan ini.
            </div>
        @endforelse

        <div class="sticky bottom-3 z-20 rounded-2xl border border-gray-200 bg-white/90 p-3 shadow-lg backdrop-blur dark:border-gray-700 dark:bg-gray-800/90">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('dosen.ujian.hasil', $ujian) }}" class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200">Kembali</a>
                <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-2.5 text-sm font-semibold text-amber-300 shadow-sm">Simpan Koreksi</button>
            </div>
        </div>
    </form>
</x-admin-layout>
