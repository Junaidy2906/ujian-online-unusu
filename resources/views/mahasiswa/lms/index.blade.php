<x-app-layout>
    <div class="px-4 py-6 sm:px-6 lg:px-8 space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h1 class="text-2xl font-semibold text-slate-900">LMS Mahasiswa</h1>
            @if (!$setting)
                <p class="mt-2 text-sm text-red-600">Pengaturan minggu LMS belum diatur admin.</p>
            @else
                <p class="mt-2 text-sm text-slate-600">Minggu aktif saat ini: <span class="font-semibold">Minggu {{ $currentWeek }}</span>. Materi minggu berikutnya akan terbuka otomatis sesuai setting admin.</p>
            @endif
        </div>

        @foreach ($mataKuliah as $mk)
            <div class="rounded-3xl border border-slate-200 bg-white p-6">
                <h2 class="text-xl font-semibold text-slate-900">{{ $mk->nama_mk }} ({{ $mk->kode_mk }})</h2>
                <p class="mt-1 text-sm text-slate-500">Dosen: {{ $mk->dosen?->user?->name ?? '-' }}</p>

                <div class="mt-4 space-y-3">
                    @forelse (($materials[$mk->id] ?? collect()) as $item)
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pertemuan {{ $item->meeting?->meeting_number ?? $item->week_number }} - {{ $typeOptions[$item->type] ?? $item->type }}</p>
                            <h3 class="mt-1 font-semibold text-slate-900">{{ $item->title }}</h3>

                            @php
                                $isLocked = !empty($item->access_code) && !in_array($item->id, $unlockedIds, true);
                            @endphp

                            @if ($isLocked)
                                <p class="mt-2 text-sm text-amber-700">Materi ini dikunci dosen. Masukkan kode akses untuk membuka.</p>
                                <form action="{{ route('mahasiswa.lms.unlock', $item) }}" method="POST" class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center">
                                    @csrf
                                    <input type="text" name="access_code" class="w-full rounded-xl border-slate-300 sm:max-w-xs" placeholder="Masukkan kode akses">
                                    <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Buka Materi</button>
                                </form>
                            @else
                                @if ($item->description)
                                    <p class="mt-1 text-sm text-slate-600">{{ $item->description }}</p>
                                @endif
                                @if ($item->file_path)
                                    <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="mt-2 mr-3 inline-block text-sm font-semibold text-emerald-600">Unduh/Lihat File</a>
                                @endif
                                @if ($item->resource_url)
                                    <a href="{{ $item->resource_url }}" target="_blank" class="mt-2 inline-block text-sm font-semibold text-sky-600">Buka Link YouTube/Drive</a>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada materi yang terbuka untuk mata kuliah ini.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
