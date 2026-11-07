<?php

namespace App\Filament\App\Resources\Contracts\Pages;

use App\Filament\App\Resources\Contracts\ContractsResource;
use Filament\Resources\Pages\ListRecords;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractsResource::class;
}
