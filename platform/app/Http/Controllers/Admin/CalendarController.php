<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobAppointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request): View
    {
        $start = $request->filled('week')
            ? Carbon::parse($request->query('week'), config('app.timezone'))->startOfWeek(Carbon::MONDAY)
            : now(config('app.timezone'))->startOfWeek(Carbon::MONDAY);
        $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

        $appointments = JobAppointment::query()
            ->with(['job.lead', 'assignee'])
            ->where('status', '!=', 'canceled')
            ->whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (JobAppointment $row) => $row->starts_at->timezone(config('app.timezone'))->toDateString());

        $days = collect(range(0, 6))->map(fn (int $i) => $start->copy()->addDays($i));

        return view('admin.schedule.index', [
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'appointments' => $appointments,
            'prev' => $start->copy()->subWeek()->toDateString(),
            'next' => $start->copy()->addWeek()->toDateString(),
        ]);
    }
}
