<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        return view('admin.kelas.index', [
            'items' => Kelas::with(['tahunAkademik', 'semester', 'dosenWali'])->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.kelas.create', [
            'tahunAkademikItems' => TahunAkademik::latest()->get(),
            'semesterItems' => Semester::with('tahunAkademik')->latest()->get(),
            'dosenItems' => Dosen::with('user')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademik,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'dosen_wali_id' => ['nullable', 'exists:dosen,id'],
            'kode_kelas' => ['required', 'string', 'max:30', 'unique:kelas,kode_kelas'],
            'nama_kelas' => ['required', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Kelas::create([
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'semester_id' => $data['semester_id'],
            'dosen_wali_id' => $data['dosen_wali_id'] ?? null,
            'kode_kelas' => $data['kode_kelas'],
            'nama_kelas' => $data['nama_kelas'],
            'angkatan' => $data['angkatan'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        $kelas->load('mahasiswa');

        return view('admin.kelas.edit', [
            'item' => $kelas,
            'tahunAkademikItems' => TahunAkademik::latest()->get(),
            'semesterItems' => Semester::with('tahunAkademik')->latest()->get(),
            'dosenItems' => Dosen::with('user')->latest()->get(),
            'mahasiswaItems' => Mahasiswa::with('user')->orderBy('nim')->get(),
            'selectedMahasiswaIds' => $kelas->mahasiswa->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademik,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'dosen_wali_id' => ['nullable', 'exists:dosen,id'],
            'kode_kelas' => ['required', 'string', 'max:30', 'unique:kelas,kode_kelas,'.$kelas->id],
            'nama_kelas' => ['required', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'mahasiswa_ids' => ['nullable', 'array'],
            'mahasiswa_ids.*' => ['integer', 'exists:mahasiswa,id'],
        ]);

        $kelas->update([
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'semester_id' => $data['semester_id'],
            'dosen_wali_id' => $data['dosen_wali_id'] ?? null,
            'kode_kelas' => $data['kode_kelas'],
            'nama_kelas' => $data['nama_kelas'],
            'angkatan' => $data['angkatan'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $kelas->mahasiswa()->sync($data['mahasiswa_ids'] ?? []);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
