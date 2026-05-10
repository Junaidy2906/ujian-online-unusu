<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function index(): View
    {
        return view('admin.semester.index', [
            'items' => Semester::with('tahunAkademik')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.semester.create', [
            'tahunAkademikItems' => TahunAkademik::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademik,id'],
            'nama' => ['required', 'string', 'max:50'],
            'urutan' => ['required', 'integer', 'min:1', 'max:20'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Semester::create([
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'nama' => $data['nama'],
            'urutan' => $data['urutan'],
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester): View
    {
        return view('admin.semester.edit', [
            'item' => $semester,
            'tahunAkademikItems' => TahunAkademik::latest()->get(),
        ]);
    }

    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $data = $request->validate([
            'tahun_akademik_id' => ['required', 'exists:tahun_akademik,id'],
            'nama' => ['required', 'string', 'max:50'],
            'urutan' => ['required', 'integer', 'min:1', 'max:20'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $semester->update([
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'nama' => $data['nama'],
            'urutan' => $data['urutan'],
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        $semester->delete();

        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil dihapus.');
    }
}