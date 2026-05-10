<x-admin-layout :title="'Tambah Kelas'">
    <x-admin.partials.form
        :action="route('admin.kelas.store')"
        method="POST"
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
    />
</x-admin-layout>
