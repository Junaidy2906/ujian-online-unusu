<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\NilaiUjian;
use App\Models\Ujian;
use Illuminate\View\View;

class LaporanNilaiController extends Controller
{
    public function index(): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');

        return view('reports.ujian.index', [
            'title' => 'Rekap Nilai Dosen',
            'items' => Ujian::with(['kelas', 'mataKuliah', 'dosen.user'])->where('dosen_id', $dosenId)->latest()->get(),
            'reportRoute' => 'dosen.laporan-nilai.show',
        ]);
    }

    public function show(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $ujian->load(['kelas.mahasiswa.user', 'mataKuliah', 'dosen.user']);

        $studentIds = $ujian->kelas?->mahasiswa?->pluck('id') ?? collect();
        $scoreItems = NilaiUjian::with(['mahasiswa.user', 'percobaanUjian'])
            ->where('ujian_id', $ujian->id)
            ->whereIn('mahasiswa_id', $studentIds)
            ->orderByDesc('nilai_akhir')
            ->orderByDesc('created_at')
            ->get();

        // Cetak rekap hanya ambil nilai tertinggi tiap mahasiswa.
        $scores = $scoreItems
            ->groupBy('mahasiswa_id')
            ->map(function ($items) {
                return $items
                    ->sortByDesc(fn ($row) => (float) ($row->nilai_akhir ?? 0))
                    ->sortByDesc(fn ($row) => $row->created_at?->timestamp ?? 0)
                    ->first();
            });

        $students = $ujian->kelas?->mahasiswa?->sortBy(fn ($student) => $student->user?->name ?? '') ?? collect();

        return view('reports.ujian.show', [
            'title' => 'Rekap Nilai Ujian',
            'ujian' => $ujian,
            'students' => $students,
            'scores' => $scores,
            'printAllRoute' => route('dosen.laporan-nilai.show', $ujian),
            'printStudentRouteName' => 'dosen.laporan-nilai.mahasiswa',
        ]);
    }

    public function cetakMahasiswa(Ujian $ujian, Mahasiswa $mahasiswa): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $scores = NilaiUjian::with(['mahasiswa.user'])
            ->where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('created_at')
            ->get();

        return view('reports.ujian.student', [
            'title' => 'Cetak Nilai Mahasiswa',
            'ujian' => $ujian->load(['kelas', 'mataKuliah', 'dosen.user']),
            'student' => $mahasiswa->load('user'),
            'scores' => $scores,
            'printBackRoute' => route('dosen.laporan-nilai.show', $ujian),
        ]);
    }
}
