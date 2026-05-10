<x-admin-layout :title="$title">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Menu Cetak</p>
                <h3 class="mt-1 text-2xl font-semibold text-slate-900">Pilih ujian untuk rekap nilai</h3>
                <p class="mt-2 text-sm text-slate-500">Gunakan halaman ini untuk masuk ke mode cetak laporan hasil ujian.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @forelse ($items as $item)
            <article class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h4 class="text-lg font-semibold text-slate-900">{{ $item->nama_ujian }}</h4>
                        <p class="mt-1 text-sm text-slate-500">{{ $item->kelas?->nama_kelas }} • {{ $item->mataKuliah?->nama_mk }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $item->status }}</span>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 text-sm text-slate-600">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Mulai: {{ $item->jadwal_mulai?->format('d M Y H:i') }}</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Durasi: {{ $item->durasi_menit }} menit</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Lulus: {{ $item->nilai_minimum_lulus }}</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Percobaan: {{ $item->maksimal_percobaan }}x</div>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route($reportRoute, $item) }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Lihat Rekap</a>
                </div>
            </article>
        @empty
            <div class="rounded-[1.8rem] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">Belum ada ujian.</div>
        @endforelse
    </div>
</x-admin-layout>