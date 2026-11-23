<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\LmsMeeting;
use App\Models\LmsMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LmsMaterialController extends Controller
{
    public function index(): View
    {
        $dosenId = Dosen::query()->where('user_id', auth()->id())->value('id');

        $materials = LmsMaterial::query()
            ->where('dosen_id', $dosenId)
            ->with(['mataKuliah', 'meeting.semester'])
            ->orderBy('week_number')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dosen.lms.index', [
            'materials' => $materials,
            'typeOptions' => LmsMaterial::typeOptions(),
        ]);
    }

    public function create(): View
    {
        $dosenId = Dosen::query()->where('user_id', auth()->id())->value('id');

        return view('dosen.lms.create', [
            'meetingItems' => $this->meetingOptionsForDosen((int) $dosenId),
            'typeOptions' => LmsMaterial::typeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dosenId = Dosen::query()->where('user_id', auth()->id())->value('id');

        $validated = $request->validate([
            'lms_meeting_id' => ['required', 'exists:lms_meetings,id'],
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', 'in:' . implode(',', array_keys(LmsMaterial::typeOptions()))],
            'access_code' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'resource_url' => ['nullable', 'url'],
            'material_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $meeting = LmsMeeting::query()
            ->where('id', $validated['lms_meeting_id'])
            ->whereHas('mataKuliah', fn ($q) => $q->where('dosen_id', $dosenId))
            ->first();

        abort_unless($meeting, 403, 'Pertemuan tidak sesuai dengan akun dosen.');
        $this->validateResourceUrl($validated['resource_url'] ?? null);

        if ($request->hasFile('material_file')) {
            $file = $request->file('material_file');
            $validated['file_path'] = $file->store('lms-materials', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_mime'] = $file->getClientMimeType();
        }

        $validated['mata_kuliah_id'] = $meeting->mata_kuliah_id;
        $validated['week_number'] = $meeting->meeting_number;
        $validated['dosen_id'] = $dosenId;
        LmsMaterial::query()->create($validated);

        return redirect()->route('dosen.lms.index')->with('success', 'Materi LMS berhasil ditambahkan.');
    }

    public function edit(LmsMaterial $lms): View
    {
        $dosenId = Dosen::query()->where('user_id', auth()->id())->value('id');
        abort_unless((int) $lms->dosen_id === (int) $dosenId, 403);

        return view('dosen.lms.edit', [
            'material' => $lms,
            'meetingItems' => $this->meetingOptionsForDosen((int) $dosenId),
            'typeOptions' => LmsMaterial::typeOptions(),
        ]);
    }

    public function update(Request $request, LmsMaterial $lms): RedirectResponse
    {
        $dosenId = Dosen::query()->where('user_id', auth()->id())->value('id');
        abort_unless((int) $lms->dosen_id === (int) $dosenId, 403);

        $validated = $request->validate([
            'lms_meeting_id' => ['required', 'exists:lms_meetings,id'],
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', 'in:' . implode(',', array_keys(LmsMaterial::typeOptions()))],
            'access_code' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'resource_url' => ['nullable', 'url'],
            'material_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $meeting = LmsMeeting::query()
            ->where('id', $validated['lms_meeting_id'])
            ->whereHas('mataKuliah', fn ($q) => $q->where('dosen_id', $dosenId))
            ->first();

        abort_unless($meeting, 403, 'Pertemuan tidak sesuai dengan akun dosen.');
        $this->validateResourceUrl($validated['resource_url'] ?? null);

        if ($request->hasFile('material_file')) {
            if ($lms->file_path) {
                Storage::disk('public')->delete($lms->file_path);
            }

            $file = $request->file('material_file');
            $validated['file_path'] = $file->store('lms-materials', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_mime'] = $file->getClientMimeType();
        }

        $validated['mata_kuliah_id'] = $meeting->mata_kuliah_id;
        $validated['week_number'] = $meeting->meeting_number;
        $lms->update($validated);

        return redirect()->route('dosen.lms.index')->with('success', 'Materi LMS berhasil diperbarui.');
    }

    public function destroy(LmsMaterial $lms): RedirectResponse
    {
        $dosenId = Dosen::query()->where('user_id', auth()->id())->value('id');
        abort_unless((int) $lms->dosen_id === (int) $dosenId, 403);

        if ($lms->file_path) {
            Storage::disk('public')->delete($lms->file_path);
        }

        $lms->delete();

        return redirect()->route('dosen.lms.index')->with('success', 'Materi LMS berhasil dihapus.');
    }

    private function meetingOptionsForDosen(int $dosenId)
    {
        return LmsMeeting::query()
            ->whereHas('mataKuliah', fn ($q) => $q->where('dosen_id', $dosenId))
            ->with(['mataKuliah', 'semester'])
            ->orderBy('semester_id')
            ->orderBy('mata_kuliah_id')
            ->orderBy('meeting_number')
            ->get();
    }

    private function validateResourceUrl(?string $url): void
    {
        if (! $url) {
            return;
        }

        $allowed = ['youtube.com', 'youtu.be', 'drive.google.com'];
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        $matched = collect($allowed)->contains(fn ($domain) => str_contains((string) $host, $domain));

        if (! $matched) {
            throw ValidationException::withMessages([
                'resource_url' => 'Link hanya boleh dari YouTube atau Google Drive.',
            ]);
        }
    }
}
