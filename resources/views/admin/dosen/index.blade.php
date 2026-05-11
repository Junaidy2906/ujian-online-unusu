<x-admin-layout :title="'Data Dosen'">
    @php
        $q = $filters['q'] ?? '';
        $status = $filters['status'] ?? 'semua';
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Data Dosen</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola akun dan profil dosen.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.dosen.template.download') }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Download Template</a>
            <a href="{{ route('admin.dosen.create') }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Tambah Data</a>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.dosen.template.upload') }}" enctype="multipart/form-data" class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf
        <div class="flex flex-wrap items-center gap-3">
            <input type="file" name="template_file" accept=".xlsx,.xls,.csv,.txt" class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Upload Data Dosen</button>
        </div>
        @error('template_file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </form>
    <form method="GET" action="{{ route('admin.dosen.index') }}" class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Cari Dosen</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Nama, email, NIDN, telepon..." class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Status</label>
                <select name="status" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="semua" @selected($status === 'semua')>Semua</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-amber-300">Filter</button>
                <a href="{{ route('admin.dosen.index') }}" class="w-full rounded-2xl border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">NIDN</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->user?->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->user?->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->nidn ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.dosen.show', $item) }}" class="rounded-xl border border-slate-300 px-3 py-1.5 text-slate-700 dark:border-gray-600 dark:text-gray-200">Detail</a>
                                <a href="{{ route('admin.dosen.edit', $item) }}" class="rounded-xl border border-gray-300 px-3 py-1.5 text-gray-700 dark:border-gray-600 dark:text-gray-200">Edit</a>
                                <form method="POST" action="{{ route('admin.dosen.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-xl border border-red-200 px-3 py-1.5 text-red-600">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
