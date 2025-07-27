<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan mengimpor model User
use Illuminate\Support\Facades\Hash; // Untuk mengenkripsi password

class AdministratorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah user administrator sudah ada untuk menghindari duplikasi
        if (!User::where('email', 'administrator@example.com')->exists()) {
            User::create([
                'name' => 'Administrator Sistem',
                'email' => 'administrator@example.com',
                'password' => Hash::make('password'), // Ganti dengan password yang kuat!
                'role' => 'administrator', // Tetapkan peran 'administrator'
                'email_verified_at' => now(),
            ]);
            $this->command->info('Administrator user created!');
        } else {
            $this->command->info('Administrator user already exists.');
        }
    }
}