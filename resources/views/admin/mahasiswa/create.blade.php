<x-admin-layout :title="'Tambah Mahasiswa'">
    <x-admin.partials.form
        :action="route('admin.mahasiswa.store')"
        method="POST"
        :back-url="route('admin.mahasiswa.index')"
        :fields="[
            ['label' => 'Nama', 'name' => 'name'],
            ['label' => 'Email', 'name' => 'email', 'type' => 'email'],
            ['label' => 'Password', 'name' => 'password', 'type' => 'password'],
            ['label' => 'NIM', 'name' => 'nim'],
            ['label' => 'Prodi', 'name' => 'prodi'],
            ['label' => 'Angkatan', 'name' => 'angkatan'],
            ['label' => 'Telepon', 'name' => 'telepon'],
            ['label' => 'Alamat', 'name' => 'alamat', 'type' => 'textarea', 'full' => true],
            ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Nonaktif']],
        ]"
    />
</x-admin-layout>
