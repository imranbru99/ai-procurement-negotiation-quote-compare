<?php

namespace App\Filament\App\Resources\SupplierRisk;

use App\Filament\App\Resources\SupplierRisk\Pages\ListSupplierRiskAssessments;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\Supplier;
use App\Models\SupplierRiskAssessment;
use App\Modules\supplier_risk\Services\RiskService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierRiskAssessmentResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = SupplierRiskAssessment::class;

    protected static ?string $navigationLabel = 'Supplier risk';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static string|\UnitEnum|null $navigationGroup = 'Intelligence';

    protected static ?int $navigationSort = 30;

    protected static function moduleKey(): string
    {
        return 'supplier_risk';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.name'),
                TextColumn::make('risk_score')->sortable(),
                IconColumn::make('financial_flag')->boolean()->label('Financial'),
                IconColumn::make('geo_flag')->boolean()->label('Geo'),
                IconColumn::make('compliance_flag')->boolean()->label('Compliance'),
                TextColumn::make('assessed_at')->dateTime(),
            ])
            ->headerActions([
                Action::make('assess')
                    ->label('Run assessment')
                    ->form([
                        Select::make('supplier_id')
                            ->options(fn (): array => Supplier::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, RiskService $risk): void {
                        $risk->assess((int) $data['supplier_id']);
                        Notification::make()->title('Risk assessment saved')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplierRiskAssessments::route('/'),
        ];
    }
}
