<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee; // PENTING: Impor model Employee
use Illuminate\Support\Facades\Hash;
use App\Models\Shift;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah user admin sudah ada untuk menghindari duplikasi
        if (!User::where('email', 'admin@example.com')->exists()) {
            // 1. Buat User baru
            $user = User::create([
                'name' => 'Admin Sistem',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Ganti dengan password yang kuat!
                'role' => 'admin',
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]);
        $defaultShift = Shift::where('name', 'Shift 6 Hari - Libur Rabu')->first();

            // 2. Buat Employee yang berelasi dengan User ini
            Employee::create([
                'user_id' => $user->id,
                'nip' => 'ADM001', // NIP dummy untuk admin
                'name' => $user->name,
                'position' => 'IT',
                'shift_id' => $defaultShift ? $defaultShift->id : null,
                'hire_date' => now()->subYears(3)->toDateString(),
                'pendidikan_terakhir' => 'S1',
                'nomor_telepon' => '082222222222',
                'tanggal_lahir' => '1990-05-10',
            ]);
            $this->command->info('Owner user and employee created!');

        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
