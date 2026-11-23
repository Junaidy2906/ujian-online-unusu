<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LmsMaterial;
use App\Models\LmsWeekSetting;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LmsController extends Controller
{
    public function index(): View
    {
        $mahasiswa = Mahasiswa::query()->where('user_id', auth()->id())->first();
        $setting = LmsWeekSetting::query()->latest('id')->first();
        $currentWeek = $this->resolveCurrentWeek($setting);

        $mataKuliah = MataKuliah::query()
            ->when($mahasiswa?->prodi, fn ($q) => $q->where('prodi', $mahasiswa->prodi))
            ->where('is_active', true)
            ->with(['dosen.user'])
            ->orderBy('nama_mk')
            ->get();

        $materials = LmsMaterial::query()
            ->whereIn('mata_kuliah_id', $mataKuliah->pluck('id'))
            ->when($currentWeek > 0, fn ($q) => $q->where('week_number', '<=', $currentWeek), fn ($q) => $q->whereRaw('1 = 0'))
            ->with(['mataKuliah', 'meeting.semester'])
            ->orderBy('week_number')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('mata_kuliah_id');

        return view('mahasiswa.lms.index', [
            'setting' => $setting,
            'currentWeek' => $currentWeek,
            'mataKuliah' => $mataKuliah,
            'materials' => $materials,
            'unlockedIds' => collect(session('lms_unlocked', []))->map(fn ($id) => (int) $id)->all(),
            'typeOptions' => LmsMaterial::typeOptions(),
        ]);
    }

    public function unlock(Request $request, LmsMaterial $material): RedirectResponse
    {
        $validated = $request->validate([
            'access_code' => ['required', 'string'],
        ]);

        if (! $material->access_code || ! hash_equals((string) $material->access_code, (string) $validated['access_code'])) {
            return back()->withErrors(['access_code' => 'Kode akses tidak sesuai.']);
        }

        $unlocked = collect(session('lms_unlocked', []))->map(fn ($id) => (int) $id);
        if (! $unlocked->contains((int) $material->id)) {
            $unlocked->push((int) $material->id);
            session(['lms_unlocked' => $unlocked->values()->all()]);
        }

        return back()->with('success', 'Kode akses benar. Materi sudah terbuka.');
    }

    private function resolveCurrentWeek(?LmsWeekSetting $setting): int
    {
        if (! $setting) {
            return 0;
        }

        $start = Carbon::parse($setting->week_1_start_date)->startOfDay();
        $today = now()->startOfDay();

        if ($today->lt($start)) {
            return 0;
        }

        return (int) floor($start->diffInDays($today) / 7) + 1;
    }
}
