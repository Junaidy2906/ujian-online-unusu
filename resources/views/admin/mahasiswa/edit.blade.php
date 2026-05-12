<x-admin-layout :title="'Edit Mahasiswa'">
    <x-admin.partials.form
        :action="route('admin.mahasiswa.update', $item)"
        method="PUT"
        :item="$item"
        :back-url="route('admin.mahasiswa.index')"
        :fields="[
            ['label' => 'Nama', 'name' => 'name', 'value' => $item->user?->name],
            ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'value' => $item->user?->email],
            ['label' => 'Password Baru', 'name' => 'password', 'type' => 'password'],
            ['label' => 'NIM', 'name' => 'nim'],
            ['label' => 'Prodi', 'name' => 'prodi'],
            ['label' => 'Semester', 'name' => 'semester', 'type' => 'number'],
            ['label' => 'Angkatan', 'name' => 'angkatan'],
            ['label' => 'Telepon', 'name' => 'telepon'],
            ['label' => 'Alamat', 'name' => 'alamat', 'type' => 'textarea', 'full' => true],
            ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Nonaktif']],
        ]"
    />
</x-admin-layout>
