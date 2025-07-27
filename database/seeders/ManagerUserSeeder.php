<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee; // PENTING: Impor model Employee
use Illuminate\Support\Facades\Hash;
use App\Models\Shift;

class ManagerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah user manager sudah ada untuk menghindari duplikasi
        if (!User::where('email', 'manager@example.com')->exists()) {
            // 1. Buat User baru
            $user = User::create([
                'name' => 'Manager Operasional',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'), // Ganti dengan password yang kuat!
                'role' => 'manager',
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]);
            $defaultShift = Shift::where('name', 'Shift 6 Hari - Libur Kamis')->first();
            
            // 2. Buat Employee yang berelasi dengan User ini
            Employee::create([
                'user_id' => $user->id,
                'nip' => 'MGR001', // NIP dummy untuk manager
                'position' => 'Operational Manager',
                'name' => $user->name,
                'shift_id' => $defaultShift ? $defaultShift->id : null,
                'hire_date' => now()->subYears(2)->toDateString(),
                'pendidikan_terakhir' => 'S1',
                'nomor_telepon' => '083333333333',
                'tanggal_lahir' => '1985-08-20',
            ]);
            $this->command->info('Manager employee created and linked!');

        } else {
            $this->command->info('Manager user already exists.');
        }
    }
}
