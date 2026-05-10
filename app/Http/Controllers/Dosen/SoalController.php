<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PilihanJawaban;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SoalController extends Controller
{
    public function index(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        return view('dosen.soal.index', [
            'ujian' => $ujian,
            'items' => Soal::with('pilihanJawaban')->where('ujian_id', $ujian->id)->orderBy('nomor')->get(),
        ]);
    }

    public function create(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        return view('dosen.soal.create', ['ujian' => $ujian]);
    }

    public function store(Request $request, Ujian $ujian): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $data = $request->validate([
            'nomor' => ['required', 'integer', 'min:1'],
            'tipe' => ['required', 'in:pg,essay'],
            'pertanyaan' => ['required', 'string'],
            'poin' => ['required', 'numeric', 'min:0'],
            'jawaban_benar' => ['nullable', 'string', 'max:255'],
            'rubrik_penilaian' => ['nullable', 'string'],
            'pilihan' => ['nullable', 'array'],
            'pilihan.*.kode' => ['required_with:pilihan', 'string', 'max:5'],
            'pilihan.*.jawaban' => ['required_with:pilihan', 'string'],
            'pilihan.*.is_benar' => ['nullable', 'boolean'],
        ]);

        if ($data['tipe'] === 'pg') {
            $filledOptions = collect($data['pilihan'] ?? [])->filter(fn ($opt) => ! empty(trim((string) ($opt['jawaban'] ?? ''))));
            $trueOptions = $filledOptions->filter(fn ($opt) => ! empty($opt['is_benar']));

            if ($filledOptions->count() < 2) {
                return back()->withErrors(['pilihan' => 'Soal PG wajib memiliki minimal 2 opsi jawaban.'])->withInput();
            }

            if ($trueOptions->count() !== 1) {
                return back()->withErrors(['pilihan' => 'Soal PG wajib memiliki tepat 1 kunci jawaban benar.'])->withInput();
            }
        }

        $soal = Soal::create([
            'ujian_id' => $ujian->id,
            'nomor' => $data['nomor'],
            'tipe' => $data['tipe'],
            'pertanyaan' => $data['pertanyaan'],
            'poin' => $data['poin'],
            'jawaban_benar' => $data['jawaban_benar'] ?? null,
            'rubrik_penilaian' => $data['rubrik_penilaian'] ?? null,
        ]);

        foreach ($data['pilihan'] ?? [] as $option) {
            if ($data['tipe'] !== 'pg' || empty(trim((string) ($option['jawaban'] ?? '')))) {
                continue;
            }

            PilihanJawaban::create([
                'soal_id' => $soal->id,
                'kode' => $option['kode'],
                'jawaban' => $option['jawaban'],
                'is_benar' => ! empty($option['is_benar']),
            ]);
        }

        return redirect()->route('dosen.ujian.soal.index', $ujian)->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroy(Ujian $ujian, Soal $soal): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId && $soal->ujian_id === $ujian->id, 403);

        $soal->delete();

        return redirect()->route('dosen.ujian.soal.index', $ujian)->with('success', 'Soal berhasil dihapus.');
    }
}
