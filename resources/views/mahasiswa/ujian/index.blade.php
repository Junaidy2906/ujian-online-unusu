<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Daftar Ujian</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">UTS aktif untuk kelas Anda.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            @forelse ($items as $item)
                @php
                    $isNotStarted = $item->jadwal_mulai && now()->lessThan($item->jadwal_mulai);
                    $isExpired = $item->jadwal_selesai && now()->greaterThan($item->jadwal_selesai);
                @endphp

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $item->nama_ujian }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->kelas?->nama_kelas }} - {{ $item->mataKuliah?->nama_mk }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai: {{ $item->jadwal_mulai?->format('d M Y H:i') }}</p>
                            @if ($item->jadwal_selesai)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selesai: {{ $item->jadwal_selesai?->format('d M Y H:i') }}</p>
                            @endif

                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                @if ($isExpired)
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Waktu Habis</span>
                                @elseif ($isNotStarted)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Belum Mulai</span>
                                    <span
                                        class="countdown inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                                        data-start-at="{{ $item->jadwal_mulai?->toIso8601String() }}"
                                    >Mulai dalam: --:--:--</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Sedang Berjalan</span>
                                @endif
                            </div>
                        </div>

                        @if ($isExpired)
                            <span class="cursor-not-allowed rounded-2xl bg-red-100 px-4 py-2 text-sm font-semibold text-red-500">Waktu Habis</span>
                        @elseif ($isNotStarted)
                            <span class="cursor-not-allowed rounded-2xl bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">Menunggu Mulai</span>
                        @else
                            <a href="{{ route('mahasiswa.ujian.show', $item) }}" class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Lihat Ujian</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">Belum ada ujian aktif.</div>
            @endforelse
        </div>
    </div>

    <script>
        (() => {
            const nodes = Array.from(document.querySelectorAll('.countdown[data-start-at]'));
            if (!nodes.length) return;

            const formatDuration = (totalSeconds) => {
                const s = Math.max(0, totalSeconds);
                const h = String(Math.floor(s / 3600)).padStart(2, '0');
                const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
                const sec = String(s % 60).padStart(2, '0');
                return `${h}:${m}:${sec}`;
            };

            const tick = () => {
                const now = Date.now();

                nodes.forEach((node) => {
                    const startAt = new Date(node.dataset.startAt).getTime();
                    if (Number.isNaN(startAt)) return;

                    const diffSeconds = Math.floor((startAt - now) / 1000);

                    if (diffSeconds <= 0) {
                        node.textContent = 'Ujian sudah dimulai, silakan refresh halaman.';
                        node.classList.remove('bg-slate-100', 'text-slate-700');
                        node.classList.add('bg-emerald-100', 'text-emerald-700');
                        return;
                    }

                    node.textContent = `Mulai dalam: ${formatDuration(diffSeconds)}`;
                });
            };

            tick();
            setInterval(tick, 1000);
        })();
    </script>
</x-app-layout>
