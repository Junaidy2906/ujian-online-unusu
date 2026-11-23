<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MahasiswaController extends Controller
{
    private const FALLBACK_EMAIL_DOMAIN = 'student.local';

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'semua');
        $prodi = trim((string) $request->query('prodi', ''));
        $kelasFilter = (string) $request->query('kelas', '');

        $query = Mahasiswa::with(['user', 'kelas']);

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('nim', 'like', '%'.$search.'%')
                    ->orWhere('angkatan', 'like', '%'.$search.'%')
                    ->orWhere('semester', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        if (in_array($status, ['aktif', 'cuti', 'nonaktif'], true)) {
            $query->where('status', $status);
        }

        if ($prodi !== '') {
            $query->where('prodi', $prodi);
        }

        if ($kelasFilter === 'tanpa') {
            $query->whereDoesntHave('kelas');
        } elseif (ctype_digit($kelasFilter)) {
            $query->whereHas('kelas', fn ($kelasQuery) => $kelasQuery->where('kelas.id', (int) $kelasFilter));
        }

        return view('admin.mahasiswa.index', [
            'items' => $query->latest()->get(),
            'prodiItems' => Mahasiswa::query()
                ->select('prodi')
                ->whereNotNull('prodi')
                ->where('prodi', '!=', '')
                ->distinct()
                ->orderBy('prodi')
                ->pluck('prodi'),
            'kelasItems' => $this->kelasOptions(),
            'filters' => [
                'q' => $search,
                'status' => in_array($status, ['semua', 'aktif', 'cuti', 'nonaktif'], true) ? $status : 'semua',
                'prodi' => $prodi,
                'kelas' => $kelasFilter === 'tanpa' || ctype_digit($kelasFilter) ? $kelasFilter : '',
            ],
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
        return view('admin.mahasiswa.create', [
            'kelasItems' => $this->kelasOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'nim' => ['required', 'string', 'max:30', 'unique:mahasiswa,nim'],
            'prodi' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,cuti,nonaktif'],
            'kelas_ids' => ['nullable', 'array'],
            'kelas_ids.*' => ['integer', 'exists:kelas,id'],
        ]);

        DB::transaction(function () use ($data): void {
            $defaultPassword = $data['nim'];
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(($data['password'] ?? null) ?: $defaultPassword),
                'role' => User::ROLE_MAHASISWA,
            ]);

            $mahasiswa = Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'prodi' => $data['prodi'] ?? null,
                'semester' => $data['semester'] ?? null,
                'angkatan' => $data['angkatan'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'status' => $data['status'],
            ]);

            $mahasiswa->kelas()->sync($data['kelas_ids'] ?? []);
        });

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load(['user', 'kelas']);

        return view('admin.mahasiswa.edit', [
            'item' => $mahasiswa,
            'kelasItems' => $this->kelasOptions(),
            'selectedKelasIds' => $mahasiswa->kelas->pluck('id')->all(),
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
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,cuti,nonaktif'],
            'kelas_ids' => ['nullable', 'array'],
            'kelas_ids.*' => ['integer', 'exists:kelas,id'],
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
                'semester' => $data['semester'] ?? null,
                'angkatan' => $data['angkatan'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'status' => $data['status'],
            ]);

            $mahasiswa->kelas()->sync($data['kelas_ids'] ?? []);
        });

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function bulkKelas(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mahasiswa_ids' => ['required', 'array', 'min:1'],
            'mahasiswa_ids.*' => ['integer', 'exists:mahasiswa,id'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'aksi' => ['required', 'in:tambah,pindah,keluarkan'],
        ], [
            'mahasiswa_ids.required' => 'Pilih minimal satu mahasiswa terlebih dahulu.',
            'kelas_id.required' => 'Pilih kelas tujuan terlebih dahulu.',
        ]);

        $kelas = Kelas::findOrFail($data['kelas_id']);
        $ids = array_map('intval', $data['mahasiswa_ids']);

        DB::transaction(function () use ($data, $kelas, $ids): void {
            if ($data['aksi'] === 'keluarkan') {
                $kelas->mahasiswa()->detach($ids);

                return;
            }

            if ($data['aksi'] === 'pindah') {
                // Remove from every other class first, then place in the chosen one.
                DB::table('kelas_mahasiswa')
                    ->whereIn('mahasiswa_id', $ids)
                    ->where('kelas_id', '!=', $kelas->id)
                    ->delete();
            }

            $kelas->mahasiswa()->syncWithoutDetaching($ids);
        });

        $label = match ($data['aksi']) {
            'tambah' => 'dimasukkan ke',
            'pindah' => 'dipindahkan ke',
            'keluarkan' => 'dikeluarkan dari',
        };

        return back()->with('success', count($ids)." mahasiswa berhasil {$label} kelas {$kelas->nama_kelas}.");
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $user = $mahasiswa->user;
        $mahasiswa->delete();
        $user?->delete();

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $headers = ['name', 'email', 'password', 'nim', 'prodi', 'semester', 'angkatan', 'telepon', 'alamat', 'status', 'kode_kelas'];
        $sample = ['Nama Mahasiswa', 'mahasiswa@contoh.ac.id', 'password123', '23100001', 'Teknik Informatika', '2', '2023', '08123456789', 'Alamat mahasiswa', 'aktif', Kelas::query()->value('kode_kelas') ?? 'KODE-KELAS'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($sample, null, 'A2');

        $tempFile = tempnam(sys_get_temp_dir(), 'template_mahasiswa_').'.xlsx';
        (new Xlsx($spreadsheet))->save($tempFile);

        return response()->download($tempFile, 'template_mahasiswa.xlsx')->deleteFileAfterSend(true);
    }

    public function uploadTemplate(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'template_file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        try {
            $rows = $this->readSpreadsheetRows($data['template_file']->getRealPath());
        } catch (SpreadsheetException) {
            throw ValidationException::withMessages([
                'template_file' => 'File tidak dapat dibaca. Periksa formatnya lalu coba lagi.',
            ]);
        }

        $header = array_map(
            fn ($value) => strtolower(trim((string) $value, "\xEF\xBB\xBF \t\n\r\0\x0B")),
            array_shift($rows) ?? [],
        );
        if (count(array_intersect(['name', 'email', 'nim'], $header)) !== 3) {
            throw ValidationException::withMessages([
                'template_file' => 'Gunakan template dengan kolom name, email, dan nim pada baris pertama.',
            ]);
        }

        $students = [];
        $skipped = 0;

        foreach ($rows as $row) {
            $values = array_map(fn ($value) => trim((string) $value), $row);
            if (count(array_filter($values, fn ($value) => $value !== '')) === 0) {
                continue;
            }

            $student = array_combine($header, $values);
            $validator = Validator::make($student, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255'],
                'nim' => ['required', 'string', 'max:30'],
                'prodi' => ['nullable', 'string', 'max:100'],
                'angkatan' => ['nullable', 'string', 'max:10'],
                'telepon' => ['nullable', 'string', 'max:20'],
            ]);

            if ($validator->fails() || strlen($student['alamat'] ?? '') > 65535
                || str_contains($student['password'] ?? '', "\0")) {
                $skipped++;

                continue;
            }

            // The last valid row for a NIM is the one saved.
            $students[$student['nim']] = $student;
        }

        if ($students === []) {
            throw ValidationException::withMessages([
                'template_file' => 'Tidak ada baris mahasiswa yang valid. Periksa nama, email, dan NIM.',
            ]);
        }

        try {
            [$created, $updated, $adjustedEmails] = retry(
                3,
                fn () => DB::transaction(fn () => $this->importStudents($students), 3),
                0,
                fn ($exception) => $exception instanceof UniqueConstraintViolationException,
            );
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'template_file' => 'Ada konflik NIM atau email saat menyimpan data. Coba unggah kembali.',
            ]);
        }

        $message = "Impor selesai. {$created} ditambahkan, {$updated} diperbarui, {$skipped} baris tidak valid dilewati.";
        if ($adjustedEmails > 0) {
            $message .= " {$adjustedEmails} mahasiswa memakai email lokal karena email pada file tidak valid atau sudah dipakai.";
        }

        if ($request->expectsJson()) {
            $request->session()->flash('success', $message);

            return response()->json(['redirect' => route('admin.mahasiswa.index', absolute: false)]);
        }

        return redirect()->route('admin.mahasiswa.index')->with('success', $message);
    }

    private function importStudents(array $students): array
    {
        $created = 0;
        $updated = 0;
        $adjustedEmails = 0;
        $kelasByKode = Kelas::query()->pluck('id', 'kode_kelas')
            ->mapWithKeys(fn ($id, $kode) => [strtolower(trim((string) $kode)) => $id]);

        foreach ($students as $row) {
            $student = Mahasiswa::query()->where('nim', $row['nim'])->lockForUpdate()->first();
            $user = $student ? $student->user()->lockForUpdate()->firstOrFail() : new User;
            $email = $this->resolveUniqueEmail($row['email'], $row['nim'], $user->id);

            $user->fill(['name' => $row['name'], 'email' => $email]);
            $password = $row['password'] ?? '';
            if ($password !== '' || ! $user->exists) {
                $user->password = Hash::make($password !== '' ? $password : $row['nim']);
            }
            if (! $user->exists) {
                $user->role = User::ROLE_MAHASISWA;
            }
            $user->save();

            $profile = [];
            foreach (['prodi', 'angkatan', 'telepon', 'alamat'] as $column) {
                if (array_key_exists($column, $row)) {
                    $profile[$column] = $row[$column] === '' ? null : $row[$column];
                }
            }
            if (array_key_exists('semester', $row)) {
                $profile['semester'] = $row['semester'] === '' ? null : max(1, min(14, (int) $row['semester']));
            }
            if (array_key_exists('status', $row)) {
                $status = strtolower($row['status']);
                $profile['status'] = in_array($status, ['aktif', 'cuti', 'nonaktif'], true) ? $status : 'aktif';
            }

            if ($student) {
                $student->update($profile);
                $updated++;
            } else {
                $student = Mahasiswa::create(['user_id' => $user->id, 'nim' => $row['nim'], ...$profile]);
                $created++;
            }

            // Optional "kode_kelas" column: one or more class codes separated by comma/semicolon.
            $kelasIds = collect(preg_split('/[,;]+/', (string) ($row['kode_kelas'] ?? '')))
                ->map(fn ($kode) => $kelasByKode->get(strtolower(trim($kode))))
                ->filter()
                ->unique()
                ->values()
                ->all();
            if ($kelasIds !== []) {
                $student->kelas()->syncWithoutDetaching($kelasIds);
            }

            if ($email !== $row['email']) {
                $adjustedEmails++;
            }
        }

        return [$created, $updated, $adjustedEmails];
    }

    private function kelasOptions()
    {
        return Kelas::query()
            ->with(['semester', 'tahunAkademik'])
            ->withCount('mahasiswa')
            ->orderByDesc('is_active')
            ->orderBy('nama_kelas')
            ->get();
    }

    private function readSpreadsheetRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);

        try {
            return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    private function resolveUniqueEmail(string $email, string $nim, ?int $ignoreUserId): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && ! $this->emailExists($email, $ignoreUserId)) {
            return $email;
        }

        // Keep an email already assigned to another account with its owner.
        $candidate = $nim.'@'.self::FALLBACK_EMAIL_DOMAIN;
        $attempt = 0;

        while ($this->emailExists($candidate, $ignoreUserId)) {
            $attempt++;
            $candidate = $nim.'+'.$attempt.'@'.self::FALLBACK_EMAIL_DOMAIN;
        }

        return $candidate;
    }

    private function emailExists(string $email, ?int $ignoreUserId = null): bool
    {
        return User::query()
            ->where('email', $email)
            ->when($ignoreUserId, fn ($query) => $query->where('id', '!=', $ignoreUserId))
            ->exists();
    }
}
