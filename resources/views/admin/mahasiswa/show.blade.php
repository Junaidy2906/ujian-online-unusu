<x-admin-layout :title="'Detail Mahasiswa'">
    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-0 lg:grid-cols-[1fr_0.9fr]">
            <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-amber-700 px-6 py-8 text-white sm:px-8">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-200">Profil Mahasiswa</p>
                <h3 class="mt-3 text-3xl font-semibold tracking-tight">{{ $item->user?->name }}</h3>
                <p class="mt-2 text-sm text-slate-300">{{ $item->user?->email }}</p>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/50">Kelas</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $totalKelas }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/50">Percobaan</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $totalPercobaan }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/50">Nilai</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $totalNilai }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">NIM</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ $item->nim }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ ucfirst($item->status) }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Angkatan</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ $item->angkatan ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Prodi</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ $item->prodi ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Semester</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ $item->semester ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Telepon</div>
                        <div class="mt-2 text-sm font-semibold text-slate-900">{{ $item->telepon ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Alamat</div>
                        <div class="mt-2 text-sm leading-6 text-slate-700">{{ $item->alamat ?? '-' }}</div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.mahasiswa.edit', $item) }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Edit Data</a>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
