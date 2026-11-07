<?php

namespace App\Modules\contracts\Services;

use App\Models\Contract;
use App\Models\ContractClause;
use App\Models\ContractVersion;
use App\Services\Ai\AiService;

class ContractService
{
    public function __construct(
        protected AiService $ai,
    ) {}

    public function createVersion(Contract $contract, ?string $body, ?int $userId = null): ContractVersion
    {
        $next = ($contract->versions()->max('version') ?? 0) + 1;

        return ContractVersion::query()->create([
            'contract_id' => $contract->id,
            'version' => $next,
            'body' => $body,
            'created_by' => $userId,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function reviewClauseRisk(ContractClause $clause): array
    {
        $review = $this->ai->reviewClauseRisk($clause->body);

        if (isset($review['risk_level'])) {
            $clause->update(['risk_level' => $review['risk_level']]);
        }

        return $review;
    }
}
