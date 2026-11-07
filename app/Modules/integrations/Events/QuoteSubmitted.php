<?php

namespace App\Modules\integrations\Events;

use App\Models\Quote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Quote $quote) {}
}
