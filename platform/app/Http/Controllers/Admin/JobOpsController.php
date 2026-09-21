<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobAppointment;
use App\Models\JobChangeOrder;
use App\Models\JobCostLine;
use App\Models\JobInvoice;
use App\Models\JobMaterial;
use App\Models\JobQuote;
use App\Models\JobTask;
use App\Services\JobOpsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobOpsController extends Controller
{
    public function __construct(private JobOpsService $ops) {}

    public function note(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
            'customer_visible' => ['nullable', 'boolean'],
        ]);

        $this->ops->addNote($job, $data['body'], $request->user(), $request->boolean('customer_visible'));

        return $this->toTab($job, 'notes', 'Note saved.');
    }

    public function storeAppointment(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', JobAppointment::TYPES)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'title' => ['nullable', 'string', 'max:160'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $this->ops->scheduleAppointment($job, $data, $request->user());

        return $this->toTab($job, 'schedule', 'Appointment booked.');
    }

    public function updateAppointment(Request $request, Job $job, JobAppointment $appointment): RedirectResponse
    {
        abort_unless($appointment->job_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', JobAppointment::STATUSES)],
        ]);

        $this->ops->updateAppointment($appointment, $data, $request->user());

        return $this->toTab($job, 'schedule', 'Appointment updated.');
    }

    public function storeQuote(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'item_label' => ['array'],
            'item_label.*' => ['nullable', 'string', 'max:160'],
            'item_qty' => ['array'],
            'item_qty.*' => ['nullable', 'numeric', 'min:0'],
            'item_unit' => ['array'],
            'item_unit.*' => ['nullable', 'string', 'max:24'],
            'item_price' => ['array'],
            'item_price.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = [];
        foreach ($data['item_label'] ?? [] as $i => $label) {
            $items[] = [
                'label' => $label,
                'qty' => $data['item_qty'][$i] ?? 1,
                'unit' => $data['item_unit'][$i] ?? 'ea',
                'unit_price' => $data['item_price'][$i] ?? 0,
            ];
        }

        $this->ops->createQuote($job, $data, $items, $request->user());

        return $this->toTab($job, 'quotes', 'Estimate saved.');
    }

    public function addQuoteItem(Request $request, Job $job, JobQuote $quote): RedirectResponse
    {
        abort_unless($quote->job_id === $job->id, 404);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:160'],
            'qty' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:24'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->ops->addQuoteItem($quote, $data, $request->user());

        return $this->toTab($job, 'quotes', 'Line added.');
    }

    public function quoteStatus(Request $request, Job $job, JobQuote $quote): RedirectResponse
    {
        abort_unless($quote->job_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', JobQuote::STATUSES)],
        ]);

        $this->ops->setQuoteStatus($quote, $data['status'], $request->user());

        return $this->toTab($job, 'quotes', 'Estimate '.$data['status'].'.');
    }

    public function storeChangeOrder(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'amount' => ['required', 'numeric'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ops->createChangeOrder($job, $data, $request->user());

        return $this->toTab($job, 'changes', 'Change order saved.');
    }

    public function changeOrderStatus(Request $request, Job $job, JobChangeOrder $order): RedirectResponse
    {
        abort_unless($order->job_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', JobChangeOrder::STATUSES)],
        ]);

        $this->ops->setChangeOrderStatus($order, $data['status'], $request->user());

        return $this->toTab($job, 'changes', 'Change order '.$data['status'].'.');
    }

    public function storeInvoice(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'in:'.implode(',', JobInvoice::KINDS)],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_on' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ops->createInvoice($job, $data, $request->user());

        return $this->toTab($job, 'invoices', 'Invoice created.');
    }

    public function invoiceStatus(Request $request, Job $job, JobInvoice $invoice): RedirectResponse
    {
        abort_unless($invoice->job_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', JobInvoice::STATUSES)],
        ]);

        $this->ops->setInvoiceStatus($invoice, $data['status'], $request->user());

        return $this->toTab($job, 'invoices', 'Invoice '.$data['status'].'.');
    }

    public function storeMaterial(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'qty' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:24'],
            'status' => ['nullable', 'in:'.implode(',', JobMaterial::STATUSES)],
            'vendor' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ops->addMaterial($job, $data, $request->user());

        return $this->toTab($job, 'crew', 'Material added.');
    }

    public function materialStatus(Request $request, Job $job, JobMaterial $material): RedirectResponse
    {
        abort_unless($material->job_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', JobMaterial::STATUSES)],
        ]);

        $this->ops->setMaterialStatus($material, $data['status'], $request->user());

        return $this->toTab($job, 'crew', 'Material updated.');
    }

    public function storeTask(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_on' => ['nullable', 'date'],
        ]);

        $this->ops->addTask($job, $data, $request->user());

        return $this->toTab($job, 'tasks', 'Task added.');
    }

    public function toggleTask(Request $request, Job $job, JobTask $task): RedirectResponse
    {
        abort_unless($task->job_id === $job->id, 404);

        $this->ops->toggleTask($task, $request->user());

        return $this->toTab($job, 'tasks', 'Task updated.');
    }

    public function storeWarranty(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'in:workmanship,manufacturer'],
            'manufacturer' => ['nullable', 'string', 'max:120'],
            'registration' => ['nullable', 'string', 'max:80'],
            'starts_on' => ['nullable', 'date'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ops->addWarranty($job, $data, $request->user());

        return $this->toTab($job, 'warranties', 'Warranty recorded.');
    }

    public function storeCost(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'in:'.implode(',', JobCostLine::KINDS)],
            'label' => ['required', 'string', 'max:160'],
            'amount' => ['required', 'numeric'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ops->addCostLine($job, $data, $request->user());

        return $this->toTab($job, 'costing', 'Cost logged.');
    }

    protected function toTab(Job $job, string $tab, string $message): RedirectResponse
    {
        return redirect()
            ->route('admin.jobs.show', ['job' => $job, 'tab' => $tab])
            ->with('success', $message);
    }
}
