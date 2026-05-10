<x-admin-layout :title="'Tambah Dosen'">
    <x-admin.partials.form
        :action="route('admin.dosen.store')"
        method="POST"
        :back-url="route('admin.dosen.index')"
        :fields="[
            ['label' => 'Nama', 'name' => 'name'],
            ['label' => 'Email', 'name' => 'email', 'type' => 'email'],
            ['label' => 'Password', 'name' => 'password', 'type' => 'password'],
            ['label' => 'NIDN', 'name' => 'nidn'],
            ['label' => 'Gelar Depan', 'name' => 'gelar_depan'],
            ['label' => 'Gelar Belakang', 'name' => 'gelar_belakang'],
            ['label' => 'Telepon', 'name' => 'telepon'],
            ['label' => 'Alamat', 'name' => 'alamat', 'type' => 'textarea', 'full' => true],
            ['label' => 'Aktif', 'name' => 'is_active', 'type' => 'checkbox', 'checkbox_label' => 'Jadikan aktif'],
        ]"
    />
</x-admin-layout>
