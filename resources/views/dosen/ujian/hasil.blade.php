<x-admin-layout :title="'Hasil Ujian'">
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ujian->nama_ujian }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $ujian->kelas?->nama_kelas }} - {{ $ujian->mataKuliah?->nama_mk }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('dosen.laporan-nilai.show', $ujian) }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-600 dark:text-slate-200">Cetak Rekap</a>
            <a href="{{ route('dosen.laporan-nilai.index') }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Menu Rekap</a>
        </div>
    </div>

    @php
        $groupedItems = $items->groupBy('mahasiswa_id');
    @endphp

    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Mahasiswa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Percobaan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nilai PG</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nilai Essai</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Akhir</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Waktu Submit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($groupedItems as $mahasiswaId => $history)
                    @php
                        $studentName = $history->first()?->mahasiswa?->user?->name ?? '-';
                    @endphp
                    <tr>
                        <td colspan="7" class="bg-slate-100 px-6 py-2 text-xs font-semibold uppercase tracking-wider text-slate-700 dark:bg-slate-700/60 dark:text-slate-100">
                            {{ $studentName }} ({{ $history->count() }} percobaan)
                        </td>
                    </tr>
                    @foreach ($history as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->mahasiswa?->user?->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Ke-{{ $item->percobaanUjian?->percobaan_ke ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->nilai_pg ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->nilai_essay ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->nilai_akhir ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $item->status_penilaian }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->created_at?->format('d M Y H:i:s') ?? '-' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada hasil.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
