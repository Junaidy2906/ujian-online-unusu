<x-admin-layout :title="'Detail Dosen'">
    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-0 lg:grid-cols-[1fr_0.9fr]">
            <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-900 px-6 py-8 text-white sm:px-8">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">Profil Dosen</p>
                <h3 class="mt-3 text-3xl font-semibold tracking-tight">{{ $item->user?->name }}</h3>
                <p class="mt-2 text-sm text-slate-300">{{ $item->user?->email }}</p>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/50">Mata Kuliah</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $totalMataKuliah }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/50">Kelas Wali</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $totalKelas }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/50">Ujian</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $totalUjian }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">NIDN</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ $item->nidn ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Gelar</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ trim(($item->gelar_depan ? $item->gelar_depan.' ' : '').($item->gelar_belakang ?? '')) ?: '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Alamat</div>
                        <div class="mt-2 text-sm leading-6 text-slate-700">{{ $item->alamat ?? '-' }}</div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.dosen.edit', $item) }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Edit Data</a>
                    <a href="{{ route('admin.dosen.index') }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
