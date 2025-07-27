<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee; // PENTING: Impor model Employee
use Illuminate\Support\Facades\Hash;
use App\Models\Shift;

class OwnerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah user owner sudah ada untuk menghindari duplikasi
        if (!User::where('email', 'owner@example.com')->exists()) {
            // 1. Buat User baru
            $user = User::create([
                'name' => 'Super Owner',
                'email' => 'owner@example.com',
                'password' => Hash::make('password'), // Ganti dengan password yang kuat!
                'role' => 'owner',
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]);
            $defaultShift = Shift::where('name', 'Shift 6 Hari - Libur Minggu')->first();

            // 2. Buat Employee yang berelasi dengan User ini
            Employee::create([
                'user_id' => $user->id,
                'nip' => 'OWN001', // NIP dummy untuk owner
                'name' => $user->name,
                'position' => 'CEO',
                'shift_id' => $defaultShift ? $defaultShift->id : null,
                'hire_date' => now()->subYears(5)->toDateString(), // Contoh tanggal masuk
                'pendidikan_terakhir' => 'S3',
                'nomor_telepon' => '081111111111',
                'tanggal_lahir' => '1980-01-01',
            ]);
            $this->command->info('Owner user and employee created!');

        } else {
            $this->command->info('Owner user already exists.');
        }
    }
}
