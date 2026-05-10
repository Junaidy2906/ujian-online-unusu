@props(['action', 'method' => 'POST', 'backUrl', 'item' => null, 'fields'])

<form method="POST" action="{{ $action }}" class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
    @csrf
    @if (! in_array($method, ['GET', 'POST']))
        @method($method)
    @endif

    <div class="border-b border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800 px-6 py-5 text-white sm:px-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-200">Form Data</p>
                <h3 class="mt-1 text-xl font-semibold">{{ $title ?? 'Kelola Data' }}</h3>
            </div>
            <a href="{{ $backUrl }}" class="inline-flex items-center rounded-2xl border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur hover:bg-white/15">Kembali</a>
        </div>
    </div>

    <div class="grid gap-0 lg:grid-cols-[1.05fr_0.95fr]">
        <div class="border-b border-slate-200 p-6 lg:border-b-0 lg:border-r">
            <div class="grid gap-5 md:grid-cols-2">
                @foreach ($fields as $field)
                    <div class="{{ $field['full'] ?? false ? 'md:col-span-2' : '' }}">
                        <label class="mb-2 block text-sm font-medium text-slate-700">{{ $field['label'] }}</label>

                        @if (($field['type'] ?? 'text') === 'textarea')
                            <textarea name="{{ $field['name'] }}" rows="{{ $field['rows'] ?? 4 }}" class="w-full rounded-2xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">{{ old($field['name'], data_get($item, $field['name'])) }}</textarea>
                        @elseif (($field['type'] ?? 'text') === 'select')
                            <select name="{{ $field['name'] }}" class="w-full rounded-2xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                                <option value="">Pilih...</option>
                                @foreach ($field['options'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old($field['name'], data_get($item, $field['name'])) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif (($field['type'] ?? 'text') === 'checkbox')
                            <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                                <input type="checkbox" name="{{ $field['name'] }}" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-900" @checked(old($field['name'], data_get($item, $field['name'])))>
                                <span>{{ $field['checkbox_label'] ?? $field['label'] }}</span>
                            </label>
                        @else
                            <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}" value="{{ old($field['name'], data_get($item, $field['name'])) }}" class="w-full rounded-2xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                        @endif

                        @error($field['name'])
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-slate-50 p-6 lg:p-8">
            <div class="rounded-[1.8rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Ringkasan</p>
                <h4 class="mt-2 text-lg font-semibold text-slate-900">Pastikan data sudah sesuai</h4>
                <p class="mt-3 text-sm leading-7 text-slate-600">Form ini mengikuti tampilan dashboard agar pengalaman admin, dosen, dan mahasiswa terasa konsisten. Simpan perubahan setelah semua field terisi.</p>

                <div class="mt-6 space-y-3 text-sm text-slate-600">
                    <div class="rounded-2xl bg-slate-100 px-4 py-3">Gunakan data yang valid dan unik.</div>
                    <div class="rounded-2xl bg-slate-100 px-4 py-3">Centang aktif jika data siap dipakai.</div>
                    <div class="rounded-2xl bg-slate-100 px-4 py-3">Kembali tanpa menyimpan jika perlu revisi.</div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between gap-3">
                <a href="{{ $backUrl }}" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-white">Batal</a>
                <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300 shadow-sm hover:bg-slate-800">Simpan</button>
            </div>
        </div>
    </div>
</form>