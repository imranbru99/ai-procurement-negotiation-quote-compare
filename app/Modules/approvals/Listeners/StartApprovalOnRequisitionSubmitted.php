<?php

namespace App\Modules\approvals\Listeners;

use App\Facades\Module;
use App\Modules\approvals\Services\ApprovalEngine;
use App\Modules\requisitions\Events\RequisitionSubmitted;

class StartApprovalOnRequisitionSubmitted
{
    public function __construct(protected ApprovalEngine $engine) {}

    public function handle(RequisitionSubmitted $event): void
    {
        if (! Module::enabled('approvals', $event->requisition->tenant_id)) {
            return;
        }

        if ($this->engine->findWorkflowFor($event->requisition)) {
            $this->engine->start($event->requisition);
        }
    }
}
