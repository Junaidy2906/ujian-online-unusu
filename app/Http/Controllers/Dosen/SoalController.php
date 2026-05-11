<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PilihanJawaban;
use App\Models\Soal;
use App\Models\Ujian;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SoalController extends Controller
{
    public function index(Request $request, Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $search = trim((string) $request->query('q', ''));
        $tipe = (string) $request->query('tipe', 'semua');

        $query = Soal::with('pilihanJawaban')->where('ujian_id', $ujian->id);

        if (in_array($tipe, ['pg', 'essay'], true)) {
            $query->where('tipe', $tipe);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('pertanyaan', 'like', '%'.$search.'%')
                    ->orWhere('jawaban_benar', 'like', '%'.$search.'%');
            });
        }

        return view('dosen.soal.index', [
            'ujian' => $ujian,
            'items' => $query->orderBy('nomor')->get(),
            'filters' => [
                'q' => $search,
                'tipe' => in_array($tipe, ['semua', 'pg', 'essay'], true) ? $tipe : 'semua',
            ],
        ]);
    }

    public function reorder(Request $request, Ujian $ujian): RedirectResponse|JsonResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $data = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'integer', 'distinct'],
        ]);

        $orderedIds = collect($data['order'])->map(fn ($id) => (int) $id)->values();
        $existingIds = Soal::query()
            ->where('ujian_id', $ujian->id)
            ->whereIn('id', $orderedIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        abort_unless($orderedIds->count() === $existingIds->count(), 422);

        DB::transaction(function () use ($orderedIds, $ujian): void {
            foreach ($orderedIds as $index => $soalId) {
                Soal::query()
                    ->where('id', $soalId)
                    ->where('ujian_id', $ujian->id)
                    ->update(['nomor' => $index + 1]);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Urutan soal berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('dosen.ujian.soal.index', $ujian)
            ->with('success', 'Urutan soal berhasil diperbarui.');
    }

    public function create(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        return view('dosen.soal.create', ['ujian' => $ujian]);
    }

    public function importForm(Ujian $ujian): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        return view('dosen.soal.import', ['ujian' => $ujian]);
    }

    public function importStore(Request $request, Ujian $ujian): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $data = $request->validate([
            'poin_default' => ['required', 'numeric', 'min:0.01'],
            'soal_text' => ['nullable', 'string'],
            'soal_file' => ['nullable', 'file', 'mimes:txt', 'max:2048'],
        ]);

        $payload = trim((string) ($data['soal_text'] ?? ''));
        if ($request->hasFile('soal_file')) {
            $payload = (string) file_get_contents($request->file('soal_file')->getRealPath());
        }

        if ($payload === '') {
            throw ValidationException::withMessages([
                'soal_text' => 'Isi soal wajib diisi atau upload file .txt.',
            ]);
        }

        $parsed = $this->parseBulkPgText($payload);
        if ($parsed === []) {
            throw ValidationException::withMessages([
                'soal_text' => 'Format soal tidak dikenali. Cek contoh format import.',
            ]);
        }

        DB::transaction(function () use ($ujian, $parsed, $data): void {
            $nextNomor = ((int) Soal::where('ujian_id', $ujian->id)->max('nomor')) + 1;

            foreach ($parsed as $item) {
                $soal = Soal::create([
                    'ujian_id' => $ujian->id,
                    'nomor' => $nextNomor++,
                    'tipe' => 'pg',
                    'pertanyaan' => $this->sanitizeHtml($item['pertanyaan']),
                    'poin' => $data['poin_default'],
                    'jawaban_benar' => $item['kunci'],
                    'rubrik_penilaian' => null,
                ]);

                foreach ($item['opsi'] as $kode => $jawaban) {
                    PilihanJawaban::create([
                        'soal_id' => $soal->id,
                        'kode' => $kode,
                        'jawaban' => $this->sanitizeHtml($jawaban),
                        'is_benar' => $kode === $item['kunci'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('dosen.ujian.soal.index', $ujian)
            ->with('success', count($parsed).' soal berhasil diimport.');
    }

    public function store(Request $request, Ujian $ujian): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId, 403);

        $data = $this->validateSoal($request);
        $validatedOptions = $data['pilihan'] ?? [];
        $sanitizedOptions = $this->sanitizeOptions($validatedOptions);

        if ($data['tipe'] === 'pg') {
            $filledOptions = collect($sanitizedOptions)->filter(fn ($opt) => ! empty(trim((string) ($opt['jawaban'] ?? ''))));
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
            'pertanyaan' => $this->sanitizeHtml($data['pertanyaan']),
            'poin' => $data['poin'],
            'jawaban_benar' => $data['jawaban_benar'] ?? null,
            'rubrik_penilaian' => isset($data['rubrik_penilaian']) ? $this->sanitizeHtml($data['rubrik_penilaian']) : null,
        ]);

        foreach ($sanitizedOptions as $option) {
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

    public function edit(Ujian $ujian, Soal $soal): View
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId && $soal->ujian_id === $ujian->id, 403);

        return view('dosen.soal.edit', [
            'ujian' => $ujian,
            'soal' => $soal->load('pilihanJawaban'),
        ]);
    }

    public function update(Request $request, Ujian $ujian, Soal $soal): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId && $soal->ujian_id === $ujian->id, 403);

        $data = $this->validateSoal($request);
        $validatedOptions = $data['pilihan'] ?? [];
        $sanitizedOptions = $this->sanitizeOptions($validatedOptions);

        if ($data['tipe'] === 'pg') {
            $filledOptions = collect($sanitizedOptions)->filter(fn ($opt) => ! empty(trim((string) ($opt['jawaban'] ?? ''))));
            $trueOptions = $filledOptions->filter(fn ($opt) => ! empty($opt['is_benar']));

            if ($filledOptions->count() < 2) {
                return back()->withErrors(['pilihan' => 'Soal PG wajib memiliki minimal 2 opsi jawaban.'])->withInput();
            }

            if ($trueOptions->count() !== 1) {
                return back()->withErrors(['pilihan' => 'Soal PG wajib memiliki tepat 1 kunci jawaban benar.'])->withInput();
            }
        }

        $soal->update([
            'nomor' => $data['nomor'],
            'tipe' => $data['tipe'],
            'pertanyaan' => $this->sanitizeHtml($data['pertanyaan']),
            'poin' => $data['poin'],
            'jawaban_benar' => $data['jawaban_benar'] ?? null,
            'rubrik_penilaian' => isset($data['rubrik_penilaian']) ? $this->sanitizeHtml($data['rubrik_penilaian']) : null,
        ]);

        $soal->pilihanJawaban()->delete();

        foreach ($sanitizedOptions as $option) {
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

        return redirect()->route('dosen.ujian.soal.index', $ujian)->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Ujian $ujian, Soal $soal): RedirectResponse
    {
        $dosenId = Dosen::where('user_id', auth()->id())->value('id');
        abort_unless($ujian->dosen_id === $dosenId && $soal->ujian_id === $ujian->id, 403);

        $soal->delete();

        return redirect()->route('dosen.ujian.soal.index', $ujian)->with('success', 'Soal berhasil dihapus.');
    }

    private function validateSoal(Request $request): array
    {
        return $request->validate([
            'nomor' => ['required', 'integer', 'min:1'],
            'tipe' => ['required', 'in:pg,essay'],
            'pertanyaan' => ['required', 'string'],
            'poin' => ['required', 'numeric', 'min:0'],
            'jawaban_benar' => ['nullable', 'string', 'max:255'],
            'rubrik_penilaian' => ['nullable', 'string'],
            'pilihan' => ['nullable', 'array'],
            'pilihan.*.kode' => [Rule::requiredIf(fn () => $request->input('tipe') === 'pg'), 'string', 'max:5'],
            'pilihan.*.jawaban' => ['nullable', 'string'],
            'pilihan.*.is_benar' => ['nullable', 'boolean'],
        ]);
    }

    private function sanitizeOptions(array $options): array
    {
        return collect($options)->map(function (array $option): array {
            $option['jawaban'] = isset($option['jawaban']) ? $this->sanitizeHtml((string) $option['jawaban']) : '';

            return $option;
        })->all();
    }

    private function sanitizeHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return $html;
        }

        $allowedTags = [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u',
            'ul', 'ol', 'li',
            'table', 'thead', 'tbody', 'tr', 'th', 'td',
            'img', 'span', 'div',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        ];

        $allowedAttributes = [
            'img' => ['src', 'alt', 'width', 'height'],
            'table' => ['border', 'cellpadding', 'cellspacing'],
            'th' => ['colspan', 'rowspan'],
            'td' => ['colspan', 'rowspan'],
            '*' => ['class'],
        ];

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><body>'.$html.'</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (! $body) {
            return strip_tags($html);
        }

        $this->sanitizeNode($body, $allowedTags, $allowedAttributes);

        $result = '';
        foreach ($body->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return trim((string) $result);
    }

    private function sanitizeNode(DOMNode $node, array $allowedTags, array $allowedAttributes): void
    {
        if ($node instanceof DOMElement) {
            $tag = strtolower($node->tagName);
            if (! in_array($tag, $allowedTags, true) && $tag !== 'body') {
                $this->unwrapNode($node);

                return;
            }

            $safeAttributes = $allowedAttributes[$tag] ?? [];
            $globalAttributes = $allowedAttributes['*'] ?? [];
            $permitted = array_unique(array_merge($safeAttributes, $globalAttributes));

            for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
                $attr = $node->attributes->item($i);
                if (! $attr) {
                    continue;
                }

                $name = strtolower($attr->name);
                $value = trim($attr->value);
                $isEventAttr = str_starts_with($name, 'on');
                $isAllowed = in_array($name, $permitted, true);
                $isUnsafeUri = in_array($name, ['src', 'href'], true) && preg_match('/^\s*javascript:/i', $value);

                if ($isEventAttr || ! $isAllowed || $isUnsafeUri) {
                    $node->removeAttribute($attr->name);
                }
            }
        }

        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            $this->sanitizeNode($child, $allowedTags, $allowedAttributes);
        }
    }

    private function unwrapNode(DOMNode $node): void
    {
        $parent = $node->parentNode;
        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private function parseBulkPgText(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $lines = array_map(static fn ($line) => trim($line), $lines);

        $items = [];
        $current = null;

        foreach ($lines as $line) {
            if ($line === '' || preg_match('/^Soal Pilihan/i', $line)) {
                continue;
            }

            if (preg_match('/^Jawaban\s*:\s*([A-D])$/i', $line, $m)) {
                if ($current !== null) {
                    $current['kunci'] = strtoupper($m[1]);
                    if ($this->isValidParsedItem($current)) {
                        $items[] = $current;
                    }
                }
                $current = null;
                continue;
            }

            if (preg_match('/^([A-D])\.\s*(.+)$/i', $line, $m)) {
                if ($current !== null) {
                    $current['opsi'][strtoupper($m[1])] = $m[2];
                }
                continue;
            }

            if ($current !== null && count($current['opsi']) > 0 && ! isset($current['kunci'])) {
                $current['pertanyaan'] .= '<br>'.$line;
                continue;
            }

            $current = [
                'pertanyaan' => $line,
                'opsi' => [],
                'kunci' => null,
            ];
        }

        return $items;
    }

    private function isValidParsedItem(array $item): bool
    {
        $required = ['A', 'B', 'C', 'D'];
        foreach ($required as $kode) {
            if (! isset($item['opsi'][$kode]) || trim((string) $item['opsi'][$kode]) === '') {
                return false;
            }
        }

        return isset($item['kunci']) && in_array($item['kunci'], $required, true);
    }
}
