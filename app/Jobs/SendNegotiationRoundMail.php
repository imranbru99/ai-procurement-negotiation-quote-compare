<?php

namespace App\Jobs;

use App\Models\NegotiationRound;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNegotiationRoundMail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $roundId,
        public string $recipientEmail,
    ) {}

    public function handle(): void
    {
        $round = NegotiationRound::query()->findOrFail($this->roundId);

        Mail::raw(
            (string) $round->message_body,
            function ($message) use ($round): void {
                $message->to($this->recipientEmail)
                    ->subject('Counter-offer — round '.$round->round_number);
            }
        );

        $round->update(['status' => 'sent', 'channel' => 'email']);
    }
}
