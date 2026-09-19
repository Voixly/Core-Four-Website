<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatWidgetController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_url' => ['nullable', 'string', 'max:255'],
            'audience' => ['nullable', 'in:residential,commercial'],
        ]);

        $token = $request->cookie('cfr_chat') ?: bin2hex(random_bytes(16));

        $conversation = Conversation::query()->firstOrCreate(
            ['visitor_token' => $token],
            [
                'page_url' => $data['page_url'] ?? $request->headers->get('referer'),
                'audience' => $data['audience'] ?? 'residential',
                'status' => 'open',
                'last_message_at' => now(),
            ]
        );

        if ($conversation->status === 'closed') {
            $conversation->update(['status' => 'open', 'last_message_at' => now()]);
        }

        if ($conversation->messages()->count() === 0) {
            $greeting = $conversation->audience === 'commercial'
                ? 'Welcome — Core Four handles commercial roofs across Houston. Is this a leak, a survey, or a replacement bid?'
                : 'Hey — Core Four here. Are you looking at a home leak, storm damage, or a full replacement?';

            $conversation->messages()->create([
                'sender' => 'bot',
                'body' => $greeting,
            ]);
        }

        return response()
            ->json($this->payload($conversation))
            ->cookie('cfr_chat', $token, 60 * 24 * 30);
    }

    public function poll(Request $request): JsonResponse
    {
        $conversation = $this->conversation($request);
        $after = (int) $request->query('after', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $after)
            ->orderBy('id')
            ->get();

        return response()->json([
            'conversation_id' => $conversation->id,
            'status' => $conversation->status,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $conversation = $this->conversation($request);

        if ($data['name'] ?? null) {
            $conversation->name = $data['name'];
        }
        if ($data['email'] ?? null) {
            $conversation->email = $data['email'];
        }
        if ($data['phone'] ?? null) {
            $conversation->phone = $data['phone'];
        }
        $conversation->last_message_at = now();
        $conversation->save();

        $conversation->messages()->create([
            'sender' => 'visitor',
            'body' => $data['body'],
        ]);

        $this->maybeBotReply($conversation, $data['body']);

        return response()->json($this->payload($conversation));
    }

    protected function conversation(Request $request): Conversation
    {
        $token = $request->cookie('cfr_chat');
        abort_unless($token, 404);

        return Conversation::query()->where('visitor_token', $token)->firstOrFail();
    }

    protected function payload(Conversation $conversation): array
    {
        $staffOnline = User::query()->where('is_active', true)->whereIn('role', ['agency', 'owner', 'staff'])->exists();

        return [
            'conversation_id' => $conversation->id,
            'status' => $conversation->status,
            'staff_online' => $staffOnline,
            'messages' => $conversation->messages()->orderBy('id')->get(),
        ];
    }

    protected function maybeBotReply(Conversation $conversation, string $body): void
    {
        $count = $conversation->messages()->where('sender', 'visitor')->count();
        if ($count > 3 || $conversation->assigned_to) {
            return;
        }

        $lower = strtolower($body);
        $reply = null;

        if ($count === 1) {
            $reply = 'Got it. What city or ZIP should we send a crew to?';
        } elseif ($count === 2) {
            $reply = 'Thanks. Want us to call you, or leave a number and we will call you back? Office line is (281) 541-0027.';
        } elseif (preg_match('/\d{3}/', $body) || str_contains($lower, 'call')) {
            $reply = 'We will have someone from the Tomball office reach out. If it is leaking now, call (281) 541-0027 for 24/7 help.';
        }

        if ($reply) {
            $conversation->messages()->create([
                'sender' => 'bot',
                'body' => $reply,
            ]);
        }
    }
}
