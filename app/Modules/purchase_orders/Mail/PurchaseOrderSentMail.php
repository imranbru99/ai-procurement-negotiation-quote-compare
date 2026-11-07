<?php

namespace App\Modules\purchase_orders\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderSentMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public PurchaseOrder $purchaseOrder,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Purchase Order '.$this->purchaseOrder->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.purchase-order-sent',
            with: [
                'purchaseOrder' => $this->purchaseOrder,
            ],
        );
    }
}
