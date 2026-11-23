<x-app-layout>
    <div class="px-4 py-6 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-white p-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Menu LMS - Dosen</h1>
                <p class="mt-2 text-sm text-slate-600">Buat bahan ajar, kuis, penunjuk penugasan, dan contoh pembelajaran per minggu.</p>
            </div>
            <a href="{{ route('dosen.lms.create') }}" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white">Tambah Materi</a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr><th class="px-4 py-3">Pertemuan</th><th class="px-4 py-3">Mata Kuliah</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Judul</th><th class="px-4 py-3">Akses</th><th class="px-4 py-3">File/Link</th><th class="px-4 py-3">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($materials as $item)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">{{ $item->meeting?->meeting_number ?? $item->week_number }}</td>
                            <td class="px-4 py-3">{{ $item->mataKuliah?->nama_mk }}</td>
                            <td class="px-4 py-3">{{ $typeOptions[$item->type] ?? $item->type }}</td>
                            <td class="px-4 py-3">{{ $item->title }}</td>
                            <td class="px-4 py-3">{{ $item->access_code ? 'Pakai Kode' : 'Terbuka' }}</td>
                            <td class="px-4 py-3">
                                @if ($item->file_path)
                                    <span class="text-xs rounded bg-emerald-100 px-2 py-1 text-emerald-700">File</span>
                                @endif
                                @if ($item->resource_url)
                                    <span class="text-xs rounded bg-sky-100 px-2 py-1 text-sky-700">Link</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('dosen.lms.edit', $item) }}" class="rounded-lg bg-amber-100 px-3 py-1 text-amber-700">Edit</a><form action="{{ route('dosen.lms.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">@csrf @method('DELETE')<button class="rounded-lg bg-red-100 px-3 py-1 text-red-700">Hapus</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada materi LMS.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
