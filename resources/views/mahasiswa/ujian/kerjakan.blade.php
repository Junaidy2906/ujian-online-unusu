<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Kerjakan Ujian</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $percobaan->ujian?->nama_ujian }}</p>
            </div>
            <div id="timer" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300" data-seconds="{{ $remainingSeconds }}">--:--</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form id="exam-form" method="POST" action="{{ route('mahasiswa.ujian.submit', $percobaan) }}" class="space-y-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                @csrf
                @foreach ($soalItems as $soal)
                    <div class="rounded-3xl border border-gray-200 p-5 dark:border-gray-700">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Soal {{ $soal->nomor }} | {{ $soal->tipe }}</div>
                        <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $soal->pertanyaan }}</h3>

                        @if ($soal->tipe === 'pg')
                            <div class="mt-4 space-y-3">
                                @foreach ($soal->pilihanJawaban as $option)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:bg-gray-700/60 dark:text-gray-200">
                                        <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $option->id }}" class="text-amber-600 focus:ring-amber-500">
                                        <span>{{ $option->kode }}. {{ $option->jawaban }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <textarea name="jawaban[{{ $soal->id }}]" rows="5" class="mt-4 w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
                        @endif
                    </div>
                @endforeach

                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('mahasiswa.ujian.show', $percobaan->ujian) }}" class="rounded-2xl border border-gray-300 px-5 py-2 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">Keluar</a>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300">Submit Ujian</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const timerElement = document.getElementById('timer');
            const form = document.getElementById('exam-form');
            let remaining = Number(timerElement.dataset.seconds || 0);

            const render = () => {
                const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
                const seconds = String(remaining % 60).padStart(2, '0');
                timerElement.textContent = `${minutes}:${seconds}`;
            };

            render();
            if (remaining <= 0) {
                form.submit();
                return;
            }

            const interval = window.setInterval(() => {
                remaining -= 1;
                if (remaining <= 0) {
                    window.clearInterval(interval);
                    form.submit();
                    return;
                }
                render();
            }, 1000);
        })();
    </script>
</x-app-layout>
