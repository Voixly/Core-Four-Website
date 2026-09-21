<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\User;
use App\Services\JobOpsService;
use Illuminate\Database\Seeder;

class JobOpsSeeder extends Seeder
{
    public function run(): void
    {
        $ops = app(JobOpsService::class);
        $staff = User::staff()->first();
        $jobs = Job::query()->with(['pipeline', 'quotes', 'appointments', 'lead'])->get();

        foreach ($jobs as $job) {
            $ops->seedDefaultTasks($job);

            if (! $job->roof_type) {
                $job->update(match ($job->pipeline?->slug) {
                    'residential-storm-insurance' => [
                        'roof_type' => 'shingle',
                        'squares' => 28,
                        'stories' => 2,
                        'pitch' => '7/12',
                        'material_system' => 'GAF Timberline HDZ',
                        'insurance_carrier' => 'State Farm',
                        'claim_number' => 'TX-88421',
                        'access_notes' => 'Gate code 4412. Dog in backyard — call first.',
                        'crew_name' => 'Crew A',
                        'address' => $job->address ?: '18412 Pine Mill Rd',
                    ],
                    'residential-leak-repair' => [
                        'roof_type' => 'shingle',
                        'squares' => 22,
                        'stories' => 1,
                        'pitch' => '4/12',
                        'material_system' => 'Repair — pipe boot and flashing',
                        'access_notes' => 'Leak over kitchen. Homeowner home after 3.',
                        'crew_name' => 'Service',
                        'address' => $job->address ?: '9022 Teal Run',
                    ],
                    default => [
                        'roof_type' => 'tpo',
                        'squares' => 140,
                        'stories' => 1,
                        'pitch' => 'low slope',
                        'material_system' => '60-mil TPO',
                        'hoa_name' => null,
                        'access_notes' => 'Load dock on the north side. Property manager on site.',
                        'crew_name' => 'Commercial 1',
                        'address' => $job->address ?: '11800 Northgate Dr',
                    ],
                });
            }

            if ($job->appointments()->doesntExist()) {
                $ops->scheduleAppointment($job, [
                    'type' => $job->type === 'commercial' ? 'inspection' : 'inspection',
                    'starts_at' => now()->addDay()->setTime(9, 0),
                    'ends_at' => now()->addDay()->setTime(10, 0),
                    'title' => 'Site inspection',
                    'assigned_to' => $staff?->id,
                    'notes' => 'Walk the roof and photograph.',
                ], $staff);

                if ($job->pipeline?->slug === 'residential-storm-insurance') {
                    $ops->scheduleAppointment($job, [
                        'type' => 'adjuster',
                        'starts_at' => now()->addDays(4)->setTime(13, 0),
                        'ends_at' => now()->addDays(4)->setTime(14, 0),
                        'title' => 'Meet the adjuster',
                        'assigned_to' => $staff?->id,
                    ], $staff);
                }
            }

            if ($job->quotes()->doesntExist()) {
                $quote = $ops->createQuote($job, [
                    'title' => $job->type === 'commercial' ? 'TPO recover bid' : 'Roof replacement',
                    'tax' => 0,
                    'notes' => 'Demo pricing for the office.',
                    'status' => 'sent',
                ], $job->type === 'commercial' ? [
                    ['label' => 'Tear-off and haul', 'qty' => 140, 'unit' => 'sq', 'unit_price' => 85],
                    ['label' => '60-mil TPO system', 'qty' => 140, 'unit' => 'sq', 'unit_price' => 420],
                ] : [
                    ['label' => 'Tear-off', 'qty' => $job->squares ?: 24, 'unit' => 'sq', 'unit_price' => 65],
                    ['label' => 'Architectural shingles', 'qty' => $job->squares ?: 24, 'unit' => 'sq', 'unit_price' => 285],
                    ['label' => 'Ridge vent and pipe boots', 'qty' => 1, 'unit' => 'ls', 'unit_price' => 850],
                ], $staff);
                $ops->setQuoteStatus(
                    $quote,
                    $job->pipeline?->slug === 'residential-storm-insurance' ? 'approved' : 'sent',
                    $staff
                );
            }

            if ($job->materials()->doesntExist()) {
                $ops->addMaterial($job, [
                    'name' => $job->material_system ?: 'Roofing system',
                    'qty' => $job->squares ?: 20,
                    'unit' => 'sq',
                    'status' => 'needed',
                    'vendor' => 'ABC Supply',
                ], $staff);
            }

            if ($job->invoices()->doesntExist() && $job->pipeline?->slug !== 'commercial-survey-bid') {
                $ops->createInvoice($job, [
                    'kind' => 'deposit',
                    'amount' => 2500,
                    'status' => 'sent',
                    'due_on' => now()->addDays(7)->toDateString(),
                ], $staff);
            }

            if ($job->changeOrders()->doesntExist() && $job->pipeline?->slug === 'residential-storm-insurance') {
                $ops->createChangeOrder($job, [
                    'title' => 'Replace 6 sheets of decking',
                    'amount' => 1480,
                    'notes' => 'Soft deck found at the back slope.',
                    'status' => 'sent',
                ], $staff);
            }

            if ($job->warranties()->doesntExist() && $job->status === 'won') {
                $ops->addWarranty($job, [
                    'kind' => 'workmanship',
                    'starts_on' => now()->toDateString(),
                    'expires_on' => now()->addYears(10)->toDateString(),
                    'notes' => 'Core Four workmanship.',
                ], $staff);
            }

            if ($job->costLines()->doesntExist()) {
                $ops->addCostLine($job, [
                    'kind' => 'material',
                    'label' => 'Material takeoff',
                    'amount' => 4200,
                ], $staff);
                $ops->addCostLine($job, [
                    'kind' => 'labor',
                    'label' => 'Crew labor',
                    'amount' => 3100,
                ], $staff);
            }

            if (! $job->events()->where('event', 'note')->exists()) {
                $ops->addNote($job, 'Office file opened with site details, estimate, and first visit on the calendar.', $staff, true);
            }
        }
    }
}
