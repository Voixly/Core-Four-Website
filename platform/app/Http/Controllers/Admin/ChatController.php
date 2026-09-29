<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\EmailThreadService;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $channel = $request->query('channel');
        if (! in_array($channel, ['email', 'chat'], true)) {
            $channel = null;
        }

        $counts = [
            'all' => Conversation::query()->count(),
            'email' => Conversation::query()->where('channel', 'email')->count(),
            'chat' => Conversation::query()->where('channel', 'chat')->count(),
            'waiting' => Conversation::query()->where('channel', 'email')->where('awaiting_staff', true)->count(),
        ];

        $conversations = Conversation::query()
            ->withCount('messages')
            ->when($channel, fn ($query) => $query->where('channel', $channel))
            ->orderByDesc('awaiting_staff')
            ->orderByDesc('last_message_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.chat.index', compact('conversations', 'channel', 'counts'));
    }

    public function show(Conversation $conversation): View
    {
        $conversation->load(['messages' => fn ($query) => $query->orderBy('id'), 'lead']);

        $canned = [
            'Thanks for reaching out — I can have an estimator call you. What ZIP are we looking at?',
            'If it is leaking now, call (281) 541-0027 for a same-day tarp.',
            'We can do a free home inspection this week. What is the best number to reach you?',
            'For commercial we start with a roof survey — nights and weekends are available so tenants stay.',
        ];

        return view('admin.chat.show', compact('conversation', 'canned'));
    }

    public function reply(Request $request, Conversation $conversation, EmailThreadService $threads): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:4000']]);

        if ($conversation->isEmail()) {
            try {
                $threads->sendStaffReply($conversation, $request->user(), $data['body']);
            } catch (\Throwable) {
                return back()->withInput()->with('error', 'That reply was not emailed. Confirm Resend can send from hello@corefourroofing.com, then try again.');
            }

            return back();
        }

        $conversation->update([
            'assigned_to' => $request->user()->id,
            'last_message_at' => now(),
            'status' => 'open',
        ]);

        $conversation->messages()->create([
            'sender' => 'staff',
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return back();
    }

    public function close(Conversation $conversation): RedirectResponse
    {
        $conversation->update(['status' => 'closed', 'awaiting_staff' => false]);

        return back()->with('success', 'Chat closed.');
    }

    public function convert(Request $request, Conversation $conversation, LeadService $leads): RedirectResponse
    {
        if ($conversation->lead_id) {
            return redirect()->route('admin.leads.show', $conversation->lead_id);
        }

        $conversation->loadMissing('messages');
        $transcript = $conversation->messages->map(fn ($m) => strtoupper($m->sender).': '.$m->body)->implode("\n");

        $lead = $leads->capture([
            'name' => $conversation->name ?: 'Chat visitor',
            'email' => $conversation->email,
            'phone' => $conversation->phone,
            'type' => $conversation->audience ?: 'residential',
            'source' => 'chat',
            'page_url' => $conversation->page_url,
            'notes' => $transcript,
        ], $request->user());

        $conversation->update(['lead_id' => $lead->id, 'status' => 'closed']);

        return redirect()->route('admin.leads.show', $lead)->with('success', 'Chat converted to lead.');
    }

    public function poll(Conversation $conversation): JsonResponse
    {
        return response()->json([
            'messages' => $conversation->messages()->orderBy('id')->get(),
            'status' => $conversation->status,
        ]);
    }
}
