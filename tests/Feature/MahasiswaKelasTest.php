<?php

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Http\UploadedFile;

function makeKelas(string $kode): Kelas
{
    $tahun = TahunAkademik::query()->first() ?? TahunAkademik::create(['nama' => '2026/2027', 'kode' => '20261']);
    $semester = Semester::query()->first() ?? Semester::create(['tahun_akademik_id' => $tahun->id, 'nama' => 'Ganjil']);

    return Kelas::create([
        'tahun_akademik_id' => $tahun->id,
        'semester_id' => $semester->id,
        'kode_kelas' => $kode,
        'nama_kelas' => 'Kelas '.$kode,
        'is_active' => true,
    ]);
}

function makeMahasiswa(string $nim): Mahasiswa
{
    $user = User::factory()->create(['role' => User::ROLE_MAHASISWA]);

    return Mahasiswa::create(['user_id' => $user->id, 'nim' => $nim, 'status' => 'aktif']);
}

test('admin can set kelas when creating and editing a mahasiswa', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $a = makeKelas('SI-A');
    $b = makeKelas('SI-B');

    $this->actingAs($admin)->get(route('admin.mahasiswa.create'))->assertOk()->assertSee('Kelas SI-A');

    $this->post(route('admin.mahasiswa.store'), [
        'name' => 'Mhs Baru', 'email' => 'baru@example.com', 'nim' => '111', 'status' => 'aktif',
        'kelas_ids' => [$a->id, $b->id],
    ])->assertRedirect(route('admin.mahasiswa.index'));

    $mhs = Mahasiswa::where('nim', '111')->firstOrFail();
    expect($mhs->kelas->pluck('id')->sort()->values()->all())->toBe([$a->id, $b->id]);

    $this->get(route('admin.mahasiswa.edit', $mhs))->assertOk();
    $this->put(route('admin.mahasiswa.update', $mhs), [
        'name' => 'Mhs Baru', 'email' => 'baru@example.com', 'nim' => '111', 'status' => 'aktif',
        'kelas_ids' => [$b->id],
    ])->assertRedirect();

    expect($mhs->kelas()->pluck('kelas.id')->all())->toBe([$b->id]);

    $this->get(route('admin.mahasiswa.index', ['kelas' => $b->id]))->assertOk()->assertSee('Mhs Baru');
    $this->get(route('admin.mahasiswa.index', ['kelas' => 'tanpa']))->assertOk()->assertDontSee('baru@example.com');
});

test('admin can bulk add, move and remove mahasiswa from a kelas', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $a = makeKelas('SI-A');
    $b = makeKelas('SI-B');
    $m1 = makeMahasiswa('201');
    $m2 = makeMahasiswa('202');
    $m1->kelas()->attach($a->id);

    $this->actingAs($admin)->post(route('admin.mahasiswa.kelas.bulk'), [
        'mahasiswa_ids' => [$m1->id, $m2->id], 'kelas_id' => $b->id, 'aksi' => 'tambah',
    ])->assertSessionHas('success');
    expect($m1->kelas()->count())->toBe(2)->and($m2->kelas()->count())->toBe(1);

    $this->post(route('admin.mahasiswa.kelas.bulk'), [
        'mahasiswa_ids' => [$m1->id], 'kelas_id' => $a->id, 'aksi' => 'pindah',
    ]);
    expect($m1->kelas()->pluck('kelas.id')->all())->toBe([$a->id]);

    $this->post(route('admin.mahasiswa.kelas.bulk'), [
        'mahasiswa_ids' => [$m2->id], 'kelas_id' => $b->id, 'aksi' => 'keluarkan',
    ]);
    expect($m2->kelas()->count())->toBe(0);
});

test('import template assigns kelas by kode_kelas', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $a = makeKelas('SI-A');

    $file = UploadedFile::fake()->createWithContent('mhs.csv', "name,email,nim,kode_kelas\nImpor Satu,imp1@example.com,301,si-a\n");
    $this->actingAs($admin)->post(route('admin.mahasiswa.template.upload'), ['template_file' => $file])->assertRedirect();

    expect(Mahasiswa::where('nim', '301')->firstOrFail()->kelas->pluck('id')->all())->toBe([$a->id]);
});

test('dosen sees the mahasiswa of the selected kelas', function () {
    $dosenUser = User::factory()->create(['role' => User::ROLE_DOSEN]);
    Dosen::create(['user_id' => $dosenUser->id, 'nidn' => '0001']);
    $a = makeKelas('SI-A');
    $m = makeMahasiswa('401');
    $m->kelas()->attach($a->id);

    $this->actingAs($dosenUser)->get(route('dosen.mahasiswa.index', ['kelas_id' => $a->id]))
        ->assertOk()->assertSee('401')->assertSee($m->user->name);
});
