<?php

namespace App\Filament\App\Pages;

use App\Facades\Module;
use App\Models\ThreeWayMatch;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ThreeWayMatchExceptions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|\UnitEnum|null $navigationGroup = 'Procure-to-pay';

    protected static ?string $navigationLabel = '3-Way Match Exceptions';

    protected string $view = 'filament.app.pages.three-way-match-exceptions';

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled('invoices');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ThreeWayMatch::query()->where('status', 'exception'))
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('po_id')->label('PO'),
                TextColumn::make('variance_amount'),
                TextColumn::make('exception_reason')->limit(50),
            ]);
    }
}
