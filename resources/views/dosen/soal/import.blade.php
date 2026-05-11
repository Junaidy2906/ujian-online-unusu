<x-admin-layout :title="'Import Soal Massal'">
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('dosen.ujian.soal.import.store', $ujian) }}" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf

        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Import Soal Pilihan Ganda</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Bisa paste teks langsung atau upload file <code>.txt</code>.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Poin Default per Soal</label>
                <input type="number" step="0.01" name="poin_default" value="{{ old('poin_default', 1) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Upload File TXT (Opsional)</label>
                <input type="file" name="soal_file" accept=".txt,text/plain" class="w-full rounded-2xl border-gray-300 bg-white text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-amber-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Paste Teks Soal (Opsional jika upload file)</label>
                <textarea name="soal_text" rows="16" class="w-full rounded-2xl border-gray-300 bg-white font-mono text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('soal_text') }}</textarea>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800">
            <p class="font-semibold">Contoh format:</p>
            <pre class="mt-2 whitespace-pre-wrap">Bahasa pemrograman yang digunakan pada kode tersebut adalah ...
A. Java
B. Python
C. Visual Basic .NET
D. PHP
Jawaban: C</pre>
        </div>

        <div class="flex items-center justify-between gap-3 pt-2">
            <a href="{{ route('dosen.ujian.soal.index', $ujian) }}" class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200">Kembali</a>
            <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300">Import Sekarang</button>
        </div>
    </form>
</x-admin-layout>
