<?php

namespace App\Modules\rfq\Services;

use App\Models\Rfq;
use App\Models\RfqInvitation;
use App\Modules\suppliers\Models\Supplier;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use InvalidArgumentException;

class RfqService
{
    public function publish(Rfq $rfq): Rfq
    {
        if ($rfq->status !== 'draft') {
            throw new InvalidArgumentException('Only draft RFQs can be published.');
        }

        if ($rfq->lines()->count() === 0) {
            throw new InvalidArgumentException('RFQ must have at least one line.');
        }

        $rfq->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return $rfq->fresh();
    }

    public function invite(Rfq $rfq, Supplier $supplier): RfqInvitation
    {
        if ($rfq->status !== 'published') {
            throw new InvalidArgumentException('RFQ must be published before inviting suppliers.');
        }

        return RfqInvitation::query()->updateOrCreate(
            [
                'rfq_id' => $rfq->id,
                'supplier_id' => $supplier->id,
            ],
            [
                'token' => Str::random(48),
                'status' => 'invited',
                'invited_at' => now(),
            ]
        );
    }

    public function signedInvitationUrl(RfqInvitation $invitation): string
    {
        return URL::signedRoute('rfq.invitation.show', ['invitation' => $invitation->id]);
    }

    public function generatePdf(Rfq $rfq)
    {
        return UnicodePdf::loadView('rfq.pdf', ['rfq' => $rfq]);
    }
}
