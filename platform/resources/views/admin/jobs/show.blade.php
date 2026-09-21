@extends('layouts.admin')
@php
    $tabs = [
        'site' => 'Site',
        'notes' => 'Notes',
        'schedule' => 'Schedule',
        'quotes' => 'Estimates',
        'photos' => 'Photos',
        'tasks' => 'Tasks',
        'crew' => 'Crew',
        'invoices' => 'Invoices',
        'changes' => 'Changes',
        'warranties' => 'Warranties',
        'costing' => 'Costing',
        'files' => 'Files',
    ];
    $tab = array_key_exists($tab ?? '', $tabs) ? $tab : 'site';
    $money = fn ($n) => '$'.number_format((float) $n, 2);
@endphp
@section('title', $job->number)
@section('meta', $job->title().' · '.$job->pipeline->name.' · '.$job->stage->name)
@section('actions')
    <a class="btn btn-ghost" href="{{ route('admin.schedule') }}">Calendar</a>
    <a class="btn" href="{{ route('admin.jobs.index', ['pipeline' => $job->pipeline->slug]) }}">Pipeline</a>
@endsection
@section('content')
<p>
    <span class="tag tag-{{ $job->status }}">{{ $job->status }}</span>
    · {{ $job->urgency }}
    @if($job->roof_type) · {{ str_replace('_', ' ', $job->roof_type) }}@endif
    @if($job->squares) · {{ $job->squares }} sq @endif
    @if($job->crew_name) · Crew: {{ $job->crew_name }}@endif
</p>
<div class="job-money">
    <div><span>Approved</span><b>{{ $money($job->approvedTotal()) }}</b></div>
    <div><span>Invoiced</span><b>{{ $money($job->invoicedTotal()) }}</b></div>
    <div><span>Paid</span><b>{{ $money($job->paidTotal()) }}</b></div>
    <div><span>Cost</span><b>{{ $money($job->costTotal()) }}</b></div>
    <div><span>Margin</span><b>{{ $job->margin() === null ? '—' : $money($job->margin()) }}</b></div>
</div>

<nav class="job-tabs">
    @foreach($tabs as $key => $label)
        <a class="{{ $tab === $key ? 'is-on' : '' }}" href="{{ route('admin.jobs.show', ['job' => $job, 'tab' => $key]) }}">{{ $label }}</a>
    @endforeach
</nav>

@if($tab === 'site')
<div class="grid job-split">
    <div class="panel">
        <h3>{{ $job->title() }}</h3>
        <p>{{ $job->address }} {{ $job->city }} {{ $job->zip }}</p>
        @if($job->lead)
            <p><a href="{{ route('admin.leads.show', $job->lead) }}">Open lead</a>
            @if($job->lead->phone) · <a href="tel:{{ $job->lead->phone }}">{{ $job->lead->phone }}</a>@endif
            · {{ $job->lead->email }}</p>
        @endif
        <form method="post" action="{{ route('admin.jobs.move', $job) }}">
            @csrf
            <label>Stage
                <select name="stage_id">
                    @foreach($job->pipeline->stages as $stage)
                        <option value="{{ $stage->id }}" @selected($stage->id === $job->stage_id)>{{ $stage->name }}</option>
                    @endforeach
                </select>
            </label>
            <button class="btn" type="submit">Move stage</button>
        </form>
        @if($job->stage->isReviewStage() && $job->lead)
            <form method="post" action="{{ route('admin.reviews.store') }}" style="margin-top:1rem">
                @csrf
                <input type="hidden" name="lead_id" value="{{ $job->lead_id }}">
                <input type="hidden" name="job_id" value="{{ $job->id }}">
                <label class="remember"><input type="checkbox" name="send_email" value="1" style="width:auto"> Email the customer</label>
                <p><button class="btn btn-ghost" type="submit">Ask for a Review Shield rating</button></p>
            </form>
        @endif
    </div>
    <div class="panel">
        <h3>Site and crew</h3>
        <form method="post" action="{{ route('admin.jobs.update', $job) }}">
            @csrf @method('PATCH')
            <label>Assign
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach($staff as $user)
                        <option value="{{ $user->id }}" @selected($job->assigned_to===$user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Status
                <select name="status">
                    @foreach(\App\Models\Job::STATUSES as $status)
                        <option value="{{ $status }}" @selected($job->status===$status)>{{ $status }}</option>
                    @endforeach
                </select>
            </label>
            <label>Payment
                <select name="payment_path">
                    @foreach(\App\Models\Job::PAYMENT_PATHS as $path)
                        <option value="{{ $path }}" @selected($job->payment_path===$path)>{{ str_replace('_', ' ', $path) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Urgency
                <select name="urgency">
                    <option value="standard" @selected($job->urgency==='standard')>Standard</option>
                    <option value="emergency" @selected($job->urgency==='emergency')>Emergency</option>
                </select>
            </label>
            <label>Roof
                <select name="roof_type">
                    <option value="">Unknown</option>
                    @foreach(\App\Models\Job::ROOF_TYPES as $type)
                        <option value="{{ $type }}" @selected($job->roof_type===$type)>{{ str_replace('_', ' ', $type) }}</option>
                    @endforeach
                </select>
            </label>
            <div class="job-inline">
                <label>Squares <input name="squares" type="number" step="0.01" value="{{ $job->squares }}"></label>
                <label>Stories <input name="stories" type="number" min="1" max="8" value="{{ $job->stories }}"></label>
                <label>Pitch <input name="pitch" value="{{ $job->pitch }}" placeholder="6/12"></label>
            </div>
            <label>Material system <input name="material_system" value="{{ $job->material_system }}"></label>
            <label>Insurance carrier <input name="insurance_carrier" value="{{ $job->insurance_carrier }}"></label>
            <label>Claim # <input name="claim_number" value="{{ $job->claim_number }}"></label>
            <label>HOA <input name="hoa_name" value="{{ $job->hoa_name }}"></label>
            <label>Crew <input name="crew_name" value="{{ $job->crew_name }}"></label>
            <label>Address <input name="address" value="{{ $job->address }}"></label>
            <label>City <input name="city" value="{{ $job->city }}"></label>
            <label>ZIP <input name="zip" value="{{ $job->zip }}"></label>
            <label>Scheduled <input type="datetime-local" name="scheduled_at" value="{{ optional($job->scheduled_at)?->format('Y-m-d\TH:i') }}"></label>
            <label>Access notes
                <textarea name="access_notes" rows="3">{{ $job->access_notes }}</textarea>
            </label>
            <label>Customer summary
                <textarea name="customer_summary" rows="4">{{ $job->customer_summary }}</textarea>
            </label>
            <button class="btn" type="submit">Save site</button>
        </form>
    </div>
</div>
@endif

@if($tab === 'notes')
<div class="panel">
    <h3>Job notes</h3>
    <form method="post" action="{{ route('admin.jobs.notes', $job) }}">
        @csrf
        <label>Note
            <textarea name="body" rows="4" required placeholder="What happened on site, what the homeowner said, what the crew needs"></textarea>
        </label>
        <label class="remember"><input type="checkbox" name="customer_visible" value="1" style="width:auto"> Show this note in the customer portal</label>
        <p><button class="btn" type="submit">Add note</button></p>
    </form>
    @foreach($job->events as $event)
        <p>
            <strong>{{ str_replace('_', ' ', $event->event) }}</strong>
            · {{ $event->user?->name }}
            · {{ $event->created_at->timezone(config('app.timezone'))->format('M j, g:ia') }}
            @if($event->customer_visible) · customer can see @endif
            <br>{{ $event->body }}
        </p>
    @endforeach
</div>
@endif

@if($tab === 'schedule')
<div class="grid job-split">
    <div class="panel">
        <h3>Book a visit</h3>
        <form method="post" action="{{ route('admin.jobs.appointments.store', $job) }}">
            @csrf
            <label>Type
                <select name="type">
                    @foreach(\App\Models\JobAppointment::TYPES as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Title <input name="title" placeholder="Optional, e.g. Meet adjuster"></label>
            <label>Starts <input type="datetime-local" name="starts_at" required></label>
            <label>Ends <input type="datetime-local" name="ends_at"></label>
            <label>Who
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach($staff as $user)
                        <option value="{{ $user->id }}" @selected($job->assigned_to===$user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Notes <textarea name="notes" rows="3"></textarea></label>
            <button class="btn" type="submit">Add to calendar</button>
        </form>
    </div>
    <div class="panel">
        <h3>On this job</h3>
        @forelse($job->appointments as $appointment)
            <div class="job-row">
                <div>
                    <strong>{{ $appointment->label() }}</strong>
                    <div class="page-meta">
                        {{ $appointment->starts_at->timezone(config('app.timezone'))->format('D M j, g:ia') }}
                        · {{ $appointment->status }}
                        @if($appointment->assignee) · {{ $appointment->assignee->name }}@endif
                    </div>
                    @if($appointment->notes)<p>{{ $appointment->notes }}</p>@endif
                </div>
                @if($appointment->status === 'scheduled')
                    <form method="post" action="{{ route('admin.jobs.appointments.update', [$job, $appointment]) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="done">
                        <button class="btn btn-ghost" type="submit">Done</button>
                    </form>
                    <form method="post" action="{{ route('admin.jobs.appointments.update', [$job, $appointment]) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="canceled">
                        <button class="btn btn-ghost" type="submit">Cancel</button>
                    </form>
                @endif
            </div>
        @empty
            <p>No visits booked yet.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'quotes')
<div class="grid job-split">
    <div class="panel">
        <h3>New estimate</h3>
        <form method="post" action="{{ route('admin.jobs.quotes.store', $job) }}">
            @csrf
            <label>Title <input name="title" required value="Roof replacement"></label>
            <label>Tax <input name="tax" type="number" step="0.01" value="0"></label>
            <label>Notes <textarea name="notes" rows="2"></textarea></label>
            <p class="page-meta">Line items</p>
            @foreach(range(1, 4) as $line)
                <div class="job-inline">
                    <label>Item <input name="item_label[]"></label>
                    <label>Qty <input name="item_qty[]" type="number" step="0.01" value="1"></label>
                    <label>Unit <input name="item_unit[]" value="sq"></label>
                    <label>Price <input name="item_price[]" type="number" step="0.01"></label>
                </div>
            @endforeach
            <button class="btn" type="submit">Save estimate</button>
        </form>
    </div>
    <div>
        @forelse($job->quotes as $quote)
            <div class="panel">
                <h3>{{ $quote->title }} <span class="tag">{{ $quote->status }}</span></h3>
                <table>
                    @foreach($quote->items as $item)
                        <tr>
                            <td>{{ $item->label }}</td>
                            <td>{{ $item->qty }} {{ $item->unit }}</td>
                            <td>{{ $money($item->unit_price) }}</td>
                            <td>{{ $money($item->amount) }}</td>
                        </tr>
                    @endforeach
                    <tr><td colspan="3">Tax</td><td>{{ $money($quote->tax) }}</td></tr>
                    <tr><th colspan="3">Total</th><th>{{ $money($quote->total) }}</th></tr>
                </table>
                @if($quote->status === 'draft')
                    <form method="post" action="{{ route('admin.jobs.quotes.items', [$job, $quote]) }}" class="job-inline" style="margin-top:0.8rem">
                        @csrf
                        <label>Add line <input name="label" required></label>
                        <label>Qty <input name="qty" type="number" step="0.01" value="1"></label>
                        <label>Unit <input name="unit" value="ea"></label>
                        <label>Price <input name="unit_price" type="number" step="0.01" required></label>
                        <button class="btn btn-ghost" type="submit">Add</button>
                    </form>
                @endif
                @if(in_array($quote->status, ['draft', 'sent'], true))
                    <div class="job-inline" style="margin-top:0.8rem">
                        @if($quote->status === 'draft')
                            <form method="post" action="{{ route('admin.jobs.quotes.status', [$job, $quote]) }}">
                                @csrf <input type="hidden" name="status" value="sent">
                                <button class="btn" type="submit">Send to customer</button>
                            </form>
                        @endif
                        <form method="post" action="{{ route('admin.jobs.quotes.status', [$job, $quote]) }}">
                            @csrf <input type="hidden" name="status" value="approved">
                            <button class="btn btn-ghost" type="submit">Mark approved</button>
                        </form>
                        <form method="post" action="{{ route('admin.jobs.quotes.status', [$job, $quote]) }}">
                            @csrf <input type="hidden" name="status" value="declined">
                            <button class="btn btn-ghost" type="submit">Decline</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="panel"><p>No estimates yet.</p></div>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'photos')
<div class="panel">
    <h3>Job photos</h3>
    <form method="post" action="{{ route('admin.jobs.documents', $job) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="category" value="photos">
        <label>Photo
            <input type="file" name="file" required accept=".jpg,.jpeg,.png,.heic,.webp">
        </label>
        <label>Visibility
            <select name="visibility">
                <option value="customer">Visible to customer</option>
                <option value="staff">Staff only</option>
            </select>
        </label>
        <button class="btn" type="submit">Upload</button>
    </form>
    <div class="job-photos">
        @forelse($job->photos() as $document)
            <a href="{{ route('admin.jobs.download', [$job, $document]) }}">{{ $document->original_name }}</a>
        @empty
            <p>No photos yet. Upload from the office or ask the customer in Files.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'tasks')
<div class="grid job-split">
    <div class="panel">
        <h3>Add task</h3>
        <form method="post" action="{{ route('admin.jobs.tasks.store', $job) }}">
            @csrf
            <label>Task <input name="title" required></label>
            <label>Due <input type="date" name="due_on"></label>
            <label>Assign
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach($staff as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </label>
            <button class="btn" type="submit">Add</button>
        </form>
    </div>
    <div class="panel">
        <h3>Checklist</h3>
        @forelse($job->tasks as $task)
            <form method="post" action="{{ route('admin.jobs.tasks.toggle', [$job, $task]) }}" class="job-row">
                @csrf
                <button class="btn btn-ghost" type="submit">{{ $task->is_done ? 'Undo' : 'Done' }}</button>
                <div>
                    <strong class="{{ $task->is_done ? 'is-done' : '' }}">{{ $task->title }}</strong>
                    <div class="page-meta">
                        @if($task->due_on) Due {{ $task->due_on->format('M j') }} · @endif
                        {{ $task->assignee?->name ?? 'Unassigned' }}
                    </div>
                </div>
            </form>
        @empty
            <p>No tasks on this job.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'crew')
<div class="grid job-split">
    <div class="panel">
        <h3>Materials</h3>
        <form method="post" action="{{ route('admin.jobs.materials.store', $job) }}">
            @csrf
            <label>Item <input name="name" required placeholder="GAF Timberline HDZ"></label>
            <div class="job-inline">
                <label>Qty <input name="qty" type="number" step="0.01" value="1"></label>
                <label>Unit <input name="unit" value="sq"></label>
                <label>Status
                    <select name="status">
                        @foreach(\App\Models\JobMaterial::STATUSES as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <label>Vendor <input name="vendor"></label>
            <label>Notes <textarea name="notes" rows="2"></textarea></label>
            <button class="btn" type="submit">Add material</button>
        </form>
    </div>
    <div class="panel">
        <h3>On order</h3>
        @forelse($job->materials as $material)
            <div class="job-row">
                <div>
                    <strong>{{ $material->name }}</strong>
                    <div class="page-meta">{{ $material->qty }} {{ $material->unit }} · {{ $material->status }}@if($material->vendor) · {{ $material->vendor }}@endif</div>
                </div>
                <form method="post" action="{{ route('admin.jobs.materials.status', [$job, $material]) }}">
                    @csrf
                    <select name="status" onchange="this.form.submit()">
                        @foreach(\App\Models\JobMaterial::STATUSES as $status)
                            <option value="{{ $status }}" @selected($material->status===$status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        @empty
            <p>No materials listed.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'invoices')
<div class="grid job-split">
    <div class="panel">
        <h3>New invoice</h3>
        <form method="post" action="{{ route('admin.jobs.invoices.store', $job) }}">
            @csrf
            <label>Kind
                <select name="kind">
                    @foreach(\App\Models\JobInvoice::KINDS as $kind)
                        <option value="{{ $kind }}">{{ ucfirst($kind) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Amount <input name="amount" type="number" step="0.01" required></label>
            <label>Due <input type="date" name="due_on"></label>
            <label>Notes <textarea name="notes" rows="2"></textarea></label>
            <button class="btn" type="submit">Create invoice</button>
        </form>
    </div>
    <div class="panel">
        <h3>Invoices</h3>
        @forelse($job->invoices as $invoice)
            <div class="job-row">
                <div>
                    <strong>{{ $invoice->number }}</strong>
                    <div class="page-meta">{{ $invoice->kind }} · {{ $money($invoice->amount) }} · {{ $invoice->status }}@if($invoice->due_on) · due {{ $invoice->due_on->format('M j') }}@endif</div>
                </div>
                @if($invoice->status !== 'void')
                    <form method="post" action="{{ route('admin.jobs.invoices.status', [$job, $invoice]) }}">
                        @csrf
                        <select name="status" onchange="this.form.submit()">
                            @foreach(\App\Models\JobInvoice::STATUSES as $status)
                                <option value="{{ $status }}" @selected($invoice->status===$status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        @empty
            <p>No invoices yet.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'changes')
<div class="grid job-split">
    <div class="panel">
        <h3>Change order</h3>
        <form method="post" action="{{ route('admin.jobs.changes.store', $job) }}">
            @csrf
            <label>Title <input name="title" required placeholder="Add decking"></label>
            <label>Amount <input name="amount" type="number" step="0.01" required></label>
            <label>Notes <textarea name="notes" rows="3"></textarea></label>
            <button class="btn" type="submit">Save change order</button>
        </form>
    </div>
    <div class="panel">
        <h3>On file</h3>
        @forelse($job->changeOrders as $order)
            <div class="job-row">
                <div>
                    <strong>{{ $order->title }}</strong>
                    <div class="page-meta">{{ $money($order->amount) }} · {{ $order->status }}</div>
                    @if($order->notes)<p>{{ $order->notes }}</p>@endif
                </div>
                @if(in_array($order->status, ['draft', 'sent'], true))
                    <form method="post" action="{{ route('admin.jobs.changes.status', [$job, $order]) }}">
                        @csrf
                        <select name="status" onchange="this.form.submit()">
                            @foreach(\App\Models\JobChangeOrder::STATUSES as $status)
                                <option value="{{ $status }}" @selected($order->status===$status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        @empty
            <p>No change orders.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'warranties')
<div class="grid job-split">
    <div class="panel">
        <h3>Record warranty</h3>
        <form method="post" action="{{ route('admin.jobs.warranties.store', $job) }}">
            @csrf
            <label>Kind
                <select name="kind">
                    <option value="workmanship">Workmanship</option>
                    <option value="manufacturer">Manufacturer</option>
                </select>
            </label>
            <label>Manufacturer <input name="manufacturer"></label>
            <label>Registration # <input name="registration"></label>
            <label>Starts <input type="date" name="starts_on"></label>
            <label>Expires <input type="date" name="expires_on"></label>
            <label>Notes <textarea name="notes" rows="2"></textarea></label>
            <button class="btn" type="submit">Save warranty</button>
        </form>
    </div>
    <div class="panel">
        <h3>On this roof</h3>
        @forelse($job->warranties as $warranty)
            <p>
                <strong>{{ ucfirst($warranty->kind) }}</strong>
                @if($warranty->manufacturer) · {{ $warranty->manufacturer }}@endif
                @if($warranty->registration) · #{{ $warranty->registration }}@endif
                <span class="page-meta">
                @if($warranty->starts_on){{ $warranty->starts_on->format('M j, Y') }}@endif
                @if($warranty->expires_on) – {{ $warranty->expires_on->format('M j, Y') }}@endif
                </span>
                @if($warranty->notes)<br>{{ $warranty->notes }}@endif
            </p>
        @empty
            <p>No warranties recorded.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'costing')
<div class="grid job-split">
    <div class="panel">
        <h3>Job cost</h3>
        <form method="post" action="{{ route('admin.jobs.costs.store', $job) }}">
            @csrf
            <label>Kind
                <select name="kind">
                    @foreach(\App\Models\JobCostLine::KINDS as $kind)
                        <option value="{{ $kind }}">{{ $kind === 'sub' ? 'Subcontractor' : ucfirst($kind) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Label <input name="label" required></label>
            <label>Amount <input name="amount" type="number" step="0.01" required></label>
            <label>Notes <textarea name="notes" rows="2"></textarea></label>
            <button class="btn" type="submit">Log cost</button>
        </form>
    </div>
    <div class="panel">
        <h3>Costs vs sold</h3>
        <p>Sold {{ $money($job->approvedTotal()) }} · Cost {{ $money($job->costTotal()) }} · Margin {{ $job->margin() === null ? '—' : $money($job->margin()) }}</p>
        @forelse($job->costLines as $line)
            <p><strong>{{ $line->label }}</strong> · {{ $line->kind }} · {{ $money($line->amount) }}</p>
        @empty
            <p>No costs logged.</p>
        @endforelse
    </div>
</div>
@endif

@if($tab === 'files')
<div class="grid job-split">
    <div>
        <div class="panel">
            <h3>Documents</h3>
            @foreach($job->documentRequests as $request)
                <p>
                    <strong>{{ $request->label }}</strong>
                    · {{ $request->isFulfilled() ? 'received' : 'waiting' }}
                    @if($request->required) · required @endif
                </p>
            @endforeach
            <form method="post" action="{{ route('admin.jobs.documents', $job) }}" enctype="multipart/form-data">
                @csrf
                <label>Upload
                    <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.heic,.webp">
                </label>
                <label>Attach to request
                    <select name="request_id">
                        <option value="">None</option>
                        @foreach($job->documentRequests as $request)
                            <option value="{{ $request->id }}">{{ $request->label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Visibility
                    <select name="visibility">
                        <option value="staff">Staff only</option>
                        <option value="customer">Visible to customer</option>
                    </select>
                </label>
                <button class="btn" type="submit">Upload</button>
            </form>
            <form method="post" action="{{ route('admin.jobs.requests', $job) }}" style="margin-top:1rem">
                @csrf
                <label>Request another file
                    <input name="label" required placeholder="e.g. HOA letter">
                </label>
                <input type="hidden" name="category" value="other">
                <label class="remember"><input type="checkbox" name="required" value="1" style="width:auto" checked> Required</label>
                <p><button class="btn btn-ghost" type="submit">Add request</button></p>
            </form>
            @foreach($job->documents as $document)
                <p>
                    <a href="{{ route('admin.jobs.download', [$job, $document]) }}">{{ $document->original_name }}</a>
                    · {{ $document->visibility }} · {{ $document->user?->name }}
                </p>
            @endforeach
        </div>
    </div>
    <div class="panel">
        <h3>Invite customer</h3>
        @foreach($job->contacts as $contact)
            <p>{{ $contact->user?->name }} · {{ $contact->user?->email }} · {{ $contact->role }}</p>
        @endforeach
        @foreach($job->invites as $invite)
            <p class="page-meta">{{ $invite->email }} · {{ $invite->accepted_at ? 'accepted' : 'expires '.$invite->expires_at->format('M j') }}</p>
        @endforeach
        <form method="post" action="{{ route('admin.jobs.invite', $job) }}">
            @csrf
            <label>Name <input name="name" value="{{ $job->lead?->name }}"></label>
            <label>Email <input type="email" name="email" required value="{{ $job->lead?->email }}"></label>
            <label>Role
                <select name="contact_role">
                    <option value="homeowner">Homeowner</option>
                    <option value="pm">Property manager</option>
                    <option value="other">Other</option>
                </select>
            </label>
            <button class="btn" type="submit">Email portal invite</button>
        </form>
    </div>
</div>
@endif
@endsection
