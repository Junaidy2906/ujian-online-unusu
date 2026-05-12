<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\JawabanMahasiswa;
use App\Models\NilaiUjian;
use App\Models\PercobaanUjian;
use App\Models\Soal;
use App\Models\TahunAkademik;
use App\Models\Ujian;
use App\Models\UjianMahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'kelasItems' => Kelas::withCount('mahasiswa')->orderBy('nama_kelas')->get(),
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
            'kelasItems' => Kelas::withCount('mahasiswa')->orderBy('nama_kelas')->get(),
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
            'items' => NilaiUjian::with(['mahasiswa.user', 'percobaanUjian'])
                ->where('ujian_id', $ujian->id)
                ->orderBy('mahasiswa_id')
                ->orderByDesc('created_at')
                ->get(),
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

        $aksesItems = UjianMahasiswa::where('ujian_id', $ujian->id)
            ->get(['mahasiswa_id', 'kode_soal', 'tambahan_percobaan']);

        $aksesByMahasiswa = $aksesItems->pluck('kode_soal', 'mahasiswa_id');
        $tambahanByMahasiswa = $aksesItems->pluck('tambahan_percobaan', 'mahasiswa_id');

        return view('dosen.ujian.akses-mahasiswa', [
            'ujian' => $ujian->load('kelas', 'mataKuliah'),
            'mahasiswaItems' => $mahasiswaItems,
            'aksesByMahasiswa' => $aksesByMahasiswa,
            'tambahanByMahasiswa' => $tambahanByMahasiswa,
        ]);
    }

    public function koreksiEssay(Ujian $ujian, NilaiUjian $nilai): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);
        abort_unless($nilai->ujian_id === $ujian->id, 404);

        $percobaan = PercobaanUjian::with(['mahasiswa.user'])
            ->where('id', $nilai->percobaan_ujian_id)
            ->where('ujian_id', $ujian->id)
            ->firstOrFail();

        $jawabanEssay = JawabanMahasiswa::with('soal')
            ->where('percobaan_ujian_id', $percobaan->id)
            ->whereHas('soal', fn ($q) => $q->where('tipe', 'essay'))
            ->get();

        return view('dosen.ujian.koreksi-essay', [
            'ujian' => $ujian->load('kelas', 'mataKuliah'),
            'nilai' => $nilai,
            'percobaan' => $percobaan,
            'jawabanEssay' => $jawabanEssay,
        ]);
    }

    public function simpanKoreksiEssay(Request $request, Ujian $ujian, NilaiUjian $nilai): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);
        abort_unless($nilai->ujian_id === $ujian->id, 404);

        $percobaan = PercobaanUjian::where('id', $nilai->percobaan_ujian_id)
            ->where('ujian_id', $ujian->id)
            ->firstOrFail();

        $jawabanEssay = JawabanMahasiswa::with('soal')
            ->where('percobaan_ujian_id', $percobaan->id)
            ->whereHas('soal', fn ($q) => $q->where('tipe', 'essay'))
            ->get();

        $rules = [
            'nilai' => ['required', 'array'],
        ];

        foreach ($jawabanEssay as $jawaban) {
            $maxPoin = (float) ($jawaban->soal?->poin ?? 0);
            $rules['nilai.'.$jawaban->id] = ['required', 'numeric', 'min:0', 'max:'.$maxPoin];
        }

        $data = $request->validate($rules);
        $inputNilai = $data['nilai'] ?? [];

        DB::transaction(function () use ($jawabanEssay, $inputNilai, $ujian, $percobaan, $nilai): void {
            foreach ($jawabanEssay as $jawaban) {
                $jawaban->update([
                    'nilai' => (float) ($inputNilai[$jawaban->id] ?? 0),
                ]);
            }

            $essayRaw = (float) JawabanMahasiswa::query()
                ->where('percobaan_ujian_id', $percobaan->id)
                ->whereHas('soal', fn ($q) => $q->where('tipe', 'essay'))
                ->sum('nilai');

            $totalPoinSemuaSoal = (float) Soal::query()
                ->where('ujian_id', $ujian->id)
                ->sum('poin');

            $nilaiEssayFinal = $totalPoinSemuaSoal > 0 ? round(($essayRaw / $totalPoinSemuaSoal) * 100, 2) : 0.0;
            $nilaiPgFinal = (float) ($percobaan->nilai_pg ?? $nilai->nilai_pg ?? 0);
            $nilaiAkhir = round($nilaiPgFinal + $nilaiEssayFinal, 2);
            $isLulus = $nilaiAkhir >= (float) $ujian->nilai_minimum_lulus;

            $percobaan->update([
                'nilai_essay' => $nilaiEssayFinal,
                'nilai_akhir' => $nilaiAkhir,
                'is_lulus' => $isLulus,
            ]);

            $nilai->update([
                'nilai_essay' => $nilaiEssayFinal,
                'nilai_akhir' => $nilaiAkhir,
                'status_penilaian' => 'selesai',
                'status_lulus' => $isLulus,
                'catatan' => null,
                'dinilai_at' => now(),
            ]);
        });

        return redirect()->route('dosen.ujian.hasil', $ujian)->with('success', 'Koreksi essai berhasil disimpan.');
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
            'tambahan_percobaan' => ['nullable', 'array'],
            'tambahan_percobaan.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'group_kelas_id' => ['nullable', 'integer'],
        ]);

        $allowedMahasiswaIds = Mahasiswa::query()
            ->whereIn('id', function ($query) use ($ujian) {
                $query->select('mahasiswa_id')->from('kelas_mahasiswa')->where('kelas_id', $ujian->kelas_id);
            })
            ->pluck('id')
            ->all();

        $allowedByKelas = Mahasiswa::with('kelas')
            ->whereIn('id', $allowedMahasiswaIds)
            ->get()
            ->groupBy(fn (Mahasiswa $mhs) => (int) ($mhs->kelas->first()?->id ?? 0))
            ->map(fn ($items) => $items->pluck('id')->all());

        $inputKode = $data['kode'] ?? [];
        $inputTambahan = $data['tambahan_percobaan'] ?? [];
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

        if (in_array($action, ['pair_group', 'clear_group'], true)) {
            $groupKelasId = (int) ($data['group_kelas_id'] ?? 0);
            $targetMahasiswaIds = $allowedByKelas->get($groupKelasId, []);

            if (empty($targetMahasiswaIds)) {
                return redirect()->route('dosen.ujian.akses-mahasiswa', $ujian)->withErrors('Kelompok kelas tidak valid.');
            }

            if ($action === 'clear_group') {
                UjianMahasiswa::where('ujian_id', $ujian->id)
                    ->whereIn('mahasiswa_id', $targetMahasiswaIds)
                    ->delete();

                return redirect()->route('dosen.ujian.akses-mahasiswa', $ujian)->with('success', 'Akses mahasiswa per kelas berhasil dikosongkan.');
            }

            if ($bulkKode === '') {
                $bulkKode = (string) $ujian->kode_soal;
            }

            foreach ($targetMahasiswaIds as $mahasiswaId) {
                UjianMahasiswa::updateOrCreate(
                    ['ujian_id' => $ujian->id, 'mahasiswa_id' => $mahasiswaId],
                    ['kode_soal' => $bulkKode]
                );
            }

            return redirect()->route('dosen.ujian.akses-mahasiswa', $ujian)->with('success', 'Mahasiswa pada kelas terpilih berhasil dipasangkan kode.');
        }

        foreach ($allowedMahasiswaIds as $mahasiswaId) {
            $kode = strtoupper(trim((string) ($inputKode[$mahasiswaId] ?? '')));
            $tambahanPercobaan = max(0, (int) ($inputTambahan[$mahasiswaId] ?? 0));

            if ($kode === '') {
                UjianMahasiswa::where('ujian_id', $ujian->id)->where('mahasiswa_id', $mahasiswaId)->delete();
                continue;
            }

            UjianMahasiswa::updateOrCreate(
                ['ujian_id' => $ujian->id, 'mahasiswa_id' => $mahasiswaId],
                [
                    'kode_soal' => $kode,
                    'tambahan_percobaan' => $tambahanPercobaan,
                ]
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
