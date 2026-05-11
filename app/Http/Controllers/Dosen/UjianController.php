<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\NilaiUjian;
use App\Models\Soal;
use App\Models\TahunAkademik;
use App\Models\Ujian;
use App\Models\UjianMahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'kelasItems' => Kelas::withCount('mahasiswa')->whereHas('mahasiswa')->latest()->get(),
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
            'kode_soal' => $this->generateUniqueKodeSoal($data['mata_kuliah_id']),
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
            'kelasItems' => Kelas::withCount('mahasiswa')->whereHas('mahasiswa')->latest()->get(),
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

    public function aksesMahasiswa(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);
        if (! $ujian->kode_soal) {
            $ujian->update(['kode_soal' => $this->generateUniqueKodeSoal($ujian->mata_kuliah_id)]);
            $ujian->refresh();
        }

        $mahasiswaItems = Mahasiswa::with('user', 'kelas')
            ->whereIn('id', function ($query) use ($ujian) {
                $query->select('mahasiswa_id')->from('kelas_mahasiswa')->where('kelas_id', $ujian->kelas_id);
            })
            ->orderBy('nim')
            ->get();

        $aksesByMahasiswa = UjianMahasiswa::where('ujian_id', $ujian->id)
            ->pluck('kode_soal', 'mahasiswa_id');

        return view('dosen.ujian.akses-mahasiswa', [
            'ujian' => $ujian->load('kelas', 'mataKuliah'),
            'mahasiswaItems' => $mahasiswaItems,
            'aksesByMahasiswa' => $aksesByMahasiswa,
        ]);
    }

    public function simpanAksesMahasiswa(Request $request, Ujian $ujian): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);
        if (! $ujian->kode_soal) {
            $ujian->update(['kode_soal' => $this->generateUniqueKodeSoal($ujian->mata_kuliah_id)]);
            $ujian->refresh();
        }

        $data = $request->validate([
            'kode' => ['nullable', 'array'],
            'kode.*' => ['nullable', 'string', 'max:30'],
        ]);

        $allowedMahasiswaIds = Mahasiswa::query()
            ->whereIn('id', function ($query) use ($ujian) {
                $query->select('mahasiswa_id')->from('kelas_mahasiswa')->where('kelas_id', $ujian->kelas_id);
            })
            ->pluck('id')
            ->all();

        $inputKode = $data['kode'] ?? [];
        $action = (string) $request->input('bulk_action', '');
        $bulkKode = strtoupper(trim((string) $request->input('bulk_kode', '')));

        if ($action === 'pair_all') {
            if ($bulkKode === '') {
                $bulkKode = (string) $ujian->kode_soal;
            }

            foreach ($allowedMahasiswaIds as $mahasiswaId) {
                UjianMahasiswa::updateOrCreate(
                    ['ujian_id' => $ujian->id, 'mahasiswa_id' => $mahasiswaId],
                    ['kode_soal' => $bulkKode]
                );
            }

            return redirect()->route('dosen.ujian.akses-mahasiswa', $ujian)->with('success', 'Semua mahasiswa berhasil dipasangkan kode.');
        }

        if ($action === 'clear_all') {
            UjianMahasiswa::where('ujian_id', $ujian->id)->delete();

            return redirect()->route('dosen.ujian.akses-mahasiswa', $ujian)->with('success', 'Semua akses mahasiswa berhasil dikosongkan.');
        }

        foreach ($allowedMahasiswaIds as $mahasiswaId) {
            $kode = strtoupper(trim((string) ($inputKode[$mahasiswaId] ?? '')));

            if ($kode === '') {
                UjianMahasiswa::where('ujian_id', $ujian->id)->where('mahasiswa_id', $mahasiswaId)->delete();
                continue;
            }

            UjianMahasiswa::updateOrCreate(
                ['ujian_id' => $ujian->id, 'mahasiswa_id' => $mahasiswaId],
                ['kode_soal' => $kode]
            );
        }

        return redirect()->route('dosen.ujian.akses-mahasiswa', $ujian)->with('success', 'Akses mahasiswa berhasil diperbarui.');
    }

    private function generateUniqueKodeSoal(int|string $mataKuliahId): string
    {
        $prefix = 'MK'.$mataKuliahId;

        do {
            $kode = $prefix.'-'.strtoupper(Str::random(6));
        } while (Ujian::where('kode_soal', $kode)->exists());

        return $kode;
    }
}
