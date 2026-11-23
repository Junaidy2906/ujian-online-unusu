<?php

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

test('the latest valid row for a NIM updates the same student account', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $user = User::factory()->create(['role' => User::ROLE_MAHASISWA]);
    $originalPassword = $user->password;
    $student = Mahasiswa::create([
        'user_id' => $user->id,
        'nim' => '00123',
        'prodi' => 'Prodi lama',
        'telepon' => '0812345678',
    ]);

    $sheet = new Spreadsheet;
    $sheet->getActiveSheet()->fromArray([
        ['name', 'email', 'nim', 'prodi'],
        ['Baris pertama', 'lama@example.com', '00123', 'Prodi lama'],
        ['Data terbaru', 'baru@example.com', '00123', 'Prodi baru'],
        ['Baris tidak valid', '', '00123', 'Tidak diimpor'],
        ['Mahasiswa baru', $admin->email, '00124', 'Prodi baru'],
    ]);
    $path = tempnam(sys_get_temp_dir(), 'mahasiswa-');

    try {
        (new Xlsx($sheet))->save($path);
        $file = UploadedFile::fake()->createWithContent('mahasiswa.xlsx', file_get_contents($path));
        $this->actingAs($admin)
            ->post(route('admin.mahasiswa.template.upload'), ['template_file' => $file], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('redirect', route('admin.mahasiswa.index', absolute: false))
            ->assertSessionHas('success', fn ($message) => str_contains($message, '1 ditambahkan, 1 diperbarui, 1 baris tidak valid dilewati'));
    } finally {
        $sheet->disconnectWorksheets();
        unlink($path);
    }

    expect($student->refresh()->prodi)->toBe('Prodi baru')
        ->and($student->telepon)->toBe('0812345678')
        ->and($student->user_id)->toBe($user->id)
        ->and($user->refresh()->name)->toBe('Data terbaru')
        ->and($user->email)->toBe('baru@example.com')
        ->and($user->password)->toBe($originalPassword);

    $newStudent = Mahasiswa::where('nim', '00124')->firstOrFail();
    expect($newStudent->user->email)->toBe('00124@student.local')
        ->and(Hash::check('00124', $newStudent->user->password))->toBeTrue()
        ->and($admin->refresh()->role)->toBe(User::ROLE_ADMIN);

    $updatedFile = UploadedFile::fake()->createWithContent(
        'mahasiswa.csv',
        "name,email,nim,password\nMahasiswa baru direvisi,{$admin->email},00124,pass-baru-123\n",
    );
    $this->post(route('admin.mahasiswa.template.upload'), ['template_file' => $updatedFile])
        ->assertRedirect(route('admin.mahasiswa.index'));

    expect($newStudent->refresh()->user->name)->toBe('Mahasiswa baru direvisi')
        ->and($newStudent->user->email)->toBe('00124@student.local')
        ->and(Hash::check('pass-baru-123', $newStudent->user->password))->toBeTrue();
    $this->assertDatabaseCount('mahasiswa', 2);
    $this->assertDatabaseCount('users', 3);
});

test('a file without the required student columns returns an upload error', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $file = UploadedFile::fake()->createWithContent('mahasiswa.csv', "nama,alamat\nMahasiswa,Ujung Tanjung\n");

    $this->actingAs($admin)
        ->post(route('admin.mahasiswa.template.upload'), ['template_file' => $file], ['Accept' => 'application/json'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('template_file');

    $this->assertDatabaseCount('mahasiswa', 0);
});
