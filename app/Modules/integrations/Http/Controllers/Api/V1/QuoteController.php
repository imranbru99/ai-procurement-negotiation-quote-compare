<?php

namespace App\Modules\integrations\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Modules\integrations\Http\Resources\QuoteApiResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuoteController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return QuoteApiResource::collection(
            Quote::query()->orderByDesc('id')->paginate(25)
        );
    }

    public function show(Quote $quote): QuoteApiResource
    {
        return new QuoteApiResource($quote);
    }
}
