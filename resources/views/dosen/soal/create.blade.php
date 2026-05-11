<x-admin-layout :title="'Tambah Soal'">
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('dosen.ujian.soal.store', $ujian) }}" class="space-y-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor</label>
                <input type="number" name="nomor" value="{{ old('nomor', 1) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Tipe</label>
                <select id="tipe" name="tipe" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <option value="pg" @selected(old('tipe') === 'pg')>Pilihan Ganda</option>
                    <option value="essay" @selected(old('tipe') === 'essay')>Essai</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Pertanyaan</label>
                <textarea name="pertanyaan" rows="4" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('pertanyaan') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Bisa isi HTML (contoh: &lt;img&gt;, &lt;table&gt;).</p>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Poin</label>
                <input type="number" step="0.01" name="poin" value="{{ old('poin', 1) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div id="kunci-pg-field">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Jawaban Benar PG</label>
                <input type="text" name="jawaban_benar" value="{{ old('jawaban_benar') }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div id="rubrik-essay-field" class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Rubrik Essai</label>
                <textarea name="rubrik_penilaian" rows="3" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('rubrik_penilaian') }}</textarea>
            </div>
        </div>

        <div id="opsi-pg-wrapper" class="rounded-3xl border border-gray-200 p-5 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Pilihan Jawaban</h4>
                <p class="text-xs text-gray-500">Untuk soal PG isi minimal dua opsi.</p>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach (['A', 'B', 'C', 'D'] as $index => $kode)
                    <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-700/60">
                        <label class="mb-2 block text-sm font-medium">Opsi {{ $kode }}</label>
                        <input type="text" name="pilihan[{{ $index }}][jawaban]" value="{{ old('pilihan.'.$index.'.jawaban') }}" class="w-full rounded-xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        <input type="hidden" name="pilihan[{{ $index }}][kode]" value="{{ $kode }}">
                        <label class="mt-3 inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="pilihan[{{ $index }}][is_benar]" value="1" @checked(old('pilihan.'.$index.'.is_benar')) class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            Benar
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 pt-2">
            <a href="{{ route('dosen.ujian.soal.index', $ujian) }}" class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200">Kembali</a>
            <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300">Simpan</button>
        </div>
    </form>

    <script>
        (function () {
            const tipeSelect = document.getElementById('tipe');
            const opsiPg = document.getElementById('opsi-pg-wrapper');
            const kunciPg = document.getElementById('kunci-pg-field');
            const rubrikEssay = document.getElementById('rubrik-essay-field');

            const toggleFields = () => {
                const isPg = tipeSelect.value === 'pg';
                opsiPg.style.display = isPg ? '' : 'none';
                kunciPg.style.display = isPg ? '' : 'none';
                rubrikEssay.style.display = isPg ? 'none' : '';
            };

            tipeSelect.addEventListener('change', toggleFields);
            toggleFields();
        })();
    </script>
</x-admin-layout>
