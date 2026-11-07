<?php

namespace App\Modules\ai_negotiation\Services;

use App\Jobs\SendNegotiationRoundMail;
use App\Models\NegotiationRound;
use App\Models\NegotiationThread;
use App\Services\Ai\AiService;
use Illuminate\Support\Facades\Auth;

class CounterOfferGenerator
{
    public function __construct(
        protected AiService $ai,
    ) {}

    public function draft(
        NegotiationThread $thread,
        array $context,
        string $locale = 'en',
    ): NegotiationRound {
        $response = $this->ai->runPromptTemplate('counter_offer', array_merge($context, [
            'locale' => $locale,
            'rfq_id' => $thread->rfq_id,
            'supplier_id' => $thread->supplier_id,
        ]), 'counter_offer');

        $roundNumber = (int) $thread->rounds()->max('round_number') + 1;

        return NegotiationRound::query()->create([
            'thread_id' => $thread->id,
            'round_number' => $roundNumber,
            'direction' => 'outbound',
            'offer_payload' => $context,
            'message_body' => $response->content,
            'channel' => 'email',
            'created_by' => Auth::id(),
            'ai_generated' => true,
            'status' => 'draft',
        ]);
    }

    /**
     * Human-confirmed send only.
     */
    public function confirmAndQueueSend(NegotiationRound $round, string $recipientEmail): NegotiationRound
    {
        abort_if($round->status === 'sent', 422, 'Round already sent.');

        $round->update(['status' => 'queued']);
        SendNegotiationRoundMail::dispatch($round->id, $recipientEmail);

        return $round->fresh();
    }
}
