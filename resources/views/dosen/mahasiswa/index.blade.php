<x-admin-layout :title="'Data Mahasiswa Kelas'">
    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Data Mahasiswa per Kelas</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Pilih kelas untuk melihat daftar mahasiswa yang akan dituju saat membuat ujian.</p>

        <form method="GET" action="{{ route('dosen.mahasiswa.index') }}" class="mt-4 grid gap-3 md:grid-cols-[1fr_auto]">
            <select name="kelas_id" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @forelse ($kelasItems as $kelas)
                    <option value="{{ $kelas->id }}" @selected($selectedKelasId == $kelas->id)>
                        {{ $kelas->nama_kelas }} ({{ $kelas->mahasiswa_count }} mahasiswa)
                    </option>
                @empty
                    <option value="">Belum ada kelas berisi mahasiswa</option>
                @endforelse
            </select>
            <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300">Tampilkan</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                {{ $selectedKelas?->nama_kelas ? 'Kelas: '.$selectedKelas->nama_kelas : 'Belum ada kelas dipilih' }}
            </h4>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">NIM</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Prodi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Angkatan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($selectedKelas?->mahasiswa ?? [] as $mhs)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $mhs->nim }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $mhs->user?->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $mhs->prodi ?: '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $mhs->angkatan ?: '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $mhs->status ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada mahasiswa pada kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
