<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\NilaiUjian;
use App\Models\Soal;
use App\Models\TahunAkademik;
use App\Models\Ujian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UjianController extends Controller
{
    public function index(): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');

        return view('dosen.ujian.index', [
            'items' => Ujian::with(['kelas', 'mataKuliah', 'semester'])->where('dosen_id', $dosenId)->latest()->get(),
        ]);
    }

    public function create(): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');

        return view('dosen.ujian.create', [
            'tahunAkademikItems' => TahunAkademik::latest()->get(),
            'kelasItems' => Kelas::latest()->get(),
            'mataKuliahItems' => MataKuliah::where('dosen_id', $dosenId)->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');

        $data = $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademik,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'nama_ujian' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'jadwal_mulai' => ['required', 'date'],
            'jadwal_selesai' => ['nullable', 'date', 'after_or_equal:jadwal_mulai'],
            'durasi_menit' => ['required', 'integer', 'min:10', 'max:360'],
            'nilai_minimum_lulus' => ['required', 'numeric', 'min:0', 'max:100'],
            'maksimal_percobaan' => ['required', 'integer', 'min:1', 'max:10'],
            'bobot_pg' => ['required', 'numeric', 'min:0', 'max:100'],
            'bobot_essay' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,aktif,nonaktif,selesai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Ujian::create([
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'semester_id' => $data['semester_id'],
            'kelas_id' => $data['kelas_id'],
            'mata_kuliah_id' => $data['mata_kuliah_id'],
            'dosen_id' => $dosenId,
            'nama_ujian' => $data['nama_ujian'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'jadwal_mulai' => $data['jadwal_mulai'],
            'jadwal_selesai' => $data['jadwal_selesai'] ?? null,
            'durasi_menit' => $data['durasi_menit'],
            'nilai_minimum_lulus' => $data['nilai_minimum_lulus'],
            'maksimal_percobaan' => $data['maksimal_percobaan'],
            'bobot_pg' => $data['bobot_pg'],
            'bobot_essay' => $data['bobot_essay'],
            'status' => $data['status'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('dosen.ujian.index')->with('success', 'Ujian berhasil dibuat.');
    }

    public function edit(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');

        abort_unless($ujian->dosen_id === $dosenId, 403);

        return view('dosen.ujian.edit', [
            'item' => $ujian,
            'tahunAkademikItems' => TahunAkademik::latest()->get(),
            'kelasItems' => Kelas::latest()->get(),
            'mataKuliahItems' => MataKuliah::where('dosen_id', $dosenId)->latest()->get(),
        ]);
    }

    public function update(Request $request, Ujian $ujian): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $data = $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademik,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'nama_ujian' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'jadwal_mulai' => ['required', 'date'],
            'jadwal_selesai' => ['nullable', 'date', 'after_or_equal:jadwal_mulai'],
            'durasi_menit' => ['required', 'integer', 'min:10', 'max:360'],
            'nilai_minimum_lulus' => ['required', 'numeric', 'min:0', 'max:100'],
            'maksimal_percobaan' => ['required', 'integer', 'min:1', 'max:10'],
            'bobot_pg' => ['required', 'numeric', 'min:0', 'max:100'],
            'bobot_essay' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,aktif,nonaktif,selesai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $ujian->update([
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'semester_id' => $data['semester_id'],
            'kelas_id' => $data['kelas_id'],
            'mata_kuliah_id' => $data['mata_kuliah_id'],
            'nama_ujian' => $data['nama_ujian'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'jadwal_mulai' => $data['jadwal_mulai'],
            'jadwal_selesai' => $data['jadwal_selesai'] ?? null,
            'durasi_menit' => $data['durasi_menit'],
            'nilai_minimum_lulus' => $data['nilai_minimum_lulus'],
            'maksimal_percobaan' => $data['maksimal_percobaan'],
            'bobot_pg' => $data['bobot_pg'],
            'bobot_essay' => $data['bobot_essay'],
            'status' => $data['status'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('dosen.ujian.index')->with('success', 'Ujian berhasil diperbarui.');
    }

    public function destroy(Ujian $ujian): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $ujian->delete();

        return redirect()->route('dosen.ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }

    public function hasil(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        return view('dosen.ujian.hasil', [
            'ujian' => $ujian->load('kelas', 'mataKuliah'),
            'items' => NilaiUjian::with('mahasiswa.user')->where('ujian_id', $ujian->id)->latest()->get(),
        ]);
    }
}