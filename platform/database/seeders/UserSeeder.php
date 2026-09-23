<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Core Four Admin', 'email' => 'admin@corefourroofing.com', 'role' => 'admin'],
            ['name' => 'Agency Admin', 'email' => 'agency@corefourroofing.com', 'role' => 'agency'],
            ['name' => 'Core Four Owner', 'email' => 'owner@corefourroofing.com', 'role' => 'owner'],
            ['name' => 'Office Staff', 'email' => 'staff@corefourroofing.com', 'role' => 'staff'],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => 'CoreFour2026!',
                    'is_active' => true,
                ]
            );
        }
    }
}
