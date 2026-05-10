<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MataKuliahController extends Controller
{
    public function index(): View
    {
        return view('admin.mata-kuliah.index', [
            'items' => MataKuliah::with('dosen.user')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.mata-kuliah.create', [
            'dosenItems' => Dosen::with('user')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'dosen_id' => ['nullable', 'exists:dosen,id'],
            'kode_mk' => ['required', 'string', 'max:30', 'unique:mata_kuliah,kode_mk'],
            'nama_mk' => ['required', 'string', 'max:150'],
            'sks' => ['required', 'integer', 'min:1', 'max:24'],
            'prodi' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        MataKuliah::create([
            'dosen_id' => $data['dosen_id'] ?? null,
            'kode_mk' => $data['kode_mk'],
            'nama_mk' => $data['nama_mk'],
            'sks' => $data['sks'],
            'prodi' => $data['prodi'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $mataKuliah): View
    {
        return view('admin.mata-kuliah.edit', [
            'item' => $mataKuliah,
            'dosenItems' => Dosen::with('user')->latest()->get(),
        ]);
    }

    public function update(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $data = $request->validate([
            'dosen_id' => ['nullable', 'exists:dosen,id'],
            'kode_mk' => ['required', 'string', 'max:30', 'unique:mata_kuliah,kode_mk,'.$mataKuliah->id],
            'nama_mk' => ['required', 'string', 'max:150'],
            'sks' => ['required', 'integer', 'min:1', 'max:24'],
            'prodi' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $mataKuliah->update([
            'dosen_id' => $data['dosen_id'] ?? null,
            'kode_mk' => $data['kode_mk'],
            'nama_mk' => $data['nama_mk'],
            'sks' => $data['sks'],
            'prodi' => $data['prodi'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $mataKuliah): RedirectResponse
    {
        $mataKuliah->delete();

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}