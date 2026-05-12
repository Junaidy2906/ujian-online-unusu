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
        @php
            $selectedIds = old('mahasiswa_ids', $selectedMahasiswaIds);
            $prodiOptions = $mahasiswaItems->pluck('prodi')->filter()->unique()->sort()->values();
            $semesterOptions = $mahasiswaItems->pluck('semester')->filter()->unique()->sort()->values();
        @endphp

        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h4 class="text-sm font-semibold text-slate-900">Anggota Kelas</h4>
                    <p class="mt-1 text-xs text-slate-600">Pilih mahasiswa yang masuk ke kelas ini.</p>
                </div>
                <div id="selected-count" class="text-xs text-slate-500">{{ count($selectedIds) }} dipilih</div>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-4">
                <div>
                    <label for="filter-prodi" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Filter Prodi</label>
                    <select id="filter-prodi" class="w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                        <option value="">Semua Prodi</option>
                        @foreach ($prodiOptions as $prodi)
                            <option value="{{ $prodi }}">{{ $prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-semester" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Filter Semester</label>
                    <select id="filter-semester" class="w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                        <option value="">Semua Semester</option>
                        @foreach ($semesterOptions as $semester)
                            <option value="{{ $semester }}">Semester {{ $semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" id="select-all-filtered" class="w-full rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-amber-300 disabled:cursor-not-allowed disabled:opacity-40">
                        Select All Hasil Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <button type="button" id="clear-all-filtered" class="w-full rounded-xl border border-red-200 px-3 py-2 text-sm font-semibold text-red-600">
                        Kosongkan Hasil Filter
                    </button>
                </div>
            </div>

            <div class="mt-4 max-h-72 overflow-auto rounded-xl border border-slate-200 bg-white">
                <div class="divide-y divide-slate-100">
                    @forelse ($mahasiswaItems as $mhs)
                        <label
                            class="mahasiswa-row flex items-center justify-between gap-3 px-4 py-3 text-sm hover:bg-slate-50"
                            data-prodi="{{ $mhs->prodi ?? '' }}"
                            data-semester="{{ $mhs->semester ?? '' }}"
                        >
                            <span class="flex min-w-0 items-center gap-3">
                                <input
                                    type="checkbox"
                                    name="mahasiswa_ids[]"
                                    value="{{ $mhs->id }}"
                                    data-role="mahasiswa-checkbox"
                                    class="rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                                    @checked(in_array($mhs->id, $selectedIds, true))
                                >
                                <span class="min-w-0">
                                    <span class="block truncate font-medium text-slate-900">{{ $mhs->user?->name ?? '-' }}</span>
                                    <span class="block text-xs text-slate-500">NIM: {{ $mhs->nim }} - {{ $mhs->prodi ?: 'Tanpa Prodi' }} - Semester {{ $mhs->semester ?? '-' }}</span>
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const prodiSelect = document.getElementById('filter-prodi');
                const semesterSelect = document.getElementById('filter-semester');
                const selectAllBtn = document.getElementById('select-all-filtered');
                const clearAllBtn = document.getElementById('clear-all-filtered');
                const rows = Array.from(document.querySelectorAll('.mahasiswa-row'));
                const countLabel = document.getElementById('selected-count');

                const normalize = (value) => (value || '').trim().toLowerCase();
                const visibleRows = () => rows.filter((row) => row.style.display !== 'none');

                const updateCount = () => {
                    const checked = document.querySelectorAll('input[data-role="mahasiswa-checkbox"]:checked').length;
                    if (countLabel) {
                        countLabel.textContent = checked + ' dipilih';
                    }
                };

                const applyFilters = () => {
                    const prodi = normalize(prodiSelect ? prodiSelect.value : '');
                    const semester = normalize(semesterSelect ? semesterSelect.value : '');

                    rows.forEach((row) => {
                        const rowProdi = normalize(row.dataset.prodi);
                        const rowSemester = normalize(row.dataset.semester);
                        const matchProdi = prodi === '' || rowProdi === prodi;
                        const matchSemester = semester === '' || rowSemester === semester;
                        row.style.display = (matchProdi && matchSemester) ? '' : 'none';
                    });

                    if (selectAllBtn) {
                        selectAllBtn.disabled = semester === '';
                    }
                };

                const setCheckedForVisible = (checked) => {
                    visibleRows().forEach((row) => {
                        const checkbox = row.querySelector('input[data-role="mahasiswa-checkbox"]');
                        if (checkbox) checkbox.checked = checked;
                    });
                    updateCount();
                };

                if (prodiSelect) prodiSelect.addEventListener('change', applyFilters);
                if (semesterSelect) semesterSelect.addEventListener('change', applyFilters);
                if (selectAllBtn) selectAllBtn.addEventListener('click', () => setCheckedForVisible(true));
                if (clearAllBtn) clearAllBtn.addEventListener('click', () => setCheckedForVisible(false));

                document.querySelectorAll('input[data-role="mahasiswa-checkbox"]').forEach((checkbox) => {
                    checkbox.addEventListener('change', updateCount);
                });

                applyFilters();
                updateCount();
            });
        </script>
    </x-admin.partials.form>
</x-admin-layout>
