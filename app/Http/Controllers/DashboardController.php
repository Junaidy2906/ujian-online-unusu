<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $role = $user?->role ?? User::ROLE_MAHASISWA;

        $mahasiswaId = DB::table('mahasiswa')->where('user_id', $user?->id)->value('id');
        $dosenId = DB::table('dosen')->where('user_id', $user?->id)->value('id');

        $context = match ($role) {
            User::ROLE_ADMIN => $this->adminContext(),
            User::ROLE_DOSEN => $this->dosenContext($dosenId),
            default => $this->mahasiswaContext($mahasiswaId),
        };

        return view('dashboard', array_merge([
            'user' => $user,
            'role' => $role,
            'roleLabel' => $user?->roleLabel() ?? 'Mahasiswa',
        ], $context));
    }

    private function adminContext(): array
    {
        $totalMahasiswa = DB::table('mahasiswa')->count();
        $totalDosen = DB::table('dosen')->count();
        $totalUjian = DB::table('ujian')->count();
        $rekapNilai = DB::table('nilai_ujian')->where('status_penilaian', 'selesai')->count();
        $avgNilai = (float) DB::table('nilai_ujian')->whereNotNull('nilai_akhir')->avg('nilai_akhir');
        $passRate = (float) DB::table('nilai_ujian')
            ->selectRaw('AVG(CASE WHEN status_lulus = 1 THEN 100 ELSE 0 END) as pass_rate')
            ->value('pass_rate');

        return [
            'heroBadge' => 'Dashboard Admin',
            'heroTitle' => 'Kontrol penuh operasional UJIAN ONLINE UNUSU',
            'heroText' => 'Pantau data mahasiswa, dosen, ujian, rekap nilai, dan performa ujian dari satu dashboard terpadu.',
            'summaryCards' => [
                ['label' => 'Total mahasiswa', 'value' => $totalMahasiswa, 'note' => 'Mahasiswa terdaftar'],
                ['label' => 'Total dosen', 'value' => $totalDosen, 'note' => 'Dosen aktif mengajar'],
                ['label' => 'Total ujian', 'value' => $totalUjian, 'note' => 'Semua jadwal ujian'],
                ['label' => 'Rekap nilai', 'value' => $rekapNilai, 'note' => 'Nilai selesai diproses'],
            ],
            'statusItems' => [
                ['label' => 'Ujian aktif', 'value' => DB::table('ujian')->where('is_active', true)->count()],
                ['label' => 'Rata-rata nilai', 'value' => number_format($avgNilai, 1)],
                ['label' => 'Persentase lulus', 'value' => number_format($passRate ?: 0, 1) . '%'],
            ],
            'chart' => [
                'title' => 'Grafik hasil ujian',
                'bars' => $this->scoreBuckets(),
            ],
            'quickActions' => [
                ['label' => 'Kelola Mahasiswa', 'route' => 'admin.mahasiswa.index'],
                ['label' => 'Kelola Dosen', 'route' => 'admin.dosen.index'],
                ['label' => 'Kelola Kelas', 'route' => 'admin.kelas.index'],
            ],
            'listTitle' => 'Pintasan manajemen',
            'listItems' => [
                'Validasi data master sebelum periode ujian dimulai.',
                'Pantau ujian yang berstatus aktif dan draft.',
                'Gunakan rekap nilai untuk evaluasi akademik lintas kelas.',
            ],
        ];
    }

    private function dosenContext(?int $dosenId): array
    {
        $mataKuliah = $dosenId ? DB::table('mata_kuliah')->where('dosen_id', $dosenId)->count() : 0;
        $jumlahSoal = $dosenId
            ? DB::table('soal')->join('ujian', 'ujian.id', '=', 'soal.ujian_id')->where('ujian.dosen_id', $dosenId)->count()
            : 0;
        $jadwalUjian = $dosenId ? DB::table('ujian')->where('dosen_id', $dosenId)->count() : 0;
        $rekapKelas = $dosenId ? DB::table('nilai_ujian')->where('dosen_id', $dosenId)->count() : 0;
        $pendingEssay = $dosenId
            ? DB::table('nilai_ujian')->where('dosen_id', $dosenId)->where('status_penilaian', 'menunggu_koreksi')->count()
            : 0;
        $upcoming = $dosenId
            ? DB::table('ujian')
                ->where('dosen_id', $dosenId)
                ->where('jadwal_mulai', '>=', now())
                ->orderBy('jadwal_mulai')
                ->limit(5)
                ->get(['nama_ujian', 'jadwal_mulai'])
                ->map(fn ($row) => $row->nama_ujian . ' - ' . date('d M Y H:i', strtotime($row->jadwal_mulai)))
                ->all()
            : [];

        return [
            'heroBadge' => 'Dashboard Dosen',
            'heroTitle' => 'Ruang kendali pengajaran dan evaluasi kelas',
            'heroText' => 'Kelola mata kuliah, bank soal, jadwal ujian, dan rekap nilai kelas secara terstruktur.',
            'summaryCards' => [
                ['label' => 'Mata kuliah', 'value' => $mataKuliah, 'note' => 'Pengampu aktif'],
                ['label' => 'Jumlah soal', 'value' => $jumlahSoal, 'note' => 'Soal tersimpan'],
                ['label' => 'Jadwal ujian', 'value' => $jadwalUjian, 'note' => 'Draft + aktif + selesai'],
                ['label' => 'Rekap nilai kelas', 'value' => $rekapKelas, 'note' => 'Data penilaian terkumpul'],
            ],
            'statusItems' => [
                ['label' => 'Ujian aktif', 'value' => $dosenId ? DB::table('ujian')->where('dosen_id', $dosenId)->where('is_active', true)->count() : 0],
                ['label' => 'Menunggu koreksi', 'value' => $pendingEssay],
                ['label' => 'Nilai selesai', 'value' => $dosenId ? DB::table('nilai_ujian')->where('dosen_id', $dosenId)->where('status_penilaian', 'selesai')->count() : 0],
            ],
            'chart' => [
                'title' => 'Grafik hasil ujian kelas',
                'bars' => $this->scoreBuckets($dosenId),
            ],
            'quickActions' => [
                ['label' => 'Kelola Ujian', 'route' => 'dosen.ujian.index'],
                ['label' => 'Tambah Soal', 'route' => 'dosen.ujian.index'],
                ['label' => 'Lihat Hasil', 'route' => 'dosen.ujian.index'],
            ],
            'listTitle' => 'Jadwal ujian terdekat',
            'listItems' => $upcoming !== [] ? $upcoming : ['Belum ada jadwal ujian terdekat.'],
        ];
    }

    private function mahasiswaContext(?int $mahasiswaId): array
    {
        $jadwalUjian = DB::table('ujian')->where('is_active', true)->count();
        $nilaiUjian = $mahasiswaId ? DB::table('nilai_ujian')->where('mahasiswa_id', $mahasiswaId)->whereNotNull('nilai_akhir')->count() : 0;
        $statusRemedial = $mahasiswaId
            ? DB::table('nilai_ujian')->where('mahasiswa_id', $mahasiswaId)->where('status_penilaian', 'selesai')->where('status_lulus', false)->count()
            : 0;
        $riwayat = $mahasiswaId ? DB::table('percobaan_ujian')->where('mahasiswa_id', $mahasiswaId)->count() : 0;
        $avgNilai = $mahasiswaId
            ? (float) DB::table('nilai_ujian')->where('mahasiswa_id', $mahasiswaId)->whereNotNull('nilai_akhir')->avg('nilai_akhir')
            : 0;
        $historyItems = $mahasiswaId
            ? DB::table('percobaan_ujian')
                ->join('ujian', 'ujian.id', '=', 'percobaan_ujian.ujian_id')
                ->where('percobaan_ujian.mahasiswa_id', $mahasiswaId)
                ->orderByDesc('percobaan_ujian.created_at')
                ->limit(5)
                ->get(['ujian.nama_ujian', 'percobaan_ujian.status'])
                ->map(fn ($row) => $row->nama_ujian . ' - status: ' . $row->status)
                ->all()
            : [];

        return [
            'heroBadge' => 'Dashboard Mahasiswa',
            'heroTitle' => 'Pantau jadwal, nilai, dan progres ujian Anda',
            'heroText' => 'Semua informasi ujian tersedia di satu tempat, termasuk status remedial dan riwayat pengerjaan.',
            'summaryCards' => [
                ['label' => 'Jadwal ujian', 'value' => $jadwalUjian, 'note' => 'Ujian aktif tersedia'],
                ['label' => 'Nilai ujian', 'value' => $nilaiUjian, 'note' => 'Nilai sudah terbit'],
                ['label' => 'Status remedial', 'value' => $statusRemedial, 'note' => 'Perlu perbaikan'],
                ['label' => 'Riwayat pengerjaan', 'value' => $riwayat, 'note' => 'Total percobaan ujian'],
            ],
            'statusItems' => [
                ['label' => 'Rata-rata nilai', 'value' => number_format($avgNilai, 1)],
                ['label' => 'Nilai selesai', 'value' => $mahasiswaId ? DB::table('nilai_ujian')->where('mahasiswa_id', $mahasiswaId)->where('status_penilaian', 'selesai')->count() : 0],
                ['label' => 'Sedang berjalan', 'value' => $mahasiswaId ? DB::table('percobaan_ujian')->where('mahasiswa_id', $mahasiswaId)->where('status', 'berlangsung')->count() : 0],
            ],
            'chart' => [
                'title' => 'Grafik capaian nilai Anda',
                'bars' => $this->scoreBuckets(null, $mahasiswaId),
            ],
            'quickActions' => [
                ['label' => 'Lihat Ujian', 'route' => 'mahasiswa.ujian.index'],
                ['label' => 'Riwayat Hasil', 'route' => 'mahasiswa.ujian.index'],
                ['label' => 'Status Nilai', 'route' => 'mahasiswa.ujian.index'],
            ],
            'listTitle' => 'Riwayat pengerjaan terbaru',
            'listItems' => $historyItems !== [] ? $historyItems : ['Belum ada riwayat pengerjaan ujian.'],
        ];
    }

    private function scoreBuckets(?int $dosenId = null, ?int $mahasiswaId = null): array
    {
        $query = DB::table('nilai_ujian')->whereNotNull('nilai_akhir');

        if ($dosenId) {
            $query->where('dosen_id', $dosenId);
        }

        if ($mahasiswaId) {
            $query->where('mahasiswa_id', $mahasiswaId);
        }

        return [
            ['label' => '< 60', 'value' => (clone $query)->where('nilai_akhir', '<', 60)->count()],
            ['label' => '60-74', 'value' => (clone $query)->whereBetween('nilai_akhir', [60, 74.99])->count()],
            ['label' => '75-84', 'value' => (clone $query)->whereBetween('nilai_akhir', [75, 84.99])->count()],
            ['label' => '>= 85', 'value' => (clone $query)->where('nilai_akhir', '>=', 85)->count()],
        ];
    }
}
