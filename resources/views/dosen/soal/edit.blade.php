<x-admin-layout :title="'Edit Soal'">
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="mb-5 rounded-3xl border border-gray-200 bg-gradient-to-r from-slate-950 to-slate-800 p-5 text-white shadow-sm dark:border-gray-700">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200">Editor Soal</p>
        <h3 class="mt-2 text-xl font-semibold">{{ $ujian->nama_ujian }}</h3>
        <p class="mt-1 text-sm text-slate-200">Perbarui soal, bobot, dan kunci jawaban dengan cepat.</p>
    </div>

    <form method="POST" action="{{ route('dosen.ujian.soal.update', [$ujian, $soal]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h4 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">Informasi Soal</h4>
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor</label>
                    <input type="number" name="nomor" value="{{ old('nomor', $soal->nomor) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Tipe</label>
                    <select id="tipe" name="tipe" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        <option value="pg" @selected(old('tipe', $soal->tipe) === 'pg')>Pilihan Ganda</option>
                        <option value="essay" @selected(old('tipe', $soal->tipe) === 'essay')>Essai</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Pertanyaan</label>
                    <textarea name="pertanyaan" rows="5" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Bisa isi HTML (contoh: &lt;img&gt;, &lt;table&gt;).</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Poin</label>
                    <input type="number" step="0.01" name="poin" value="{{ old('poin', $soal->poin) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                </div>
                <div id="kunci-pg-field">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Jawaban Benar PG</label>
                    <input type="text" name="jawaban_benar" value="{{ old('jawaban_benar', $soal->jawaban_benar) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                </div>
                <div id="rubrik-essay-field" class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Rubrik Essai</label>
                    <textarea name="rubrik_penilaian" rows="3" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('rubrik_penilaian', $soal->rubrik_penilaian) }}</textarea>
                </div>
            </div>
        </section>

        <section id="opsi-pg-wrapper" class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Pilihan Jawaban</h4>
                <p class="text-xs text-gray-500">Untuk soal PG isi minimal dua opsi.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach (['A', 'B', 'C', 'D'] as $index => $kode)
                    @php $existing = $soal->pilihanJawaban->firstWhere('kode', $kode); @endphp
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700/40">
                        <label class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-100">Opsi {{ $kode }}</label>
                        <input type="text" name="pilihan[{{ $index }}][jawaban]" value="{{ old('pilihan.'.$index.'.jawaban', $existing?->jawaban) }}" class="w-full rounded-xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        <input type="hidden" name="pilihan[{{ $index }}][kode]" value="{{ $kode }}">
                        <label class="mt-3 inline-flex items-center gap-2 text-sm font-medium">
                            <input type="checkbox" name="pilihan[{{ $index }}][is_benar]" value="1" @checked(old('pilihan.'.$index.'.is_benar', $existing?->is_benar)) class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            Tandai sebagai jawaban benar
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="sticky bottom-3 z-20 rounded-2xl border border-gray-200 bg-white/90 p-3 shadow-lg backdrop-blur dark:border-gray-700 dark:bg-gray-800/90">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('dosen.ujian.soal.index', $ujian) }}" class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200">Kembali</a>
                <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-2.5 text-sm font-semibold text-amber-300 shadow-sm">Update Soal</button>
            </div>
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
