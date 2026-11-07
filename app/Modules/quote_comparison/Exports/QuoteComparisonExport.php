<?php

namespace App\Modules\quote_comparison\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuoteComparisonExport implements FromArray, WithHeadings
{
    /**
     * @param  array<string, mixed>  $results
     */
    public function __construct(
        protected array $results,
    ) {}

    public function headings(): array
    {
        return ['Quote ID', 'Supplier', 'TCO (base)', 'Weighted score', 'Outlier'];
    }

    public function array(): array
    {
        return collect($this->results['quotes'] ?? [])->map(fn (array $row): array => [
            $row['quote_id'] ?? null,
            $row['supplier_name'] ?? null,
            $row['tco_base'] ?? null,
            $row['weighted_score'] ?? null,
            ($row['outliers']['is_outlier'] ?? false) ? 'yes' : 'no',
        ])->all();
    }
}
