<x-app-layout>
    <div class="px-4 py-6 sm:px-6 lg:px-8 space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h1 class="text-2xl font-semibold text-slate-900">Menu LMS - Admin</h1>
            <p class="mt-2 text-sm text-slate-600">Kelola pengaturan minggu pembelajaran dan pantau data master LMS.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Daftar Dosen</p>
                <p class="mt-2 text-3xl font-semibold">{{ $totalDosen }}</p>
                <a href="{{ route('admin.dosen.index') }}" class="mt-2 inline-block text-sm font-semibold text-emerald-700">Lihat daftar dosen</a>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Daftar Kelas</p>
                <p class="mt-2 text-3xl font-semibold">{{ $totalKelas }}</p>
                <a href="{{ route('admin.kelas.index') }}" class="mt-2 inline-block text-sm font-semibold text-emerald-700">Lihat daftar kelas</a>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Daftar Mahasiswa</p>
                <p class="mt-2 text-3xl font-semibold">{{ $totalMahasiswa }}</p>
                <a href="{{ route('admin.mahasiswa.index') }}" class="mt-2 inline-block text-sm font-semibold text-emerald-700">Lihat daftar mahasiswa</a>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-slate-900">Pengaturan Minggu LMS</h2>
            <p class="mt-1 text-sm text-slate-600">Atur minggu 1 dimulai tanggal berapa dan hari apa. Minggu berikutnya akan terbuka bertahap otomatis.</p>

            <form action="{{ route('admin.lms.setting.save') }}" method="POST" class="mt-6 grid gap-4 md:grid-cols-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700">Tanggal Mulai Minggu 1</label>
                    <input type="date" name="week_1_start_date" value="{{ old('week_1_start_date', optional($setting?->week_1_start_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-xl border-slate-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Hari Pertemuan</label>
                    <select name="meeting_day" class="mt-1 w-full rounded-xl border-slate-300" required>
                        @foreach ([1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'] as $value => $label)
                            <option value="{{ $value }}" @selected((int) old('meeting_day', $setting?->meeting_day) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white">Simpan Setting</button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-slate-900">Default 16 Pertemuan per Semester</h2>
            <p class="mt-1 text-sm text-slate-600">Buat otomatis 16 pertemuan untuk semua mata kuliah aktif pada semester yang dipilih.</p>

            <form action="{{ route('admin.lms.meetings.generate') }}" method="POST" class="mt-6 grid gap-4 md:grid-cols-3">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Semester</label>
                    <select name="semester_id" class="mt-1 w-full rounded-xl border-slate-300" required>
                        <option value="">Pilih semester</option>
                        @foreach ($semesterItems as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->nama }} - {{ $semester->tahunAkademik->nama ?? 'Tahun Akademik' }} @if($semester->is_active) (Aktif) @endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Generate 16 Pertemuan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
