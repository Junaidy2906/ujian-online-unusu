<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandingController extends Controller
{
    public function edit(): View
    {
        $setting = SiteSetting::firstOrCreate(
            ['id' => 1],
            ['app_name' => 'UJIAN ONLINE UNUSU']
        );

        return view('admin.branding.edit', [
            'setting' => $setting,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = SiteSetting::firstOrCreate(
            ['id' => 1],
            ['app_name' => 'UJIAN ONLINE UNUSU']
        );

        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'campus_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_campus_image' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_logo') && $setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
            $setting->logo_path = null;
        }

        if ($request->boolean('remove_campus_image') && $setting->campus_image_path) {
            Storage::disk('public')->delete($setting->campus_image_path);
            $setting->campus_image_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = $this->cropAndStoreImage(
                $request->file('logo'),
                1,
                1,
                'branding/logo'
            );
        }

        if ($request->hasFile('campus_image')) {
            if ($setting->campus_image_path) {
                Storage::disk('public')->delete($setting->campus_image_path);
            }
            $setting->campus_image_path = $this->cropAndStoreImage(
                $request->file('campus_image'),
                16,
                9,
                'branding/campus'
            );
        }

        $setting->app_name = $data['app_name'];
        $setting->save();

        return back()->with('success', 'Branding aplikasi berhasil diperbarui.');
    }

    private function cropAndStoreImage(UploadedFile $file, int $ratioW, int $ratioH, string $directory): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'svg') {
            return $file->store($directory, 'public');
        }

        [$width, $height, $type] = getimagesize($file->getRealPath());

        $source = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_WEBP => imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (! $source) {
            return $file->store($directory, 'public');
        }

        $targetRatio = $ratioW / $ratioH;
        $sourceRatio = $width / $height;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $height;
            $cropWidth = (int) round($height * $targetRatio);
            $srcX = (int) floor(($width - $cropWidth) / 2);
            $srcY = 0;
        } else {
            $cropWidth = $width;
            $cropHeight = (int) round($width / $targetRatio);
            $srcX = 0;
            $srcY = (int) floor(($height - $cropHeight) / 2);
        }

        $dest = imagecreatetruecolor($cropWidth, $cropHeight);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        imagecopyresampled($dest, $source, 0, 0, $srcX, $srcY, $cropWidth, $cropHeight, $cropWidth, $cropHeight);

        $filename = $directory.'/'.uniqid('branding_', true).'.webp';
        $fullPath = storage_path('app/public/'.$filename);
        if (! is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        imagewebp($dest, $fullPath, 90);
        imagedestroy($source);
        imagedestroy($dest);

        return $filename;
    }
}
