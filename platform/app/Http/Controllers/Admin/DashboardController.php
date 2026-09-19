<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\EmailSend;
use App\Models\Lead;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newLeads' => Lead::query()->where('status', 'new')->count(),
            'openChats' => Conversation::query()->where('status', 'open')->count(),
            'inspections' => Lead::query()->where('status', 'inspected')->whereDate('updated_at', today())->count(),
            'mailHealth' => [
                'scheduled' => EmailSend::query()->where('status', 'scheduled')->count(),
                'sent' => EmailSend::query()->where('status', 'sent')->whereDate('sent_at', today())->count(),
                'failed' => EmailSend::query()->where('status', 'failed')->count(),
            ],
            'recentLeads' => Lead::query()->latest()->limit(8)->get(),
        ]);
    }
}
