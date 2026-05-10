<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MahasiswaController extends Controller
{
    public function index(): View
    {
        return view('admin.mahasiswa.index', [
            'items' => Mahasiswa::with('user')->latest()->get(),
        ]);
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load(['user', 'kelas', 'percobaanUjian.ujian', 'nilaiUjian']);

        return view('admin.mahasiswa.show', [
            'item' => $mahasiswa,
            'totalKelas' => $mahasiswa->kelas->count(),
            'totalPercobaan' => $mahasiswa->percobaanUjian->count(),
            'totalNilai' => $mahasiswa->nilaiUjian->whereNotNull('nilai_akhir')->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.mahasiswa.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nim' => ['required', 'string', 'max:30', 'unique:mahasiswa,nim'],
            'prodi' => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,cuti,nonaktif'],
        ]);

        DB::transaction(function () use ($data): void {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_MAHASISWA,
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'prodi' => $data['prodi'] ?? null,
                'angkatan' => $data['angkatan'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'status' => $data['status'],
            ]);
        });

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load('user');

        return view('admin.mahasiswa.edit', [
            'item' => $mahasiswa,
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->load('user');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$mahasiswa->user_id],
            'password' => ['nullable', 'string', 'min:8'],
            'nim' => ['required', 'string', 'max:30', 'unique:mahasiswa,nim,'.$mahasiswa->id],
            'prodi' => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,cuti,nonaktif'],
        ]);

        DB::transaction(function () use ($data, $mahasiswa): void {
            $mahasiswa->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => filled($data['password'] ?? null) ? Hash::make($data['password']) : $mahasiswa->user->password,
                'role' => User::ROLE_MAHASISWA,
            ]);

            $mahasiswa->update([
                'nim' => $data['nim'],
                'prodi' => $data['prodi'] ?? null,
                'angkatan' => $data['angkatan'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'status' => $data['status'],
            ]);
        });

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $user = $mahasiswa->user;
        $mahasiswa->delete();
        $user?->delete();

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = ['name', 'email', 'password', 'nim', 'prodi', 'angkatan', 'telepon', 'alamat', 'status'];
        $sample = ['Nama Mahasiswa', 'mahasiswa@contoh.ac.id', 'password123', '23100001', 'Teknik Informatika', '2023', '08123456789', 'Alamat mahasiswa', 'aktif'];

        $callback = function () use ($headers, $sample): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            fputcsv($output, $sample);
            fclose($output);
        };

        return response()->streamDownload($callback, 'template_mahasiswa.csv', [
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

                if (($rowData['name'] ?? '') === '' || ($rowData['email'] ?? '') === '' || ($rowData['password'] ?? '') === '' || ($rowData['nim'] ?? '') === '') {
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $rowData['email']],
                    [
                        'name' => $rowData['name'],
                        'password' => Hash::make($rowData['password']),
                        'role' => User::ROLE_MAHASISWA,
                    ]
                );

                Mahasiswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nim' => $rowData['nim'],
                        'prodi' => $rowData['prodi'] !== '' ? $rowData['prodi'] : null,
                        'angkatan' => $rowData['angkatan'] !== '' ? $rowData['angkatan'] : null,
                        'telepon' => $rowData['telepon'] !== '' ? $rowData['telepon'] : null,
                        'alamat' => $rowData['alamat'] !== '' ? $rowData['alamat'] : null,
                        'status' => in_array(strtolower((string) ($rowData['status'] ?? 'aktif')), ['aktif', 'cuti', 'nonaktif'], true)
                            ? strtolower((string) $rowData['status'])
                            : 'aktif',
                    ]
                );

                $created++;
            }
            fclose($file);
        });

        return back()->with('success', "Upload data mahasiswa selesai. Baris diproses: {$created}.");
    }
}
