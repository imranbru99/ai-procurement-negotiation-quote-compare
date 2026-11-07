<?php

namespace App\Modules\integrations\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Modules\integrations\Http\Resources\PurchaseOrderApiResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseOrderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PurchaseOrderApiResource::collection(
            PurchaseOrder::query()->orderByDesc('id')->paginate(25)
        );
    }

    public function show(PurchaseOrder $purchaseOrder): PurchaseOrderApiResource
    {
        return new PurchaseOrderApiResource($purchaseOrder);
    }
}
