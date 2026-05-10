<x-admin-layout :title="'Edit Dosen'">
    <x-admin.partials.form
        :action="route('admin.dosen.update', $item)"
        method="PUT"
        :item="$item"
        :back-url="route('admin.dosen.index')"
        :fields="[
            ['label' => 'Nama', 'name' => 'name', 'value' => $item->user?->name],
            ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'value' => $item->user?->email],
            ['label' => 'Password Baru', 'name' => 'password', 'type' => 'password'],
            ['label' => 'NIDN', 'name' => 'nidn', 'full' => false],
            ['label' => 'Gelar Depan', 'name' => 'gelar_depan'],
            ['label' => 'Gelar Belakang', 'name' => 'gelar_belakang'],
            ['label' => 'Telepon', 'name' => 'telepon'],
            ['label' => 'Alamat', 'name' => 'alamat', 'type' => 'textarea', 'full' => true],
            ['label' => 'Aktif', 'name' => 'is_active', 'type' => 'checkbox', 'checkbox_label' => 'Jadikan aktif'],
        ]"
    />
</x-admin-layout>
