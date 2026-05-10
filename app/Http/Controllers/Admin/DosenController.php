<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DosenController extends Controller
{
    public function index(): View
    {
        return view('admin.dosen.index', [
            'items' => Dosen::with('user')->latest()->get(),
        ]);
    }

    public function show(Dosen $dosen): View
    {
        $dosen->load(['user', 'mataKuliah', 'kelasWali', 'ujian']);

        return view('admin.dosen.show', [
            'item' => $dosen,
            'totalMataKuliah' => $dosen->mataKuliah->count(),
            'totalKelas' => $dosen->kelasWali->count(),
            'totalUjian' => $dosen->ujian->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.dosen.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nidn' => ['nullable', 'string', 'max:30', 'unique:dosen,nidn'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $request): void {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_DOSEN,
            ]);

            Dosen::create([
                'user_id' => $user->id,
                'nidn' => $data['nidn'] ?? null,
                'gelar_depan' => $data['gelar_depan'] ?? null,
                'gelar_belakang' => $data['gelar_belakang'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen): View
    {
        $dosen->load('user');

        return view('admin.dosen.edit', [
            'item' => $dosen,
        ]);
    }

    public function update(Request $request, Dosen $dosen): RedirectResponse
    {
        $dosen->load('user');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$dosen->user_id],
            'password' => ['nullable', 'string', 'min:8'],
            'nidn' => ['nullable', 'string', 'max:30', 'unique:dosen,nidn,'.$dosen->id],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $request, $dosen): void {
            $dosen->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => filled($data['password'] ?? null) ? Hash::make($data['password']) : $dosen->user->password,
                'role' => User::ROLE_DOSEN,
            ]);

            $dosen->update([
                'nidn' => $data['nidn'] ?? null,
                'gelar_depan' => $data['gelar_depan'] ?? null,
                'gelar_belakang' => $data['gelar_belakang'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen): RedirectResponse
    {
        $user = $dosen->user;
        $dosen->delete();
        $user?->delete();

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil dihapus.');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = ['name', 'email', 'password', 'nidn', 'gelar_depan', 'gelar_belakang', 'telepon', 'alamat', 'is_active'];
        $sample = ['Nama Dosen', 'dosen@contoh.ac.id', 'password123', '1234567890', 'Dr.', 'M.Kom', '08123456789', 'Alamat dosen', '1'];

        $callback = function () use ($headers, $sample): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            fputcsv($output, $sample);
            fclose($output);
        };

        return response()->streamDownload($callback, 'template_dosen.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function uploadTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'template_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = fopen($data['template_file']->getRealPath(), 'r');
        $header = fgetcsv($file);

        if (! $header) {
            return back()->withErrors(['template_file' => 'File template kosong.']);
        }

        $header = array_map(fn ($value) => strtolower(trim((string) $value)), $header);
        $created = 0;

        DB::transaction(function () use ($file, $header, &$created): void {
            while (($row = fgetcsv($file)) !== false) {
                if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                    continue;
                }

                $rowData = [];
                foreach ($header as $index => $column) {
                    $rowData[$column] = trim((string) ($row[$index] ?? ''));
                }

                if (($rowData['name'] ?? '') === '' || ($rowData['email'] ?? '') === '' || ($rowData['password'] ?? '') === '') {
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $rowData['email']],
                    [
                        'name' => $rowData['name'],
                        'password' => Hash::make($rowData['password']),
                        'role' => User::ROLE_DOSEN,
                    ]
                );

                Dosen::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nidn' => $rowData['nidn'] !== '' ? $rowData['nidn'] : null,
                        'gelar_depan' => $rowData['gelar_depan'] !== '' ? $rowData['gelar_depan'] : null,
                        'gelar_belakang' => $rowData['gelar_belakang'] !== '' ? $rowData['gelar_belakang'] : null,
                        'telepon' => $rowData['telepon'] !== '' ? $rowData['telepon'] : null,
                        'alamat' => $rowData['alamat'] !== '' ? $rowData['alamat'] : null,
                        'is_active' => in_array(strtolower((string) ($rowData['is_active'] ?? '1')), ['1', 'true', 'ya', 'aktif'], true),
                    ]
                );

                $created++;
            }
            fclose($file);
        });

        return back()->with('success', "Upload data dosen selesai. Baris diproses: {$created}.");
    }
}
