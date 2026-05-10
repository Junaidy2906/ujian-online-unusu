<x-admin-layout :title="'Edit Semester'">
    <x-admin.partials.form
        :action="route('admin.semester.update', $item)"
        method="PUT"
        :item="$item"
        :back-url="route('admin.semester.index')"
        :fields="[
            ['label' => 'Tahun Akademik', 'name' => 'tahun_akademik_id', 'type' => 'select', 'options' => $tahunAkademikItems->pluck('nama', 'id')->all()],
            ['label' => 'Nama Semester', 'name' => 'nama'],
            ['label' => 'Urutan', 'name' => 'urutan', 'type' => 'number'],
            ['label' => 'Tanggal Mulai', 'name' => 'tanggal_mulai', 'type' => 'date'],
            ['label' => 'Tanggal Selesai', 'name' => 'tanggal_selesai', 'type' => 'date'],
            ['label' => 'Aktif', 'name' => 'is_active', 'type' => 'checkbox', 'checkbox_label' => 'Jadikan aktif'],
        ]"
    />
</x-admin-layout>