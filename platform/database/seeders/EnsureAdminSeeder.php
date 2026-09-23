<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class EnsureAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->where('role', 'admin')->exists()) {
            return;
        }

        $existing = User::query()->where('email', 'admin@corefourroofing.com')->first();
        if ($existing) {
            $existing->forceFill([
                'role' => 'admin',
                'is_active' => true,
            ])->save();

            return;
        }

        User::query()->create([
            'name' => 'Core Four Admin',
            'email' => 'admin@corefourroofing.com',
            'role' => 'admin',
            'password' => 'CoreFour2026!',
            'is_active' => true,
        ]);
    }
}
