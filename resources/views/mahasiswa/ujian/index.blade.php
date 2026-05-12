<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Daftar Ujian</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">UTS aktif untuk kelas Anda.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            @forelse ($items as $item)
                @php
                    $isExpired = $item->jadwal_selesai && now()->greaterThan($item->jadwal_selesai);
                @endphp

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $item->nama_ujian }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->kelas?->nama_kelas }} - {{ $item->mataKuliah?->nama_mk }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai: {{ $item->jadwal_mulai?->format('d M Y H:i') }}</p>
                            @if ($item->jadwal_selesai)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selesai: {{ $item->jadwal_selesai?->format('d M Y H:i') }}</p>
                            @endif
                            <div class="mt-2">
                                @if ($isExpired)
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Waktu Habis</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                                @endif
                            </div>
                        </div>

                        @if ($isExpired)
                            <span class="cursor-not-allowed rounded-2xl bg-red-100 px-4 py-2 text-sm font-semibold text-red-500">Waktu Habis</span>
                        @else
                            <a href="{{ route('mahasiswa.ujian.show', $item) }}" class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Lihat Ujian</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">Belum ada ujian aktif.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
