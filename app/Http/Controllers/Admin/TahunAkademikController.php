<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAkademikController extends Controller
{
    public function index(): View
    {
        return view('admin.tahun-akademik.index', [
            'items' => TahunAkademik::latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.tahun-akademik.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'kode' => ['required', 'string', 'max:20', 'unique:tahun_akademik,kode'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        TahunAkademik::create([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.tahun-akademik.index')->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    public function edit(TahunAkademik $tahunAkademik): View
    {
        return view('admin.tahun-akademik.edit', [
            'item' => $tahunAkademik,
        ]);
    }

    public function update(Request $request, TahunAkademik $tahunAkademik): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'kode' => ['required', 'string', 'max:20', 'unique:tahun_akademik,kode,'.$tahunAkademik->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $tahunAkademik->update([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.tahun-akademik.index')->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    public function destroy(TahunAkademik $tahunAkademik): RedirectResponse
    {
        $tahunAkademik->delete();

        return redirect()->route('admin.tahun-akademik.index')->with('success', 'Tahun akademik berhasil dihapus.');
    }
}