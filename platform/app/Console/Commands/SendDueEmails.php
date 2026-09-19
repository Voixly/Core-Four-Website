<?php

namespace App\Console\Commands;

use App\Mail\NurtureMail;
use App\Models\EmailSend;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDueEmails extends Command
{
    protected $signature = 'email:send-due';

    protected $description = 'Send scheduled nurture emails that are due';

    public function handle(): int
    {
        $sends = EmailSend::query()
            ->with(['step', 'lead'])
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->limit(100)
            ->get();

        foreach ($sends as $send) {
            if (! $send->lead?->email || ! $send->step?->is_active) {
                $send->update(['status' => 'skipped']);
                continue;
            }

            try {
                Mail::to($send->lead->email)->send(new NurtureMail($send->step, $send->lead));
                $send->update(['status' => 'sent', 'sent_at' => now(), 'error' => null]);
            } catch (\Throwable $e) {
                $send->update(['status' => 'failed', 'error' => $e->getMessage()]);
            }
        }

        $this->info('Processed '.$sends->count().' sends.');

        return self::SUCCESS;
    }
}
