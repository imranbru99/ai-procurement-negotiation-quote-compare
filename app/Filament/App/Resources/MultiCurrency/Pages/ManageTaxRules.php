<?php

namespace App\Filament\App\Resources\MultiCurrency\Pages;

use App\Filament\App\Resources\MultiCurrency\TaxRuleResource;
use Filament\Resources\Pages\ManageRecords;

class ManageTaxRules extends ManageRecords
{
    protected static string $resource = TaxRuleResource::class;
}
