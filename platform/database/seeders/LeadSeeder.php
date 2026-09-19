<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::query()->where('role', 'staff')->first();

        $samples = [
            ['name' => 'Maria Delgado', 'phone' => '2815550142', 'email' => 'maria.d@example.com', 'city' => 'Tomball', 'zip' => '77375', 'type' => 'residential', 'need' => 'inspection', 'source' => 'website', 'status' => 'new'],
            ['name' => 'James Whitaker', 'phone' => '8325550198', 'email' => 'jwhitaker@example.com', 'city' => 'Cypress', 'zip' => '77429', 'type' => 'residential', 'need' => 'leak', 'source' => 'city', 'status' => 'contacted'],
            ['name' => 'Northgate Plaza Mgmt', 'phone' => '7135550110', 'email' => 'facilities@northgate.example', 'city' => 'Houston', 'zip' => '77070', 'type' => 'commercial', 'need' => 'survey', 'source' => 'commercial', 'status' => 'inspected'],
            ['name' => 'Guide download — Katy', 'phone' => '2815550177', 'email' => 'pat.nguyen@example.com', 'city' => 'Katy', 'zip' => '77494', 'type' => 'residential', 'need' => 'replace', 'source' => 'guide', 'status' => 'new'],
        ];

        foreach ($samples as $row) {
            $lead = Lead::query()->firstOrCreate(
                ['email' => $row['email']],
                $row + ['assigned_to' => $staff?->id]
            );
            if ($lead->wasRecentlyCreated) {
                $lead->log($staff, 'created', 'Seeded demo lead from '.$lead->source);
            }
        }
    }
}
