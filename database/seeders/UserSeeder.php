<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada menggunakan Eloquent
        User::query()->delete();

        $users = [
            [
                'username' => 'superadmin',
                'password' => Hash::make('superadmin123'),
                'nama_lengkap' => 'Super Administrator',
                'role' => 'Super Admin',
            ],
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Administrator',
                'role' => 'Admin',
            ],
            [
                'username' => 'user',
                'password' => Hash::make('user123'),
                'nama_lengkap' => 'User Poliklinik',
                'role' => 'User',
            ],
            [
                'username' => 'dr.budi',
                'password' => Hash::make('dokter123'),
                'nama_lengkap' => 'Dr. Budi Santoso',
                'role' => 'Admin',
            ],
            [
                'username' => 'perawat.ani',
                'password' => Hash::make('perawat123'),
                'nama_lengkap' => 'Ani Wijaya',
                'role' => 'User',
            ],
                        [
                'username' => 'didisuryadi',
                'password' => Hash::make('didisuryadi'),
                'nama_lengkap' => 'Didi Suryadi',
                'role' => 'User',
            ],
                        [
                'username' => 'faridwajidi',
                'password' => Hash::make('faridwajidi'),
                'nama_lengkap' => 'Farid Wajidi',
                'role' => 'User',
            ],
                        [
                'username' => 'ellienm',
                'password' => Hash::make('ellienm'),
                'nama_lengkap' => 'Ellien M',
                'role' => 'User',
            ],
        ];

        // Using Eloquent create method with mass assignment
        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
