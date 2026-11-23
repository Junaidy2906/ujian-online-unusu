<div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <h1 class="text-2xl font-semibold text-slate-900">{{ isset($material) ? 'Edit Materi LMS' : 'Tambah Materi LMS' }}</h1>
            <form action="{{ isset($material) ? route('dosen.lms.update', $material) : route('dosen.lms.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                @isset($material)
                    @method('PUT')
                @endisset

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Pertemuan (Mata Kuliah + Semester)</label>
                    <select name="lms_meeting_id" class="mt-1 w-full rounded-xl border-slate-300" required>
                        <option value="">Pilih Pertemuan</option>
                        @foreach ($meetingItems as $meeting)
                            <option value="{{ $meeting->id }}" @selected((int) old('lms_meeting_id', $material->lms_meeting_id ?? null) === (int) $meeting->id)>
                                {{ $meeting->mataKuliah->nama_mk }} ({{ $meeting->mataKuliah->kode_mk }}) - {{ $meeting->semester->nama }} - Pertemuan {{ $meeting->meeting_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Jenis Konten</label>
                    <select name="type" class="mt-1 w-full rounded-xl border-slate-300" required>
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $material->type ?? null) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Judul</label>
                    <input type="text" name="title" value="{{ old('title', $material->title ?? null) }}" class="mt-1 w-full rounded-xl border-slate-300" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" rows="4" class="mt-1 w-full rounded-xl border-slate-300">{{ old('description', $material->description ?? null) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Link Sumber (opsional)</label>
                    <input type="url" name="resource_url" value="{{ old('resource_url', $material->resource_url ?? null) }}" class="mt-1 w-full rounded-xl border-slate-300" placeholder="https://youtube.com/... atau https://drive.google.com/...">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Upload File (Word/PDF/Gambar)</label>
                    <input type="file" name="material_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" class="mt-1 w-full rounded-xl border-slate-300">
                    @if (!empty($material?->file_path))
                        <p class="mt-2 text-sm text-slate-600">File saat ini: {{ $material->file_name }}</p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Kode Akses Mahasiswa (opsional)</label>
                    <input type="text" name="access_code" value="{{ old('access_code', $material->access_code ?? null) }}" class="mt-1 w-full rounded-xl border-slate-300" placeholder="Contoh: MKBIO7">
                </div>

                <div class="md:col-span-2 flex gap-3">
                    <button class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white">{{ isset($material) ? 'Update' : 'Simpan' }}</button>
                    <a href="{{ route('dosen.lms.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                </div>
            </form>
        </div>
    </div>
