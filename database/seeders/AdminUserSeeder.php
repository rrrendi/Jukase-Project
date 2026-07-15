<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Membuat akun login Admin (Owner Jukase Project) sesuai F-01
     * dan Tabel 1.4 (Definisi Aktor). Pelanggan TIDAK memiliki akun
     * (guest checkout), sehingga hanya satu akun admin yang dibuat.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@jukase.com'],
            [
                'name' => 'Fijia Al Hadiansyah',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
