<x-admin-layout :title="'Buat Ujian'">
    <x-admin.partials.form
        :action="route('dosen.ujian.store')"
        method="POST"
        :back-url="route('dosen.ujian.index')"
        :fields="[
            ['label' => 'Tahun Akademik', 'name' => 'tahun_akademik_id', 'type' => 'select', 'options' => $tahunAkademikItems->pluck('nama', 'id')->all()],
            ['label' => 'Semester', 'name' => 'semester_id', 'type' => 'select', 'options' => \App\Models\Semester::latest()->get()->pluck('nama', 'id')->all()],
            ['label' => 'Kelas', 'name' => 'kelas_id', 'type' => 'select', 'options' => $kelasItems->mapWithKeys(fn ($k) => [$k->id => $k->nama_kelas.' ('.$k->mahasiswa_count.' mhs)'])->all()],
            ['label' => 'Mata Kuliah', 'name' => 'mata_kuliah_id', 'type' => 'select', 'options' => $mataKuliahItems->pluck('nama_mk', 'id')->all()],
            ['label' => 'Nama Ujian', 'name' => 'nama_ujian'],
            ['label' => 'Deskripsi', 'name' => 'deskripsi', 'type' => 'textarea', 'full' => true],
            ['label' => 'Jadwal Mulai', 'name' => 'jadwal_mulai', 'type' => 'datetime-local'],
            ['label' => 'Jadwal Selesai', 'name' => 'jadwal_selesai', 'type' => 'datetime-local'],
            ['label' => 'Durasi (menit)', 'name' => 'durasi_menit', 'type' => 'number'],
            ['label' => 'Nilai Minimum Lulus', 'name' => 'nilai_minimum_lulus', 'type' => 'number'],
            ['label' => 'Maksimal Percobaan', 'name' => 'maksimal_percobaan', 'type' => 'number'],
            ['label' => 'Bobot PG', 'name' => 'bobot_pg', 'type' => 'number'],
            ['label' => 'Bobot Essai', 'name' => 'bobot_essay', 'type' => 'number'],
            ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'selesai' => 'Selesai']],
            ['label' => 'Aktif', 'name' => 'is_active', 'type' => 'checkbox', 'checkbox_label' => 'Jadikan aktif'],
        ]"
    />
</x-admin-layout>
