<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class SchemaInstaller
{
    public static function ensure(): string
    {
        $code = Artisan::call('migrate', ['--force' => true]);
        $output = trim(Artisan::output());

        if ($code !== 0 || ! Schema::hasTable('cities')) {
            return $output;
        }

        foreach (['CitySeeder', 'SettingSeeder', 'GuideSeeder', 'PipelineSeeder', 'ReportSeeder', 'EmailSequenceSeeder'] as $seeder) {
            Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            $output .= "\n".trim(Artisan::output());
        }

        if (Schema::hasTable('users') && User::query()->count() === 0) {
            Artisan::call('db:seed', ['--class' => 'UserSeeder', '--force' => true]);
            $output .= "\n".trim(Artisan::output());
        }

        if (Schema::hasTable('users')) {
            Artisan::call('db:seed', ['--class' => 'EnsureAdminSeeder', '--force' => true]);
            $output .= "\n".trim(Artisan::output());
        }

        return trim($output);
    }
}
