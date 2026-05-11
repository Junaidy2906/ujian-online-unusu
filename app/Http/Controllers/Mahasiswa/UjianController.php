<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\JawabanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\NilaiUjian;
use App\Models\PercobaanUjian;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianMahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UjianController extends Controller
{
    public function index(): View
    {
        $mahasiswaId = Mahasiswa::where('user_id', auth()->id())->value('id');

        $kelasIds = DB::table('kelas_mahasiswa')->where('mahasiswa_id', $mahasiswaId)->pluck('kelas_id');

        return view('mahasiswa.ujian.index', [
            'items' => Ujian::with(['kelas', 'mataKuliah'])
                ->where('is_active', true)
                ->whereIn('kelas_id', $kelasIds)
                ->whereExists(function ($query) use ($mahasiswaId) {
                    $query->select(DB::raw(1))
                        ->from('ujian_mahasiswa')
                        ->whereColumn('ujian_mahasiswa.ujian_id', 'ujian.id')
                        ->where('ujian_mahasiswa.mahasiswa_id', $mahasiswaId)
                        ->whereColumn('ujian_mahasiswa.kode_soal', 'ujian.kode_soal');
                })
                ->orderByDesc('jadwal_mulai')
                ->get(),
        ]);
    }

    public function show(Ujian $ujian): View
    {
        $mahasiswaId = Mahasiswa::where('user_id', auth()->id())->value('id');
        $kelasIds = DB::table('kelas_mahasiswa')->where('mahasiswa_id', $mahasiswaId)->pluck('kelas_id');
        abort_unless($kelasIds->contains($ujian->kelas_id), 403);
        $this->abortIfNoExamAccess($ujian->id, $mahasiswaId);

        $attemptCount = PercobaanUjian::where('ujian_id', $ujian->id)->where('mahasiswa_id', $mahasiswaId)->count();

        return view('mahasiswa.ujian.show', [
            'ujian' => $ujian->load('kelas', 'mataKuliah'),
            'attemptCount' => $attemptCount,
            'lastScore' => NilaiUjian::where('ujian_id', $ujian->id)->where('mahasiswa_id', $mahasiswaId)->latest()->first(),
        ]);
    }

    public function start(Ujian $ujian): RedirectResponse
    {
        $mahasiswaId = Mahasiswa::where('user_id', auth()->id())->value('id');
        $kelasIds = DB::table('kelas_mahasiswa')->where('mahasiswa_id', $mahasiswaId)->pluck('kelas_id');
        abort_unless($kelasIds->contains($ujian->kelas_id), 403);
        $this->abortIfNoExamAccess($ujian->id, $mahasiswaId);

        $attemptCount = PercobaanUjian::where('ujian_id', $ujian->id)->where('mahasiswa_id', $mahasiswaId)->count();
        abort_if($attemptCount >= $ujian->maksimal_percobaan, 403, 'Batas percobaan ujian telah tercapai.');

        $nextAttempt = $attemptCount + 1;

        $attempt = PercobaanUjian::firstOrCreate(
            [
                'ujian_id' => $ujian->id,
                'mahasiswa_id' => $mahasiswaId,
                'percobaan_ke' => $nextAttempt,
            ],
            [
                'mulai_at' => now(),
                'status' => 'berlangsung',
            ]
        );

        if (! $attempt->wasRecentlyCreated && $attempt->status === 'selesai') {
            return redirect()->route('mahasiswa.ujian.show', $ujian)->with('success', 'Percobaan untuk nomor ini sudah selesai.');
        }

        return redirect()->route('mahasiswa.ujian.kerjakan', $attempt);
    }

    public function kerjakan(PercobaanUjian $percobaan): View
    {
        $mahasiswaId = Mahasiswa::where('user_id', auth()->id())->value('id');
        abort_unless($percobaan->mahasiswa_id === $mahasiswaId, 403);
        abort_if($percobaan->status === 'selesai', 403, 'Percobaan ujian ini sudah selesai.');

        $ujian = $percobaan->ujian()->with('soal.pilihanJawaban')->first();
        $totalSeconds = ((int) $ujian->durasi_menit) * 60;
        $elapsedSeconds = $percobaan->mulai_at ? $percobaan->mulai_at->diffInSeconds(now()) : 0;
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        return view('mahasiswa.ujian.kerjakan', [
            'percobaan' => $percobaan->load('ujian.kelas', 'ujian.mataKuliah'),
            'ujian' => $ujian,
            'soalItems' => $ujian->soal()->orderBy('nomor')->get(),
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function submit(Request $request, PercobaanUjian $percobaan): RedirectResponse
    {
        $mahasiswaId = Mahasiswa::where('user_id', auth()->id())->value('id');
        abort_unless($percobaan->mahasiswa_id === $mahasiswaId, 403);

        $ujian = $percobaan->ujian()->with('soal.pilihanJawaban')->first();
        $soalItems = $ujian->soal()->with('pilihanJawaban')->orderBy('nomor')->get();

        $request->validate([
            'jawaban' => ['nullable', 'array'],
        ]);

        $totalPgPoin = (float) $soalItems->where('tipe', 'pg')->sum('poin');
        $totalEssayPoin = (float) $soalItems->where('tipe', 'essay')->sum('poin');
        $nilaiPgRaw = 0.0;
        $nilaiEssayRaw = 0.0;
        $essayExists = false;
        $isTimedOut = $percobaan->mulai_at
            ? now()->greaterThanOrEqualTo($percobaan->mulai_at->copy()->addMinutes((int) $ujian->durasi_menit))
            : false;

        DB::transaction(function () use ($request, $percobaan, $soalItems, &$nilaiPgRaw, &$nilaiEssayRaw, &$essayExists, $ujian, $mahasiswaId): void {
            foreach ($soalItems as $soal) {
                $answer = $request->input('jawaban.'.$soal->id);

                if ($soal->tipe === 'pg') {
                    $chosen = $soal->pilihanJawaban->firstWhere('id', (int) $answer);
                    $isCorrect = (bool) ($chosen?->is_benar ?? false);
                    $score = $isCorrect ? (float) $soal->poin : 0;
                    $nilaiPgRaw += $score;

                    JawabanMahasiswa::updateOrCreate(
                        ['percobaan_ujian_id' => $percobaan->id, 'soal_id' => $soal->id],
                        [
                            'pilihan_jawaban_id' => $chosen?->id,
                            'jawaban_text' => null,
                            'is_benar' => $isCorrect,
                            'nilai' => $score,
                        ]
                    );
                } else {
                    $essayExists = true;
                    JawabanMahasiswa::updateOrCreate(
                        ['percobaan_ujian_id' => $percobaan->id, 'soal_id' => $soal->id],
                        [
                            'pilihan_jawaban_id' => null,
                            'jawaban_text' => $answer,
                            'is_benar' => null,
                            'nilai' => null,
                        ]
                    );
                }
            }
        });

        $pgScore100 = $totalPgPoin > 0 ? ($nilaiPgRaw / $totalPgPoin) * 100 : 0;
        $essayScore100 = $totalEssayPoin > 0 ? ($nilaiEssayRaw / $totalEssayPoin) * 100 : 0;
        $nilaiPgFinal = round($pgScore100 * ((float) $ujian->bobot_pg / 100), 2);
        $nilaiEssayFinal = $essayExists ? null : round($essayScore100 * ((float) $ujian->bobot_essay / 100), 2);
        $nilaiAkhir = $essayExists ? $nilaiPgFinal : round($nilaiPgFinal + $nilaiEssayFinal, 2);
        $isLulus = $nilaiAkhir >= (float) $ujian->nilai_minimum_lulus && ! $essayExists;

        $percobaan->update([
            'selesai_at' => now(),
            'status' => 'selesai',
            'nilai_pg' => $nilaiPgFinal,
            'nilai_essay' => $nilaiEssayFinal,
            'nilai_akhir' => $nilaiAkhir,
            'is_lulus' => $isLulus,
        ]);

        NilaiUjian::updateOrCreate(
            ['percobaan_ujian_id' => $percobaan->id],
            [
                'ujian_id' => $ujian->id,
                'mahasiswa_id' => $mahasiswaId,
                'dosen_id' => $ujian->dosen_id,
                'nilai_pg' => $nilaiPgFinal,
                'nilai_essay' => $nilaiEssayFinal,
                'nilai_akhir' => $nilaiAkhir,
                'status_penilaian' => $essayExists ? 'menunggu_koreksi' : 'selesai',
                'status_lulus' => $isLulus,
                'catatan' => $essayExists ? 'Menunggu koreksi essai oleh dosen.' : ($isTimedOut ? 'Submit otomatis karena waktu ujian habis.' : null),
                'dinilai_at' => now(),
            ]
        );

        return redirect()->route('mahasiswa.ujian.hasil', $percobaan)->with('success', 'Jawaban berhasil disubmit.');
    }

    public function hasil(PercobaanUjian $percobaan): View
    {
        $mahasiswaId = Mahasiswa::where('user_id', auth()->id())->value('id');
        abort_unless($percobaan->mahasiswa_id === $mahasiswaId, 403);

        return view('mahasiswa.ujian.hasil', [
            'percobaan' => $percobaan->load('ujian.kelas', 'ujian.mataKuliah'),
            'nilai' => NilaiUjian::where('percobaan_ujian_id', $percobaan->id)->first(),
        ]);
    }

        public function cetak(PercobaanUjian $percobaan): View
        {
            $mahasiswaId = Mahasiswa::where('user_id', auth()->id())->value('id');
            abort_unless($percobaan->mahasiswa_id === $mahasiswaId, 403);

            $ujian = $percobaan->ujian()->with(['kelas', 'mataKuliah', 'dosen.user'])->first();
            $mahasiswa = Mahasiswa::with('user')->findOrFail($mahasiswaId);

            return view('reports.ujian.student', [
                'title' => 'Cetak Hasil Ujian',
                'ujian' => $ujian,
                'student' => $mahasiswa,
                'scores' => NilaiUjian::where('ujian_id', $ujian->id)->where('mahasiswa_id', $mahasiswa->id)->latest()->get(),
                'printBackRoute' => route('mahasiswa.ujian.hasil', $percobaan),
            ]);
        }

    private function abortIfNoExamAccess(int $ujianId, int $mahasiswaId): void
    {
        $isAllowed = UjianMahasiswa::query()
            ->join('ujian', 'ujian.id', '=', 'ujian_mahasiswa.ujian_id')
            ->where('ujian_mahasiswa.ujian_id', $ujianId)
            ->where('ujian_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->whereColumn('ujian_mahasiswa.kode_soal', 'ujian.kode_soal')
            ->exists();

        abort_unless($isAllowed, 403, 'Anda belum mendapatkan kode akses ujian dari dosen.');
    }
}
