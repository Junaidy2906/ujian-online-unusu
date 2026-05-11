<x-admin-layout :title="'Edit Kelas'">
    <x-admin.partials.form
        :action="route('admin.kelas.update', ['kelas' => $item->id])"
        method="PUT"
        :item="$item"
        :back-url="route('admin.kelas.index')"
        :fields="[
            ['label' => 'Tahun Akademik', 'name' => 'tahun_akademik_id', 'type' => 'select', 'options' => $tahunAkademikItems->pluck('nama', 'id')->all()],
            ['label' => 'Semester', 'name' => 'semester_id', 'type' => 'select', 'options' => $semesterItems->pluck('nama', 'id')->all()],
            ['label' => 'Dosen Wali', 'name' => 'dosen_wali_id', 'type' => 'select', 'options' => $dosenItems->mapWithKeys(fn ($item) => [$item->id => ($item->user?->name ?? 'Dosen').' '.($item->nidn ? '(' . $item->nidn . ')' : '')])->all()],
            ['label' => 'Kode Kelas', 'name' => 'kode_kelas'],
            ['label' => 'Nama Kelas', 'name' => 'nama_kelas'],
            ['label' => 'Angkatan', 'name' => 'angkatan'],
            ['label' => 'Aktif', 'name' => 'is_active', 'type' => 'checkbox', 'checkbox_label' => 'Jadikan aktif'],
        ]"
    >
        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h4 class="text-sm font-semibold text-slate-900">Anggota Kelas</h4>
                    <p class="mt-1 text-xs text-slate-600">Pilih mahasiswa yang masuk ke kelas ini.</p>
                </div>
                <div class="text-xs text-slate-500">{{ count(old('mahasiswa_ids', $selectedMahasiswaIds)) }} dipilih</div>
            </div>

            <div class="mt-4 max-h-72 overflow-auto rounded-xl border border-slate-200 bg-white">
                <div class="divide-y divide-slate-100">
                    @forelse ($mahasiswaItems as $mhs)
                        <label class="flex items-center justify-between gap-3 px-4 py-3 text-sm hover:bg-slate-50">
                            <span class="flex min-w-0 items-center gap-3">
                                <input
                                    type="checkbox"
                                    name="mahasiswa_ids[]"
                                    value="{{ $mhs->id }}"
                                    class="rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                                    @checked(in_array($mhs->id, old('mahasiswa_ids', $selectedMahasiswaIds), true))
                                >
                                <span class="min-w-0">
                                    <span class="block truncate font-medium text-slate-900">{{ $mhs->user?->name ?? '-' }}</span>
                                    <span class="block text-xs text-slate-500">NIM: {{ $mhs->nim }} • {{ $mhs->prodi ?: 'Tanpa Prodi' }}</span>
                                </span>
                            </span>
                            <span class="text-xs text-slate-500">{{ $mhs->angkatan ?: '-' }}</span>
                        </label>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data mahasiswa.</div>
                    @endforelse
                </div>
            </div>

            @error('mahasiswa_ids')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('mahasiswa_ids.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </x-admin.partials.form>
</x-admin-layout>
