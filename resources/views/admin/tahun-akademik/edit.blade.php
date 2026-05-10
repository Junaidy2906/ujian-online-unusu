<x-admin-layout :title="'Edit Tahun Akademik'">
    <x-admin.partials.form
        :action="route('admin.tahun-akademik.update', $item)"
        method="PUT"
        :item="$item"
        :back-url="route('admin.tahun-akademik.index')"
        :fields="[
            ['label' => 'Kode', 'name' => 'kode'],
            ['label' => 'Nama', 'name' => 'nama'],
            ['label' => 'Aktif', 'name' => 'is_active', 'type' => 'checkbox', 'checkbox_label' => 'Jadikan aktif'],
        ]"
    />
</x-admin-layout>