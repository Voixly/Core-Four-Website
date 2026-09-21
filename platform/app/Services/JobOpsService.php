<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobAppointment;
use App\Models\JobChangeOrder;
use App\Models\JobCostLine;
use App\Models\JobInvoice;
use App\Models\JobMaterial;
use App\Models\JobQuote;
use App\Models\JobQuoteItem;
use App\Models\JobTask;
use App\Models\JobWarranty;
use App\Models\User;

class JobOpsService
{
    public function addNote(Job $job, string $body, ?User $actor = null, bool $customerVisible = false): void
    {
        $job->log($actor, 'note', $body, $customerVisible);
    }

    public function scheduleAppointment(Job $job, array $data, ?User $actor = null): JobAppointment
    {
        $appointment = $job->appointments()->create([
            'assigned_to' => $data['assigned_to'] ?? $job->assigned_to,
            'type' => $data['type'] ?? 'inspection',
            'status' => $data['status'] ?? 'scheduled',
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'] ?? null,
            'title' => $data['title'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        if (empty($job->scheduled_at) || $appointment->starts_at->lt($job->scheduled_at)) {
            $job->update(['scheduled_at' => $appointment->starts_at]);
        }

        $job->log(
            $actor,
            'appointment',
            $appointment->label().' · '.$appointment->starts_at->timezone(config('app.timezone'))->format('D M j, g:ia'),
            true
        );

        return $appointment;
    }

    public function updateAppointment(JobAppointment $appointment, array $data, ?User $actor = null): JobAppointment
    {
        $appointment->update($data);
        $appointment->job->log($actor, 'appointment', 'Updated '.$appointment->fresh()->label());

        return $appointment->fresh();
    }

    public function createQuote(Job $job, array $data, array $items, ?User $actor = null): JobQuote
    {
        $quote = $job->quotes()->create([
            'title' => $data['title'],
            'status' => $data['status'] ?? 'draft',
            'tax' => $data['tax'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'sent_at' => ($data['status'] ?? 'draft') === 'sent' ? now() : null,
        ]);

        foreach ($items as $i => $item) {
            if (empty($item['label'])) {
                continue;
            }
            $qty = (float) ($item['qty'] ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $quote->items()->create([
                'label' => $item['label'],
                'qty' => $qty,
                'unit' => $item['unit'] ?? 'ea',
                'unit_price' => $price,
                'amount' => $qty * $price,
                'sort' => $i,
            ]);
        }

        $quote->recalculate();
        $job->log($actor, 'quote', $quote->title.' · $'.number_format((float) $quote->fresh()->total, 2));

        return $quote->fresh('items');
    }

    public function addQuoteItem(JobQuote $quote, array $data, ?User $actor = null): JobQuoteItem
    {
        $qty = (float) ($data['qty'] ?? 1);
        $price = (float) ($data['unit_price'] ?? 0);
        $item = $quote->items()->create([
            'label' => $data['label'],
            'qty' => $qty,
            'unit' => $data['unit'] ?? 'ea',
            'unit_price' => $price,
            'amount' => $qty * $price,
            'sort' => ((int) $quote->items()->max('sort')) + 1,
        ]);
        $quote->recalculate();
        $quote->job->log($actor, 'quote', 'Line added to '.$quote->title);

        return $item;
    }

    public function setQuoteStatus(JobQuote $quote, string $status, ?User $actor = null): JobQuote
    {
        $payload = ['status' => $status];
        if ($status === 'sent') {
            $payload['sent_at'] = $quote->sent_at ?? now();
        }
        if (in_array($status, ['approved', 'declined'], true)) {
            $payload['decided_at'] = now();
        }
        $quote->update($payload);
        $quote->job->log($actor, 'quote', $quote->title.' is '.$status, $status !== 'draft');

        return $quote->fresh();
    }

    public function createChangeOrder(Job $job, array $data, ?User $actor = null): JobChangeOrder
    {
        $order = $job->changeOrders()->create([
            'title' => $data['title'],
            'amount' => $data['amount'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
        ]);
        $job->log($actor, 'change_order', $order->title.' · $'.number_format((float) $order->amount, 2));

        return $order;
    }

    public function setChangeOrderStatus(JobChangeOrder $order, string $status, ?User $actor = null): JobChangeOrder
    {
        $order->update(['status' => $status]);
        $order->job->log($actor, 'change_order', $order->title.' is '.$status, $status !== 'draft');

        return $order->fresh();
    }

    public function createInvoice(Job $job, array $data, ?User $actor = null): JobInvoice
    {
        $invoice = $job->invoices()->create([
            'number' => $data['number'] ?? $this->nextInvoiceNumber($job),
            'kind' => $data['kind'] ?? 'deposit',
            'amount' => $data['amount'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'due_on' => $data['due_on'] ?? null,
            'notes' => $data['notes'] ?? null,
            'paid_at' => ($data['status'] ?? '') === 'paid' ? now() : null,
        ]);
        $job->log($actor, 'invoice', $invoice->number.' · $'.number_format((float) $invoice->amount, 2), $invoice->status !== 'draft');

        return $invoice;
    }

    public function setInvoiceStatus(JobInvoice $invoice, string $status, ?User $actor = null): JobInvoice
    {
        $invoice->update([
            'status' => $status,
            'paid_at' => $status === 'paid' ? ($invoice->paid_at ?? now()) : null,
        ]);
        $invoice->job->log($actor, 'invoice', $invoice->number.' is '.$status, $status !== 'draft');

        return $invoice->fresh();
    }

    public function addMaterial(Job $job, array $data, ?User $actor = null): JobMaterial
    {
        $material = $job->materials()->create([
            'name' => $data['name'],
            'qty' => $data['qty'] ?? 1,
            'unit' => $data['unit'] ?? 'ea',
            'status' => $data['status'] ?? 'needed',
            'vendor' => $data['vendor'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        $job->log($actor, 'material', $material->name.' · '.$material->status);

        return $material;
    }

    public function setMaterialStatus(JobMaterial $material, string $status, ?User $actor = null): JobMaterial
    {
        $material->update(['status' => $status]);
        $material->job->log($actor, 'material', $material->name.' is '.$status);

        return $material->fresh();
    }

    public function addTask(Job $job, array $data, ?User $actor = null): JobTask
    {
        $task = $job->tasks()->create([
            'title' => $data['title'],
            'assigned_to' => $data['assigned_to'] ?? null,
            'due_on' => $data['due_on'] ?? null,
            'sort' => ((int) $job->tasks()->max('sort')) + 1,
        ]);
        $job->log($actor, 'task', $task->title);

        return $task;
    }

    public function toggleTask(JobTask $task, ?User $actor = null): JobTask
    {
        $task->update(['is_done' => ! $task->is_done]);
        $task->job->log($actor, 'task', $task->title.' '.($task->is_done ? 'done' : 'reopened'));

        return $task->fresh();
    }

    public function addWarranty(Job $job, array $data, ?User $actor = null): JobWarranty
    {
        $warranty = $job->warranties()->create([
            'kind' => $data['kind'] ?? 'workmanship',
            'manufacturer' => $data['manufacturer'] ?? null,
            'registration' => $data['registration'] ?? null,
            'starts_on' => $data['starts_on'] ?? null,
            'expires_on' => $data['expires_on'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        $job->log($actor, 'warranty', ucfirst($warranty->kind).' warranty recorded', true);

        return $warranty;
    }

    public function addCostLine(Job $job, array $data, ?User $actor = null): JobCostLine
    {
        $line = $job->costLines()->create([
            'kind' => $data['kind'] ?? 'material',
            'label' => $data['label'],
            'amount' => $data['amount'] ?? 0,
            'notes' => $data['notes'] ?? null,
        ]);
        $job->log($actor, 'cost', $line->label.' · $'.number_format((float) $line->amount, 2));

        return $line;
    }

    public function seedDefaultTasks(Job $job): void
    {
        if ($job->tasks()->exists()) {
            return;
        }

        $titles = match ($job->pipeline?->slug) {
            'emergency-tarp' => [
                'Confirm water intrusion',
                'Tarp the opening',
                'Photo the work',
                'Schedule permanent repair',
            ],
            'residential-storm-insurance' => [
                'Inspect and photograph',
                'Meet the adjuster',
                'Write the estimate',
                'Order materials',
                'Install',
                'Final walkthrough',
            ],
            'commercial-survey-bid' => [
                'Site survey',
                'Core / moisture scan notes',
                'Write the bid',
                'Present to owner / PM',
            ],
            default => [
                'Inspect site',
                'Upload photos',
                'Write estimate',
                'Schedule work',
                'Order materials',
                'Final walkthrough',
            ],
        };

        foreach ($titles as $i => $title) {
            $job->tasks()->create(['title' => $title, 'sort' => $i]);
        }
    }

    public function nextInvoiceNumber(Job $job): string
    {
        $prefix = 'INV-'.$job->number.'-';
        $last = JobInvoice::query()->where('number', 'like', $prefix.'%')->orderByDesc('number')->value('number');
        $n = $last ? ((int) substr($last, -2)) + 1 : 1;

        return $prefix.str_pad((string) $n, 2, '0', STR_PAD_LEFT);
    }
}
