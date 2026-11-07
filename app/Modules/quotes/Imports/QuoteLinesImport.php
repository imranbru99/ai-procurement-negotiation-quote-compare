<?php

namespace App\Modules\quotes\Imports;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Modules\quotes\Services\QuoteService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuoteLinesImport implements ToModel, WithHeadingRow
{
    public function __construct(
        protected Quote $quote,
        protected QuoteService $quoteService,
    ) {}

    public function model(array $row): ?QuoteLine
    {
        if (empty($row['description'])) {
            return null;
        }

        $line = QuoteLine::query()->create([
            'quote_id' => $this->quote->id,
            'description' => (string) $row['description'],
            'qty' => $row['qty'] ?? 1,
            'unit_price' => $row['unit_price'] ?? 0,
            'tax_rate' => $row['tax_rate'] ?? 0,
        ]);

        return $this->quoteService->recalculateLine($line);
    }
}
