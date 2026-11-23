<x-admin-layout :title="'Data Mahasiswa'">
    @php
        $q = $filters['q'] ?? '';
        $status = $filters['status'] ?? 'semua';
        $prodiSelected = $filters['prodi'] ?? '';
        $kelasSelected = $filters['kelas'] ?? '';
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Data Mahasiswa</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola akun dan profil mahasiswa.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.mahasiswa.template.download') }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Download Template</a>
            <a href="{{ route('admin.mahasiswa.create') }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Tambah Data</a>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.mahasiswa.template.upload', absolute: false) }}" enctype="multipart/form-data"
        x-data="studentUpload" @submit.prevent="upload($el)"
        class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf
        <div class="flex flex-wrap items-center gap-3">
            <label for="student-template" class="sr-only">File data mahasiswa</label>
            <input id="student-template" type="file" name="template_file" accept=".xlsx,.xls,.csv,.txt" required :disabled="busy" @change="error = ''" class="max-w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            <button type="submit" :disabled="busy" class="rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white disabled:cursor-wait disabled:opacity-60">Upload Data Mahasiswa</button>
        </div>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Data dengan NIM yang sama akan diperbarui dari file ini.</p>
        <div x-show="busy" style="display: none" class="mt-4 space-y-1">
            <p id="student-upload-status" role="status" class="text-sm font-medium text-slate-700 dark:text-slate-200" x-text="status"></p>
            <progress x-show="progress !== null" max="100" :value="progress ?? 0" aria-labelledby="student-upload-status" class="block h-2 w-full accent-emerald-600"></progress>
            <progress x-show="progress === null" aria-labelledby="student-upload-status" class="block h-2 w-full accent-emerald-600"></progress>
        </div>
        <p x-show="error" style="display: none" role="alert" x-text="error" class="mt-2 text-sm text-red-600"></p>
        @error('template_file')<p x-show="!attempted" role="alert" class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </form>
    <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="grid gap-3 md:grid-cols-6">
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Cari Mahasiswa</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Nama, email, NIM, angkatan, semester..." class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Status</label>
                <select name="status" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="semua" @selected($status === 'semua')>Semua</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="cuti" @selected($status === 'cuti')>Cuti</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Prodi</label>
                <select name="prodi" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Semua Prodi</option>
                    @foreach ($prodiItems as $prodi)
                        <option value="{{ $prodi }}" @selected($prodiSelected === $prodi)>{{ $prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Kelas</label>
                <select name="kelas" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Semua Kelas</option>
                    <option value="tanpa" @selected($kelasSelected === 'tanpa')>Belum punya kelas</option>
                    @foreach ($kelasItems as $kelas)
                        <option value="{{ $kelas->id }}" @selected($kelasSelected === (string) $kelas->id)>{{ $kelas->nama_kelas }} ({{ $kelas->mahasiswa_count }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-amber-300">Filter</button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="w-full rounded-2xl border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">Reset</a>
            </div>
        </div>
    </form>

    <form id="bulk-kelas-form" method="POST" action="{{ route('admin.mahasiswa.kelas.bulk') }}"
        onsubmit="const n=document.querySelectorAll('input[name=\'mahasiswa_ids[]\']:checked').length; if(!n){alert('Centang minimal satu mahasiswa pada tabel.');return false;} return confirm(n+' mahasiswa akan diproses. Lanjutkan?')"
        class="rounded-3xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-900/50 dark:bg-gray-800">
        @csrf
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[220px] flex-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.16em] text-amber-800 dark:text-amber-300">Atur Kelas Mahasiswa Terpilih</label>
                <select name="kelas_id" required class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Pilih kelas...</option>
                    @foreach ($kelasItems as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} — {{ $kelas->kode_kelas }} ({{ $kelas->mahasiswa_count }} mhs)</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" name="aksi" value="tambah" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Masukkan ke Kelas</button>
            <button type="submit" name="aksi" value="pindah" class="rounded-2xl border border-slate-400 bg-white px-4 py-2 text-sm font-semibold text-slate-700 dark:bg-gray-900 dark:text-gray-200">Pindahkan ke Kelas</button>
            <button type="submit" name="aksi" value="keluarkan" class="rounded-2xl border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 dark:bg-gray-900">Keluarkan dari Kelas</button>
        </div>
        <p class="mt-2 text-xs text-amber-900/70 dark:text-amber-200/70">
            Centang mahasiswa di tabel, pilih kelas, lalu klik aksi. <b>Masukkan</b> = tambah kelas (kelas lama tetap), <b>Pindahkan</b> = hanya di kelas ini.
            <span id="bulk-kelas-count" class="font-semibold"></span>
        </p>
        @error('mahasiswa_ids')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('kelas_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </form>

    <div class="overflow-x-auto rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="w-10 py-3 pl-6 text-left">
                        <input type="checkbox" aria-label="Pilih semua" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                            onchange="document.querySelectorAll('input[name=\'mahasiswa_ids[]\']').forEach(cb=>cb.checked=this.checked); updateBulkKelasCount()">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">NIM</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Prodi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Semester</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($items as $item)
                    <tr>
                        <td class="py-4 pl-6">
                            <input type="checkbox" name="mahasiswa_ids[]" value="{{ $item->id }}" form="bulk-kelas-form" onchange="updateBulkKelasCount()"
                                aria-label="Pilih {{ $item->user?->name }}" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->user?->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->user?->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->nim }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->prodi ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->semester ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($item->kelas as $kelas)
                                    <span class="rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-700">{{ $kelas->nama_kelas }}</span>
                                @empty
                                    <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-600">Belum ada</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.mahasiswa.show', $item) }}" class="rounded-xl border border-slate-300 px-3 py-1.5 text-slate-700 dark:border-gray-600 dark:text-gray-200">Detail</a>
                                <a href="{{ route('admin.mahasiswa.edit', $item) }}" class="rounded-xl border border-gray-300 px-3 py-1.5 text-gray-700 dark:border-gray-600 dark:text-gray-200">Edit</a>
                                <form method="POST" action="{{ route('admin.mahasiswa.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-xl border border-red-200 px-3 py-1.5 text-red-600">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <script>
        function updateBulkKelasCount() {
            const n = document.querySelectorAll("input[name='mahasiswa_ids[]']:checked").length;
            document.getElementById('bulk-kelas-count').textContent = n ? '(' + n + ' mahasiswa dipilih)' : '';
        }
    </script>
</x-admin-layout>
