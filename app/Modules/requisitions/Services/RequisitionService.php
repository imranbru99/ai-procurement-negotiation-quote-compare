<?php

namespace App\Modules\requisitions\Services;

use App\Models\PurchaseRequisition;
use App\Models\Rfq;
use App\Models\RfqLine;
use App\Modules\requisitions\Events\RequisitionSubmitted;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RequisitionService
{
    public function assignNumber(PurchaseRequisition $requisition): void
    {
        if ($requisition->number) {
            return;
        }

        $sequence = PurchaseRequisition::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $requisition->tenant_id)
            ->count() + 1;

        $requisition->number = sprintf('PR-%d-%05d', $requisition->tenant_id, $sequence);
    }

    public function submit(PurchaseRequisition $requisition): PurchaseRequisition
    {
        if ($requisition->status !== PurchaseRequisition::STATUS_DRAFT) {
            throw new InvalidArgumentException('Only draft requisitions can be submitted.');
        }

        if ($requisition->lines()->count() === 0) {
            throw new InvalidArgumentException('Requisition must have at least one line.');
        }

        $this->assignNumber($requisition);

        $requisition->status = PurchaseRequisition::STATUS_SUBMITTED;
        $requisition->save();

        event(new RequisitionSubmitted($requisition));

        return $requisition->fresh();
    }

    public function convertToRfq(PurchaseRequisition $requisition, ?string $title = null): Rfq
    {
        if ($requisition->status !== PurchaseRequisition::STATUS_APPROVED) {
            throw new InvalidArgumentException('Only approved requisitions can be converted to RFQ.');
        }

        return DB::transaction(function () use ($requisition, $title): Rfq {
            $sequence = Rfq::query()
                ->withoutGlobalScopes()
                ->where('tenant_id', $requisition->tenant_id)
                ->count() + 1;

            $rfq = Rfq::query()->create([
                'tenant_id' => $requisition->tenant_id,
                'number' => sprintf('RFQ-%d-%05d', $requisition->tenant_id, $sequence),
                'title' => $title ?? 'RFQ from '.$requisition->number,
                'description' => $requisition->notes,
                'status' => 'draft',
                'type' => 'rfq',
                'requisition_id' => $requisition->id,
                'currency_code' => $requisition->currency_code,
            ]);

            foreach ($requisition->lines as $line) {
                RfqLine::query()->create([
                    'rfq_id' => $rfq->id,
                    'catalog_item_id' => $line->catalog_item_id,
                    'description' => $line->description,
                    'qty' => $line->qty,
                    'unit_id' => $line->unit_id,
                    'target_price' => $line->estimated_unit_price,
                ]);
            }

            $requisition->update(['status' => PurchaseRequisition::STATUS_CONVERTED]);

            return $rfq->load('lines');
        });
    }
}
