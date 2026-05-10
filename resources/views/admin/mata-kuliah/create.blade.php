<x-admin-layout :title="'Tambah Mata Kuliah'">
    <x-admin.partials.form
        :action="route('admin.mata-kuliah.store')"
        method="POST"
        :back-url="route('admin.mata-kuliah.index')"
        :fields="[
            ['label' => 'Dosen Pengampu', 'name' => 'dosen_id', 'type' => 'select', 'options' => $dosenItems->mapWithKeys(fn ($item) => [$item->id => $item->user?->name ?? 'Dosen'])->all()],
            ['label' => 'Kode MK', 'name' => 'kode_mk'],
            ['label' => 'Nama MK', 'name' => 'nama_mk'],
            ['label' => 'SKS', 'name' => 'sks', 'type' => 'number'],
            ['label' => 'Prodi', 'name' => 'prodi'],
            ['label' => 'Aktif', 'name' => 'is_active', 'type' => 'checkbox', 'checkbox_label' => 'Jadikan aktif'],
        ]"
    />
</x-admin-layout>