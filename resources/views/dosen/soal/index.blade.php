<x-admin-layout :title="'Bank Soal'">
    <div class="flex items-center justify-between gap-4 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Bank Soal: {{ $ujian->nama_ujian }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Tambahkan soal PG dan essai.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dosen.ujian.soal.import.form', $ujian) }}" class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">Import Massal</a>
            <a href="{{ route('dosen.ujian.soal.create', $ujian) }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Tambah Soal</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($items as $item)
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">{{ $item->tipe }}</div>
                        <div class="prose prose-sm mt-2 max-w-none text-gray-900 dark:prose-invert">{!! $item->pertanyaan !!}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dosen.ujian.soal.edit', [$ujian, $item]) }}" class="rounded-xl border border-blue-200 px-3 py-1.5 text-sm text-blue-600">Edit</a>
                        <form method="POST" action="{{ route('dosen.ujian.soal.destroy', [$ujian, $item]) }}" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf @method('DELETE')
                            <button class="rounded-xl border border-red-200 px-3 py-1.5 text-sm text-red-600">Hapus</button>
                        </form>
                    </div>
                </div>
                @if ($item->pilihanJawaban->count())
                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        @foreach ($item->pilihanJawaban as $option)
                            <div class="rounded-2xl bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:bg-gray-700/60 dark:text-gray-200">
                                <strong>{{ $option->kode }}.</strong> <span>{!! $option->jawaban !!}</span> {{ $option->is_benar ? '(Benar)' : '' }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">Belum ada soal.</div>
        @endforelse
    </div>
</x-admin-layout>
