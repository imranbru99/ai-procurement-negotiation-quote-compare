<?php

namespace App\Modules\integrations\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Modules\integrations\Http\Resources\RfqApiResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RfqController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RfqApiResource::collection(
            Rfq::query()->orderByDesc('id')->paginate(25)
        );
    }

    public function show(Rfq $rfq): RfqApiResource
    {
        return new RfqApiResource($rfq);
    }
}
