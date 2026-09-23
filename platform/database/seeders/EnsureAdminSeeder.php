<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnsureAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@corefourroofing.com';
        $user = User::query()->where('email', $email)->first();
        $passwordReady = Setting::get('bootstrap_admin_password') === '1';

        if (! $user) {
            User::query()->create([
                'name' => 'Core Four Admin',
                'email' => $email,
                'role' => 'admin',
                'password' => 'CoreFour2026!',
                'is_active' => true,
            ]);
            Setting::put('bootstrap_admin_password', '1');

            return;
        }

        $user->forceFill([
            'role' => 'admin',
            'is_active' => true,
            ...($passwordReady ? [] : ['password' => 'CoreFour2026!']),
        ])->save();

        if (! $passwordReady) {
            Setting::put('bootstrap_admin_password', '1');
        }
    }
}
