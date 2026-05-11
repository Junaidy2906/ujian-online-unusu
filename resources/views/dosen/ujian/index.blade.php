<x-admin-layout :title="'Ujian Saya'">
    <div class="flex items-center justify-between gap-4 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ujian Saya</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola ujian, soal, dan hasil mahasiswa.</p>
        </div>
        <a href="{{ route('dosen.ujian.create') }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Buat Ujian</a>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Ujian</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Mapel</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Soal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->nama_ujian }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->kelas?->nama_kelas }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->mataKuliah?->nama_mk }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300"><code>{{ $item->kode_soal ?? '-' }}</code></td>
                        <td class="px-6 py-4 text-sm"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $item->status }}</span></td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('dosen.ujian.soal.index', $item) }}" class="rounded-xl border border-gray-300 px-3 py-1.5 text-gray-700 dark:border-gray-600 dark:text-gray-200">Soal</a>
                                <a href="{{ route('dosen.ujian.akses-mahasiswa', $item) }}" class="rounded-xl border border-gray-300 px-3 py-1.5 text-gray-700 dark:border-gray-600 dark:text-gray-200">Akses</a>
                                <a href="{{ route('dosen.ujian.hasil', $item) }}" class="rounded-xl border border-gray-300 px-3 py-1.5 text-gray-700 dark:border-gray-600 dark:text-gray-200">Hasil</a>
                                <a href="{{ route('dosen.ujian.edit', $item) }}" class="rounded-xl border border-gray-300 px-3 py-1.5 text-gray-700 dark:border-gray-600 dark:text-gray-200">Edit</a>
                                <form method="POST" action="{{ route('dosen.ujian.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-xl border border-red-200 px-3 py-1.5 text-red-600">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada ujian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
