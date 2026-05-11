<x-admin-layout :title="'Akses Mahasiswa Ujian'">
    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="mb-5 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ujian->nama_ujian }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $ujian->kelas?->nama_kelas }} • {{ $ujian->mataKuliah?->nama_mk }}</p>
        <div class="mt-4 rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Kode soal ujian ini: <code class="font-semibold">{{ $ujian->kode_soal }}</code>
        </div>
        <p class="mt-2 text-xs text-gray-500">Mahasiswa hanya bisa melihat ujian jika kode di bawah diisi sama persis dengan kode soal di atas.</p>
    </div>

    <form method="POST" action="{{ route('dosen.ujian.akses-mahasiswa.simpan', $ujian) }}" class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf
        <div class="flex flex-wrap items-end gap-3 border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900/40">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Massal</label>
                <input type="text" name="bulk_kode" value="{{ $ujian->kode_soal }}" class="rounded-2xl border-gray-300 bg-white text-sm uppercase dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <button type="submit" name="bulk_action" value="pair_all" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Pasangkan ke Semua Mahasiswa</button>
            <button type="submit" name="bulk_action" value="clear_all" class="rounded-2xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600">Kosongkan Semua Akses</button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Akses Soal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $grouped = $mahasiswaItems->groupBy(fn ($m) => ($m->prodi ?: 'Tanpa Prodi').'||'.($m->kelas->first()?->nama_kelas ?: 'Tanpa Kelas'));
                    @endphp
                    @forelse ($grouped as $key => $members)
                        @php
                            [$prodi, $kelas] = explode('||', $key);
                        @endphp
                        <tr>
                            <td colspan="3" class="bg-slate-100 px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-700 dark:bg-slate-700/60 dark:text-slate-100">
                                Prodi: {{ $prodi }} | Kelas: {{ $kelas }}
                            </td>
                        </tr>
                        @foreach ($members as $mhs)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $mhs->nim }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $mhs->user?->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <input
                                        type="text"
                                        name="kode[{{ $mhs->id }}]"
                                        value="{{ old('kode.'.$mhs->id, $aksesByMahasiswa[$mhs->id] ?? '') }}"
                                        placeholder="Kosongkan untuk menutup akses"
                                        class="w-full rounded-2xl border-gray-300 bg-white text-sm uppercase dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada mahasiswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4 dark:border-gray-700">
            <a href="{{ route('dosen.ujian.index') }}" class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200">Kembali</a>
            <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300">Simpan Akses</button>
        </div>
    </form>
</x-admin-layout>
