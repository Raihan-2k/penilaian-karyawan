<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShiftSeeder::class,
            OwnerUserSeeder::class,
            FixedAppraisalCriteriaSeeder::class,
            AdminUserSeeder::class,
            AdministratorUserSeeder::class,
            ManagerUserSeeder::class,
        ]);
    }
}
