<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use App\Services\JobService;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(JobService::class);
        $staff = User::staff()->first();

        $samples = [
            ['email' => 'maria.d@example.com', 'slug' => 'residential-storm-insurance', 'payment' => 'insurance', 'urgency' => 'standard'],
            ['email' => 'jwhitaker@example.com', 'slug' => 'residential-leak-repair', 'payment' => 'retail', 'urgency' => 'emergency'],
            ['email' => 'facilities@northgate.example', 'slug' => 'commercial-survey-bid', 'payment' => 'commercial_capex', 'urgency' => 'standard'],
        ];

        foreach ($samples as $sample) {
            $lead = Lead::query()->where('email', $sample['email'])->first();
            $pipeline = Pipeline::query()->where('slug', $sample['slug'])->first();
            if (! $lead || ! $pipeline || $lead->job_id) {
                continue;
            }

            $service->createFromLead($lead, [
                'pipeline_id' => $pipeline->id,
                'payment_path' => $sample['payment'],
                'urgency' => $sample['urgency'],
                'assigned_to' => $staff?->id,
                'city' => $lead->city,
                'zip' => $lead->zip,
            ], $staff);
        }
    }
}
