<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User Pegawai (Untuk Test Input SKP)
        User::create([
            'nama'     => 'Budi Pegawai',
            'username' => 'budi',
            'email'    => 'pegawai@test.com',
            'nip'      => '199001012020011001',
            'role'     => 'pegawai',
            'password' => Hash::make('password'), // Password: password
        ]);

        // 2. User Admin / Verifikator
        User::create([
            'nama'     => 'Admin SKP',
            'username' => 'admin',
            'email'    => 'admin@test.com',
            'nip'      => '198501012015011002',
            'role'     => 'admin', // Sesuaikan dengan enum role kamu
            'password' => Hash::make('password'),
        ]);

        // 3. User Kepala (Untuk TTD Final)
        User::updateOrCreate([
            'nama'     => 'Ahmad Muhajir, A.Md.PK., S.Tr.RMIK',
            'username' => 'kepala',
            'email'    => 'kepala@test.com',
            'nip'      => '19900107 202421 1 009',
            'role'     => 'kepala',
            'password' => Hash::make('password'),
        ]);

        // 4. User Pegawai (Untuk Input SKP)
        User::updateOrCreate([
            'nama'     => 'Intansari',
            'username' => 'intansari',
            'email'    => 'intansari@test.com',
            'nip'      => '199511212019032026',
            'role'     => 'pegawai',
            'password' => Hash::make('password'),
        ]);
    }
}