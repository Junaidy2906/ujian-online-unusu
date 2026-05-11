<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Kerjakan Ujian</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $percobaan->ujian?->nama_ujian }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 pb-32 md:pb-8" x-data="examApp({ total: {{ $soalItems->count() }} })">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form id="exam-form" method="POST" action="{{ route('mahasiswa.ujian.submit', $percobaan) }}" class="space-y-5">
                @csrf

                <div class="grid gap-5 lg:grid-cols-[1fr_280px]">
                    <section class="space-y-5">
                        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                        <svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-2"><path d="M9 4h6"/><path d="M10 2h4v4h-4z"/><rect x="5" y="4" width="14" height="18" rx="2"/><path d="M9 11h6M9 15h4"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Soal <span x-text="current + 1"></span> dari {{ $soalItems->count() }}</h3>
                                        <div class="mt-2 h-2 w-56 rounded-full bg-gray-200 dark:bg-gray-700">
                                            <div class="h-2 rounded-full bg-blue-600 transition-all" :style="`width: ${progress}%`"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-blue-600" x-text="`${progress}%`"></span>
                            </div>
                        </div>

                        @foreach ($soalItems as $index => $soal)
                            <article
                                x-show="current === {{ $index }}"
                                x-transition:enter="transition ease-out duration-250"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                            >
                                <div class="mb-4 flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg bg-blue-600 px-3 py-1 text-xs font-semibold text-white">SOAL {{ $soal->nomor }}</span>
                                    <span class="rounded-lg bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ strtoupper($soal->tipe) }}</span>
                                    <span class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-100">Bobot: {{ $soal->poin }}</span>
                                </div>

                                <div class="prose prose-sm max-w-none text-gray-900 dark:prose-invert">{!! $soal->pertanyaan !!}</div>

                                @if ($soal->tipe === 'pg')
                                    <div class="mt-5 space-y-3">
                                        @foreach ($soal->pilihanJawaban as $option)
                                            <label
                                                class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-4 text-sm transition hover:border-blue-300 hover:bg-blue-50/40 dark:border-gray-700 dark:hover:bg-gray-700/40"
                                                :class="{ 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-900/20': answers[{{ $soal->id }}] == '{{ $option->id }}' }"
                                            >
                                                <input
                                                    type="radio"
                                                    name="jawaban[{{ $soal->id }}]"
                                                    value="{{ $option->id }}"
                                                    class="text-emerald-600 focus:ring-emerald-500"
                                                    x-model="answers[{{ $soal->id }}]"
                                                    @change="markAnswered({{ $index }})"
                                                >
                                                <span><strong>{{ $option->kode }}.</strong> {!! $option->jawaban !!}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <textarea
                                        name="jawaban[{{ $soal->id }}]"
                                        rows="6"
                                        class="mt-5 w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                        x-model="answers[{{ $soal->id }}]"
                                        @input="markAnswered({{ $index }})"
                                    ></textarea>
                                @endif

                                <div class="mt-6 rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:bg-blue-900/20 dark:text-blue-200">
                                    Pilih satu jawaban yang paling tepat.
                                </div>
                            </article>
                        @endforeach

                        <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <button type="button" class="rounded-2xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200" @click="prev()">Sebelumnya</button>
                                <button type="button" class="rounded-2xl border border-blue-200 px-5 py-2.5 text-sm font-semibold text-blue-700" @click="saved = true; setTimeout(() => saved = false, 1200)">Simpan Jawaban</button>
                                <button type="button" class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-semibold text-amber-300" @click="next()">Selanjutnya</button>
                            </div>
                            <p x-show="saved" x-transition class="mt-3 text-sm font-medium text-emerald-600">Jawaban tersimpan di form.</p>
                            <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                                <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Submit Ujian</button>
                            </div>
                        </div>
                    </section>

                    <aside class="space-y-4">
                        <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h4 class="mb-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Daftar Soal</h4>
                            <div class="grid grid-cols-5 gap-2">
                                @foreach ($soalItems as $index => $soal)
                                    <button
                                        type="button"
                                        class="h-10 rounded-lg border text-sm font-semibold transition"
                                        :class="navClass({{ $index }})"
                                        @click="go({{ $index }})"
                                    >
                                        {{ $soal->nomor }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mt-5 rounded-xl bg-gray-50 p-3 text-sm dark:bg-gray-700/40">
                                <p class="mb-2 font-semibold text-gray-700 dark:text-gray-100">Keterangan</p>
                                <div class="space-y-1 text-gray-600 dark:text-gray-300">
                                    <p><span class="inline-block h-3 w-3 rounded bg-blue-600"></span> Soal Aktif</p>
                                    <p><span class="inline-block h-3 w-3 rounded bg-emerald-500"></span> Sudah Dijawab</p>
                                    <p><span class="inline-block h-3 w-3 rounded border border-gray-300 bg-white"></span> Belum Dijawab</p>
                                </div>
                            </div>

                            <div id="timer"
                                class="mt-4 rounded-3xl border border-blue-900/60 bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 px-5 py-4 text-amber-300 shadow-lg"
                                data-seconds="{{ (int) $remainingSeconds }}">
                                <div class="mb-2 flex items-center justify-between">
                                    <p class="text-xs font-semibold tracking-[0.18em] text-blue-200/90">SISA WAKTU</p>
                                    <span id="timer-state" class="rounded-full bg-blue-500/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-blue-200">Aman</span>
                                </div>
                                <p id="timer-text" class="text-3xl font-extrabold leading-none tabular-nums">--:--:--</p>
                                <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/15">
                                    <div id="timer-progress" class="h-full w-full rounded-full bg-gradient-to-r from-emerald-300 via-amber-300 to-rose-400 transition-all duration-700"></div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        </div>

        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 bg-white/95 p-3 backdrop-blur md:hidden dark:border-gray-700 dark:bg-gray-900/95">
            <div class="mx-auto max-w-7xl">
                <div class="mb-3 overflow-x-auto pb-1">
                    <div class="flex min-w-max gap-2">
                        @foreach ($soalItems as $index => $soal)
                            <button
                                type="button"
                                class="h-9 min-w-9 rounded-lg border px-3 text-sm font-semibold transition"
                                :class="navClass({{ $index }})"
                                @click="go({{ $index }})"
                            >
                                {{ $soal->nomor }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" class="rounded-xl border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200" @click="prev()">Sebelumnya</button>
                    <button type="button" class="rounded-xl bg-slate-950 px-3 py-2 text-sm font-semibold text-amber-300" @click="next()">Selanjutnya</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function examApp(config) {
            return {
                total: config.total,
                current: 0,
                saved: false,
                visited: { 0: true },
                answered: {},
                answers: {},
                get progress() {
                    return Math.max(5, Math.round(((this.current + 1) / this.total) * 100));
                },
                go(idx) {
                    this.current = idx;
                    this.visited[idx] = true;
                },
                next() {
                    if (this.current < this.total - 1) {
                        this.go(this.current + 1);
                    }
                },
                prev() {
                    if (this.current > 0) {
                        this.go(this.current - 1);
                    }
                },
                markAnswered(idx) {
                    this.answered[idx] = true;
                },
                navClass(idx) {
                    if (this.current === idx) return 'border-blue-600 bg-blue-600 text-white';
                    if (this.answered[idx]) return 'border-emerald-500 bg-emerald-100 text-emerald-700';
                    return 'border-gray-300 bg-white text-gray-700 hover:border-blue-300';
                }
            };
        }

        (function () {
            const timerElement = document.getElementById('timer');
            const timerText = document.getElementById('timer-text');
            const timerState = document.getElementById('timer-state');
            const timerProgress = document.getElementById('timer-progress');
            const form = document.getElementById('exam-form');
            if (!timerElement || !timerText || !timerState || !timerProgress || !form) return;
            const initial = Math.max(0, parseInt(timerElement.dataset.seconds || '0', 10));
            let remaining = initial;

            const render = () => {
                const total = Math.max(0, Math.floor(remaining));
                const hours = String(Math.floor(total / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
                const seconds = String(total % 60).padStart(2, '0');
                timerText.textContent = `${hours}:${minutes}:${seconds}`;

                const ratio = initial > 0 ? Math.max(0, (total / initial) * 100) : 0;
                timerProgress.style.width = `${ratio}%`;

                if (total <= 300) {
                    timerState.textContent = 'Kritis';
                    timerState.className = 'rounded-full bg-rose-500/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-rose-200';
                } else if (total <= 900) {
                    timerState.textContent = 'Waspada';
                    timerState.className = 'rounded-full bg-amber-500/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-amber-200';
                } else {
                    timerState.textContent = 'Aman';
                    timerState.className = 'rounded-full bg-blue-500/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-blue-200';
                }
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
