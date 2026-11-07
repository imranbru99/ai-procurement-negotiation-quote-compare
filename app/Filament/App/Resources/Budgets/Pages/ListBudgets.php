<?php

namespace App\Filament\App\Resources\Budgets\Pages;

use App\Filament\App\Resources\Budgets\BudgetsResource;
use Filament\Resources\Pages\ListRecords;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetsResource::class;
}
