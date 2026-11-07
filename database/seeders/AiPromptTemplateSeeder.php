<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiPromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'quote_extraction',
                'name' => 'Quote extraction',
                'system_prompt' => 'You extract structured procurement quote lines from unstructured text.',
                'user_prompt_template' => "Extract line items from:\n\n{raw_text}",
            ],
            [
                'key' => 'quote_analysis',
                'name' => 'Quote analysis',
                'system_prompt' => 'You are a procurement analyst. Flag hidden costs, outliers, and unit mismatches.',
                'user_prompt_template' => 'Analyze this comparison JSON: {comparison_json}',
            ],
            [
                'key' => 'negotiation_strategy',
                'name' => 'Negotiation strategy',
                'system_prompt' => 'You recommend BATNA, walk-away, and concession plans. Never auto-award.',
                'user_prompt_template' => 'RFQ {rfq_id} supplier {supplier_id}: produce JSON strategy.',
            ],
            [
                'key' => 'counter_offer',
                'name' => 'Counter offer draft',
                'system_prompt' => 'Draft professional counter-offers. Locale: {locale}.',
                'user_prompt_template' => 'Draft a counter-offer message for RFQ {rfq_id} supplier {supplier_id}.',
            ],
            [
                'key' => 'negotiation_simulator',
                'name' => 'Negotiation simulator',
                'system_prompt' => 'Estimate likely outcomes with confidence intervals. Label as AI estimate.',
                'user_prompt_template' => 'Simulate scenario: {scenario_json}',
            ],
            [
                'key' => 'copilot_chat',
                'name' => 'Procurement copilot',
                'system_prompt' => 'Answer using only supplied tenant context. Respect permissions.',
                'user_prompt_template' => "Question: {question}\n\nContext: {context_json}",
            ],
            [
                'key' => 'supplier_risk',
                'name' => 'Supplier risk assessment',
                'system_prompt' => 'Assess supplier risk from structured context. Respond with concise JSON.',
                'user_prompt_template' => 'Supplier {supplier_name} ({country}). Context: {context_json}',
            ],
        ];

        foreach ($templates as $template) {
            AiPromptTemplate::query()->updateOrCreate(
                ['tenant_id' => null, 'key' => $template['key']],
                $template,
            );
        }
    }
}
