<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\NilaiUjian;
use App\Models\Ujian;
use Illuminate\View\View;

class LaporanNilaiController extends Controller
{
    public function index(): View
    {
        return view('reports.ujian.index', [
            'title' => 'Rekap Nilai',
            'items' => Ujian::with(['kelas', 'mataKuliah', 'dosen.user'])->latest()->get(),
            'reportRoute' => 'admin.laporan-nilai.show',
        ]);
    }

    public function show(Ujian $ujian): View
    {
        $ujian->load(['kelas.mahasiswa.user', 'mataKuliah', 'dosen.user']);

        $studentIds = $ujian->kelas?->mahasiswa?->pluck('id') ?? collect();
        $scores = NilaiUjian::with(['mahasiswa.user'])
            ->where('ujian_id', $ujian->id)
            ->whereIn('mahasiswa_id', $studentIds)
            ->orderByDesc('nilai_akhir')
            ->get()
            ->keyBy('mahasiswa_id');

        $students = $ujian->kelas?->mahasiswa?->sortBy(fn ($student) => $student->user?->name ?? '') ?? collect();

        return view('reports.ujian.show', [
            'title' => 'Rekap Nilai Ujian',
            'ujian' => $ujian,
            'students' => $students,
            'scores' => $scores,
            'printAllRoute' => route('admin.laporan-nilai.show', $ujian),
            'printStudentRouteName' => 'admin.laporan-nilai.mahasiswa',
        ]);
    }

    public function cetakMahasiswa(Ujian $ujian, Mahasiswa $mahasiswa): View
    {
        $ujian->load(['kelas', 'mataKuliah', 'dosen.user']);

        $scores = NilaiUjian::with(['mahasiswa.user'])
            ->where('ujian_id', $ujian->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('created_at')
            ->get();

        return view('reports.ujian.student', [
            'title' => 'Cetak Nilai Mahasiswa',
            'ujian' => $ujian,
            'student' => $mahasiswa->load('user'),
            'scores' => $scores,
            'printBackRoute' => route('admin.laporan-nilai.show', $ujian),
        ]);
    }
}