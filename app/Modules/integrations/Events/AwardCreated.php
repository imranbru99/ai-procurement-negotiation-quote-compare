<?php

namespace App\Modules\integrations\Events;

use App\Models\Award;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AwardCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Award $award) {}
}
