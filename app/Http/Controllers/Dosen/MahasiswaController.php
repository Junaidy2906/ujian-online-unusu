<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $kelasId = $request->integer('kelas_id');

        $kelasItems = Kelas::query()
            ->withCount('mahasiswa')
            ->whereHas('mahasiswa')
            ->orderBy('nama_kelas')
            ->get();

        $selectedKelasId = $kelasId ?: (int) ($kelasItems->first()->id ?? 0);
        $selectedKelas = $selectedKelasId
            ? Kelas::with(['mahasiswa.user'])->find($selectedKelasId)
            : null;

        return view('dosen.mahasiswa.index', [
            'kelasItems' => $kelasItems,
            'selectedKelas' => $selectedKelas,
            'selectedKelasId' => $selectedKelasId,
        ]);
    }
}
