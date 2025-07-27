<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus shift lama jika ingin diganti total
        // Shift::truncate(); // Hati-hati: ini akan menghapus semua data shift yang ada!

        // Shift 6 Hari Kerja dengan Libur Bergantian
        $daysOfWeek = [
            0 => 'Minggu', // Sunday
            1 => 'Senin',  // Monday
            2 => 'Selasa', // Tuesday
            3 => 'Rabu',   // Wednesday
            4 => 'Kamis',  // Thursday
            5 => 'Jumat',  // Friday
            6 => 'Sabtu'   // Saturday
        ];

        foreach ($daysOfWeek as $offDayIndex => $offDayName) {
            $workingDays = [];
            for ($i = 0; $i <= 6; $i++) {
                if ($i !== $offDayIndex) {
                    $workingDays[] = $i;
                }
            }

            $shiftName = "Shift 6 Hari - Libur " . $offDayName;
            $description = "6 hari kerja, libur setiap hari " . $offDayName . ". Jam kerja 08:00 - 16:00.";

            if (!Shift::where('name', $shiftName)->exists()) {
                Shift::create([
                    'name' => $shiftName,
                    'description' => $description,
                    'schedule' => [
                        'working_days' => $workingDays,
                        'off_days' => [$offDayIndex]
                    ],
                    'start_time' => '08:00:00',
                    'end_time' => '16:00:00',
                ]);
                $this->command->info("Shift created: " . $shiftName);
            } else {
                $this->command->info("Shift already exists: " . $shiftName);
            }
        }

        // Anda bisa tetap mempertahankan shift 5 hari atau shift malam jika masih relevan
        if (!Shift::where('name', 'Shift Pagi (5 Hari)')->exists()) {
            Shift::create([
                'name' => 'Shift Pagi (5 Hari)',
                'description' => 'Senin - Jumat, 08:00 - 17:00',
                'schedule' => [
                    'working_days' => [1, 2, 3, 4, 5],
                    'off_days' => [0, 6]
                ],
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
            ]);
            $this->command->info('Shift created: Shift Pagi (5 Hari)');
        }
    }
}
