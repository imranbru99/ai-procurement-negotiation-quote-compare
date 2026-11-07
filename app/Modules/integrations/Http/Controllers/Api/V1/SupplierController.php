<?php

namespace App\Modules\integrations\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\integrations\Http\Resources\SupplierApiResource;
use App\Modules\suppliers\Models\Supplier;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SupplierApiResource::collection(
            Supplier::query()->orderBy('name')->paginate(25)
        );
    }

    public function show(Supplier $supplier): SupplierApiResource
    {
        return new SupplierApiResource($supplier);
    }
}
