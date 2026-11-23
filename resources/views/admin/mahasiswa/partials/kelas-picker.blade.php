@php
    $checkedIds = collect(old('kelas_ids', $selectedKelasIds ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

<div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
    <div class="flex flex-wrap items-start justify-between gap-2">
        <div>
            <h4 class="text-sm font-semibold text-slate-900 dark:text-gray-100">Kelas Mahasiswa</h4>
            <p class="mt-1 text-xs text-slate-600 dark:text-gray-400">Centang kelas yang diikuti mahasiswa ini. Dosen akan melihat mahasiswa ini saat memilih kelas tersebut.</p>
        </div>
        @if ($kelasItems->isNotEmpty())
            <input type="search" placeholder="Cari kelas..." oninput="const q=this.value.toLowerCase();this.closest('div.rounded-2xl').querySelectorAll('[data-kelas-option]').forEach(el=>el.style.display=el.dataset.kelasOption.includes(q)?'':'none')"
                class="w-full max-w-xs rounded-xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
        @endif
    </div>

    @if ($kelasItems->isEmpty())
        <p class="mt-3 text-sm text-slate-500">Belum ada data kelas. <a href="{{ route('admin.kelas.create') }}" class="font-semibold text-amber-700 underline">Tambah kelas</a> terlebih dahulu.</p>
    @else
        <div class="mt-3 grid max-h-72 gap-2 overflow-y-auto sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($kelasItems as $kelas)
                <label data-kelas-option="{{ strtolower($kelas->nama_kelas.' '.$kelas->kode_kelas.' '.$kelas->angkatan) }}"
                    class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm hover:border-amber-400 dark:border-gray-700 dark:bg-gray-800">
                    <input type="checkbox" name="kelas_ids[]" value="{{ $kelas->id }}" @checked(in_array($kelas->id, $checkedIds, true))
                        class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <span>
                        <span class="block font-medium text-gray-900 dark:text-gray-100">{{ $kelas->nama_kelas }}</span>
                        <span class="block text-xs text-gray-500">
                            {{ $kelas->kode_kelas }}
                            @if ($kelas->semester) · {{ $kelas->semester->nama }} @endif
                            @if ($kelas->tahunAkademik) · {{ $kelas->tahunAkademik->nama }} @endif
                            · {{ $kelas->mahasiswa_count }} mhs
                            @unless ($kelas->is_active) · <span class="text-red-500">nonaktif</span> @endunless
                        </span>
                    </span>
                </label>
            @endforeach
        </div>
    @endif

    @error('kelas_ids')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    @error('kelas_ids.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
