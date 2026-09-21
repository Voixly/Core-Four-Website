<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CitySeeder::class,
            EmailSequenceSeeder::class,
            ReportSeeder::class,
            GuideSeeder::class,
            SettingSeeder::class,
            LeadSeeder::class,
            ReviewSeeder::class,
            PipelineSeeder::class,
            JobSeeder::class,
            JobOpsSeeder::class,
        ]);
    }
}
