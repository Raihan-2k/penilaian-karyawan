<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppraisalCriterion;
use Illuminate\Support\Facades\DB; // PENTING: Import DB facade

class FixedAppraisalCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pemeriksaan foreign key sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Hapus semua kriteria yang sudah ada untuk memastikan hanya kriteria tetap yang ada
        // Hati-hati: ini akan menghapus semua data di tabel appraisal_criteria!
        AppraisalCriterion::truncate();

        // Aktifkan kembali pemeriksaan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Definisi kriteria penilaian yang baru dan lebih detail
        $criteriaToSeed = [
            // 1. Kinerja
            ['name' => 'Produktivitas', 'description' => 'Kemampuan menyelesaikan tugas dalam jumlah dan waktu yang ditentukan.'],
            ['name' => 'Ketepatan waktu', 'description' => 'Kepatuhan terhadap deadline dan jadwal kerja.'],
            ['name' => 'Kualitas Pekerjaan', 'description' => 'Akurasi, kelengkapan, dan standar hasil kerja.'],
            ['name' => 'Efisiensi Kerja', 'description' => 'Kemampuan menggunakan sumber daya (waktu, alat) secara optimal.'],

            // 2. Sikap & Prilaku
            ['name' => 'Kerjasama tim', 'description' => 'Kemampuan bekerja sama dengan rekan kerja untuk mencapai tujuan bersama.'],
            ['name' => 'Komunikasi', 'description' => 'Kejelasan dan efektivitas dalam menyampaikan informasi dan mendengarkan.'],
            ['name' => 'Etika Kerja', 'description' => 'Integritas, kejujuran, dan profesionalisme dalam lingkungan kerja.'],
            ['name' => 'Kepatuhan terhadap peraturan', 'description' => 'Ketaatan terhadap kebijakan dan prosedur perusahaan.'],

            // 3. Kepribadian Profesional
            ['name' => 'Inisiatif', 'description' => 'Kemampuan untuk memulai tindakan tanpa harus diminta.'],
            ['name' => 'Kreativitas', 'description' => 'Kemampuan menghasilkan ide-ide baru dan solusi inovatif.'],
            ['name' => 'Adaptabilitas', 'description' => 'Fleksibilitas dalam menghadapi perubahan dan situasi baru.'],
            ['name' => 'Konsistensi', 'description' => 'Keterandalan dan stabilitas dalam kinerja dan perilaku.'],

            // 4. Kehadiran & Disiplin
            ['name' => 'Kehadiran tepat waktu', 'description' => 'Kepatuhan terhadap jam masuk dan pulang kerja.'],
            ['name' => 'Kepatuhan jam kerja', 'description' => 'Mematuhi jam kerja yang ditetapkan dan tidak menyalahgunakannya.'],
        ];

        foreach ($criteriaToSeed as $criterion) {
            AppraisalCriterion::create($criterion);
        }

        $this->command->info('Fixed appraisal criteria seeded successfully!');
    }
}
