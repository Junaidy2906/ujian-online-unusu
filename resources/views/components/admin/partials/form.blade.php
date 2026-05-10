@props(['action', 'method' => 'POST', 'backUrl', 'item' => null, 'fields'])

<form method="POST" action="{{ $action }}" class="space-y-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    @csrf
    @if (! in_array($method, ['GET', 'POST']))
        @method($method)
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        @foreach ($fields as $field)
            <div class="{{ $field['full'] ?? false ? 'md:col-span-2' : '' }}">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $field['label'] }}</label>

                @if (($field['type'] ?? 'text') === 'textarea')
                    <textarea name="{{ $field['name'] }}" rows="{{ $field['rows'] ?? 4 }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old($field['name'], $field['value'] ?? data_get($item, $field['name'])) }}</textarea>
                @elseif (($field['type'] ?? 'text') === 'select')
                    <select name="{{ $field['name'] }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        <option value="">Pilih...</option>
                        @foreach ($field['options'] as $value => $label)
                            <option value="{{ $value }}" @selected(old($field['name'], $field['value'] ?? data_get($item, $field['name'])) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                @elseif (($field['type'] ?? 'text') === 'checkbox')
                    <label class="inline-flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-200">
                        <input type="checkbox" name="{{ $field['name'] }}" value="1" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500" @checked(old($field['name'], $field['value'] ?? data_get($item, $field['name'])))>
                        <span>{{ $field['checkbox_label'] ?? $field['label'] }}</span>
                    </label>
                @else
                    <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}" value="{{ old($field['name'], $field['value'] ?? data_get($item, $field['name'])) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @endif

                @error($field['name'])
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>

    <div class="flex items-center justify-between gap-3 pt-2">
        <a href="{{ $backUrl }}" class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Kembali</a>
        <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300 shadow-sm hover:bg-slate-800">Simpan</button>
    </div>
</form>
