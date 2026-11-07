<?php

namespace App\Modules\supplier_risk\Services;

use App\Models\Supplier;
use App\Models\SupplierRiskAssessment;
use App\Modules\suppliers\Models\Supplier as SupplierModel;
use App\Services\Ai\AiService;
use App\Services\Tenancy\TenantContext;
use Illuminate\Support\Carbon;

class RiskService
{
    /** @var list<string> */
    protected array $lowRiskCountries = ['US', 'GB', 'DE', 'CA', 'AU', 'FR', 'NL'];

    public function __construct(
        protected AiService $ai,
    ) {}

    public function assess(Supplier|int $supplier): SupplierRiskAssessment
    {
        $supplierModel = $supplier instanceof Supplier
            ? $supplier
            : SupplierModel::query()->with('documents')->findOrFail($supplier);

        $financialFlag = blank($supplierModel->bank_iban) || blank($supplierModel->tax_id);
        $country = strtoupper((string) $supplierModel->country);
        $geoFlag = $country !== '' && ! in_array($country, $this->lowRiskCountries, true);
        $certifications = $supplierModel->certifications ?? [];
        $complianceFlag = $certifications === [];

        $expiryFlags = [];
        foreach ($supplierModel->documents ?? [] as $document) {
            $expires = $document->expires_at ?? null;
            if ($expires && Carbon::parse($expires)->lte(now()->addDays(30))) {
                $expiryFlags[] = [
                    'document_id' => $document->id,
                    'name' => $document->name ?? $document->type,
                    'expires_at' => (string) $expires,
                ];
            }
        }

        $baseScore = 35
            + ($financialFlag ? 20 : 0)
            + ($geoFlag ? 15 : 0)
            + ($complianceFlag ? 15 : 0)
            + min(15, count($expiryFlags) * 5);

        $baseScore = min(100, max(1, $baseScore));

        $aiReasoning = $this->aiReasoning($supplierModel, [
            'financial_flag' => $financialFlag,
            'geo_flag' => $geoFlag,
            'compliance_flag' => $complianceFlag,
            'document_expiry_flags' => $expiryFlags,
            'risk_score' => $baseScore,
        ]);

        return SupplierRiskAssessment::query()->create([
            'tenant_id' => TenantContext::getId() ?? $supplierModel->tenant_id,
            'supplier_id' => $supplierModel->id,
            'risk_score' => $baseScore,
            'financial_flag' => $financialFlag,
            'geo_flag' => $geoFlag,
            'compliance_flag' => $complianceFlag,
            'sanctions_notes' => $geoFlag ? 'Review sanctions exposure for supplier country.' : null,
            'document_expiry_flags' => $expiryFlags,
            'ai_reasoning' => $aiReasoning,
            'assessed_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function aiReasoning(SupplierModel $supplier, array $context): string
    {
        try {
            $response = $this->ai->runPromptTemplate('supplier_risk', [
                'supplier_name' => $supplier->name,
                'country' => $supplier->country ?? 'unknown',
                'context_json' => json_encode($context, JSON_THROW_ON_ERROR),
            ], 'supplier_risk');

            return $response->content;
        } catch (\Throwable) {
            return json_encode([
                'summary' => 'Demo risk assessment — verify financials, geography, and compliance manually.',
                'risk_score' => $context['risk_score'] ?? 50,
            ], JSON_THROW_ON_ERROR);
        }
    }
}
