<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // Untuk generate password jika ada yang kosong

class MigrateEmployeeLoginDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = Employee::all(); // Ambil semua data karyawan dari tabel lama

        foreach ($employees as $employee) {
            // Cek apakah user dengan email ini sudah ada di tabel users
            // Ini penting jika Anda mungkin sudah punya user di tabel users
            $user = User::where('email', $employee->email)->first();

            if (!$user) {
                // Jika user belum ada, buat user baru di tabel 'users'
                $user = User::create([
                    'name' => $employee->name,
                    'email' => $employee->email,
                    // Pastikan password sudah di-hash. Jika belum, hash di sini.
                    // Jika ada kemungkinan password kosong, berikan default hash.
                    'password' => $employee->password ? $employee->password : Hash::make(Str::random(10)),
                    'role' => $employee->role ?? 'karyawan', // Gunakan role yang ada di employee atau default 'karyawan'
                    'email_verified_at' => now(), // Asumsikan sudah diverifikasi, atau set null
                ]);
                $this->command->info("Created user: {$user->email}");
            } else {
                $this->command->info("User already exists: {$user->email}. Linking existing user.");
                // Jika user sudah ada, pastikan role-nya sesuai
                if ($user->role !== ($employee->role ?? 'karyawan')) {
                    $user->role = $employee->role ?? 'karyawan';
                    $user->save();
                    $this->command->info("Updated role for user: {$user->email}");
                }
            }

            // Tautkan karyawan ke user yang baru dibuat/ditemukan
            $employee->user_id = $user->id;
            $employee->save();
            $this->command->info("Linked employee ID {$employee->id} to user ID {$user->id}");
        }

        $this->command->info('Data login karyawan berhasil dimigrasi ke tabel users dan ditautkan.');
    }
}