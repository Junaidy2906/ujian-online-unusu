<x-admin-layout :title="'Bank Soal'">
    @php
        $q = $filters['q'] ?? '';
        $tipe = $filters['tipe'] ?? 'semua';
        $totalSoal = $items->count();
        $totalPg = $items->where('tipe', 'pg')->count();
        $totalEssay = $items->where('tipe', 'essay')->count();
        $totalPoin = $items->sum('poin');
    @endphp

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-300/90">Manajemen Bank Soal</p>
                <h3 class="mt-2 text-xl font-semibold text-white">Bank Soal: {{ $ujian->nama_ujian }}</h3>
                <p class="mt-1 text-sm text-slate-300">Tambah, susun, dan cek kualitas soal PG maupun essai.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dosen.ujian.soal.import.form', $ujian) }}" class="rounded-2xl border border-slate-500/70 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">Import Massal</a>
                <a href="{{ route('dosen.ujian.soal.create', $ujian) }}" class="rounded-2xl bg-amber-300 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-amber-200">Tambah Soal</a>
            </div>
        </div>
        <div class="mt-5 grid gap-3 md:grid-cols-4">
            <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-300">Total Soal</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ $totalSoal }}</p>
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-300">Pilihan Ganda</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ $totalPg }}</p>
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-300">Essai</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ $totalEssay }}</p>
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-300">Total Poin</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ rtrim(rtrim(number_format((float) $totalPoin, 2, '.', ''), '0'), '.') }}</p>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('dosen.ujian.soal.index', $ujian) }}" class="mt-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Cari Soal</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari pertanyaan atau kunci jawaban..." class="w-full rounded-2xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Filter Tipe</label>
                <select name="tipe" class="w-full rounded-2xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                    <option value="semua" @selected($tipe === 'semua')>Semua</option>
                    <option value="pg" @selected($tipe === 'pg')>Pilihan Ganda</option>
                    <option value="essay" @selected($tipe === 'essay')>Essai</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-amber-300">Terapkan</button>
                <a href="{{ route('dosen.ujian.soal.index', $ujian) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700">Reset</a>
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500">Tip: seret kartu soal untuk ubah urutan nomor secara otomatis.</p>
    </form>

    <form method="POST" action="{{ route('dosen.ujian.soal.poin.bulk', $ujian) }}" class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
        @csrf
        <div class="grid gap-3 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Poin Massal</label>
                <input type="number" name="poin_massal" step="0.01" min="0" placeholder="Contoh: 1" class="w-full rounded-2xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Terapkan ke</label>
                <select name="tipe" class="w-full rounded-2xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                    <option value="semua">Semua Soal</option>
                    <option value="pg">Hanya Pilihan Ganda</option>
                    <option value="essay">Hanya Essai</option>
                </select>
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-amber-300">Set Poin Massal (Select All)</button>
            </div>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($items as $item)
            <div class="soal-card rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                draggable="true"
                data-id="{{ $item->id }}"
                data-nomor="{{ $item->nomor }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="w-full">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <span class="drag-handle cursor-grab rounded-full border border-slate-300 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-600" title="Geser untuk urutkan">&#x2630; Urutkan</span>
                            <span class="soal-nomor rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-amber-300">Soal #{{ $item->nomor }}</span>
                            <span class="rounded-full border border-slate-300 bg-slate-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-700">{{ $item->tipe === 'pg' ? 'Pilihan Ganda' : 'Essai' }}</span>
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Poin {{ rtrim(rtrim(number_format((float) $item->poin, 2, '.', ''), '0'), '.') }}</span>
                        </div>
                        <div class="prose prose-sm max-w-none text-gray-900 dark:prose-invert">{!! $item->pertanyaan !!}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('dosen.ujian.soal.poin.update', [$ujian, $item]) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="poin" step="0.01" min="0" value="{{ rtrim(rtrim(number_format((float) $item->poin, 2, '.', ''), '0'), '.') }}" class="w-24 rounded-xl border-slate-300 px-3 py-1.5 text-sm focus:border-amber-500 focus:ring-amber-500">
                            <button class="rounded-xl border border-emerald-200 px-3 py-1.5 text-sm text-emerald-700">Simpan Poin</button>
                        </form>
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
                            <div class="rounded-2xl border {{ $option->is_benar ? 'border-emerald-200 bg-emerald-50/60' : 'border-slate-200 bg-slate-50' }} px-4 py-3 text-sm text-gray-700 dark:bg-gray-700/60 dark:text-gray-200">
                                <strong>{{ $option->kode }}.</strong> <span>{!! $option->jawaban !!}</span>
                                @if ($option->is_benar)
                                    <span class="ml-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">(Benar)</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">Belum ada soal.</div>
        @endforelse
    </div>

    @if ($items->count() > 1)
        <script>
            (() => {
                const cards = Array.from(document.querySelectorAll('.soal-card'));
                if (!cards.length) return;

                let dragging = null;
                const parent = cards[0].parentElement;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const reorderUrl = @json(route('dosen.ujian.soal.reorder', $ujian));

                const renumber = () => {
                    Array.from(parent.querySelectorAll('.soal-card')).forEach((card, idx) => {
                        const badge = card.querySelector('.soal-nomor');
                        if (badge) badge.textContent = `Soal #${idx + 1}`;
                    });
                };

                const saveOrder = async () => {
                    const order = Array.from(parent.querySelectorAll('.soal-card')).map((el) => Number(el.dataset.id));
                    try {
                        await fetch(reorderUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ order })
                        });
                    } catch (e) {
                        console.error(e);
                    }
                };

                cards.forEach((card) => {
                    card.addEventListener('dragstart', () => {
                        dragging = card;
                        card.classList.add('opacity-50');
                    });

                    card.addEventListener('dragend', async () => {
                        card.classList.remove('opacity-50');
                        dragging = null;
                        renumber();
                        await saveOrder();
                    });

                    card.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        if (!dragging || dragging === card) return;
                        const rect = card.getBoundingClientRect();
                        const isAfter = e.clientY > rect.top + rect.height / 2;
                        if (isAfter) {
                            card.after(dragging);
                        } else {
                            card.before(dragging);
                        }
                    });
                });
            })();
        </script>
    @endif
</x-admin-layout>
