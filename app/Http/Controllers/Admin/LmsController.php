<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\LmsMeeting;
use App\Models\LmsWeekSetting;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LmsController extends Controller
{
    public function index(): View
    {
        return view('admin.lms.index', [
            'setting' => LmsWeekSetting::query()->latest('id')->first(),
            'totalDosen' => Dosen::query()->count(),
            'totalKelas' => Kelas::query()->count(),
            'totalMahasiswa' => Mahasiswa::query()->count(),
            'semesterItems' => Semester::query()->with('tahunAkademik')->orderByDesc('is_active')->orderByDesc('id')->get(),
        ]);
    }

    public function saveSetting(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'week_1_start_date' => ['required', 'date'],
            'meeting_day' => ['required', 'integer', 'between:1,7'],
        ], [
            'week_1_start_date.required' => 'Tanggal awal minggu 1 wajib diisi.',
            'meeting_day.required' => 'Hari pertemuan wajib dipilih.',
        ]);

        LmsWeekSetting::query()->updateOrCreate(
            ['id' => LmsWeekSetting::query()->latest('id')->value('id')],
            $validated
        );

        return back()->with('success', 'Pengaturan minggu LMS berhasil disimpan.');
    }

    public function generateMeetings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'semester_id' => ['required', 'exists:semester,id'],
        ]);

        $semesterId = (int) $validated['semester_id'];
        $mataKuliahItems = MataKuliah::query()->where('is_active', true)->get(['id']);

        foreach ($mataKuliahItems as $mk) {
            for ($meeting = 1; $meeting <= 16; $meeting++) {
                LmsMeeting::query()->firstOrCreate([
                    'mata_kuliah_id' => $mk->id,
                    'semester_id' => $semesterId,
                    'meeting_number' => $meeting,
                ]);
            }
        }

        return back()->with('success', 'Default 16 pertemuan berhasil dibuat untuk semua mata kuliah aktif pada semester terpilih.');
    }
}
