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
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $item->nama_ujian }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->kelas?->nama_kelas }} • {{ $item->mataKuliah?->nama_mk }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai: {{ $item->jadwal_mulai?->format('d M Y H:i') }}</p>
                        </div>
                        <a href="{{ route('mahasiswa.ujian.show', $item) }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-amber-300">Lihat Ujian</a>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">Belum ada ujian aktif.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>