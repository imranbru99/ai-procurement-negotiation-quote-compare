<?php

namespace App\Modules\requisitions\Events;

use App\Models\PurchaseRequisition;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequisitionSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public PurchaseRequisition $requisition) {}
}
