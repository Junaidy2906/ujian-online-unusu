<x-admin-layout :title="'Branding Aplikasi'">
    <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Aplikasi</label>
                <input type="text" name="app_name" value="{{ old('app_name', $setting->app_name) }}" class="w-full rounded-2xl border-gray-300 bg-white text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('app_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-700/50 dark:text-gray-200">
                Upload logo dan gambar kampus di sini. File akan otomatis dipakai di Landing Page dan Login Page.
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-700">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Logo Aplikasi</label>
                <input id="logo-input" type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.svg" class="w-full rounded-xl border border-gray-300 bg-white p-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('logo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    Hapus logo saat ini
                </label>

                <div class="mt-4 rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Preview Logo (Auto Crop 1:1)</p>
                    <div id="logo-preview-wrap" class="h-36 w-36 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700">
                        @if ($setting->logo_path)
                            <img id="logo-preview" src="{{ asset('storage/'.$setting->logo_path) }}" alt="Logo Aplikasi" class="h-full w-full object-cover">
                        @else
                            <img id="logo-preview" src="{{ asset('images/default-unusu-logo.svg') }}" alt="Logo Default" class="h-full w-full object-contain p-2">
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-700">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Gambar Kampus</label>
                <input id="campus-input" type="file" name="campus_image" accept=".jpg,.jpeg,.png,.webp" class="w-full rounded-xl border border-gray-300 bg-white p-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                @error('campus_image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="checkbox" name="remove_campus_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    Hapus gambar kampus saat ini
                </label>

                <div class="mt-4 rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Preview Gambar Kampus (Auto Crop 16:9)</p>
                    <div class="aspect-video overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700">
                        @if ($setting->campus_image_path)
                            <img id="campus-preview" src="{{ asset('storage/'.$setting->campus_image_path) }}" alt="Gambar Kampus" class="h-full w-full object-cover">
                        @else
                            <div id="campus-preview" class="grid h-full w-full place-items-center text-sm text-gray-500">Belum ada gambar kampus</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-2 text-sm font-semibold text-amber-300">Simpan Branding</button>
        </div>
    </form>

    <script>
        (function () {
            const setPreview = (inputId, previewId, isImageTag = true) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener('change', function () {
                    const file = this.files && this.files[0];
                    if (!file) return;

                    const url = URL.createObjectURL(file);
                    if (isImageTag) {
                        preview.src = url;
                    } else {
                        preview.innerHTML = '';
                        const img = document.createElement('img');
                        img.src = url;
                        img.className = 'h-full w-full object-cover';
                        preview.appendChild(img);
                    }
                });
            };

            setPreview('logo-input', 'logo-preview', true);
            setPreview('campus-input', 'campus-preview', false);
        })();
    </script>
</x-admin-layout>
