<?php

namespace App\Filament\App\Resources\SupplierPerformance;

use App\Filament\App\Resources\SupplierPerformance\Pages\ListSupplierScorecards;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\Supplier;
use App\Models\SupplierScorecard;
use App\Modules\supplier_performance\Services\ScorecardService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class SupplierScorecardResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = SupplierScorecard::class;

    protected static ?string $navigationLabel = 'Supplier scorecards';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|\UnitEnum|null $navigationGroup = 'Intelligence';

    protected static ?int $navigationSort = 20;

    protected static function moduleKey(): string
    {
        return 'supplier_performance';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.name')->searchable(),
                TextColumn::make('period_start')->date(),
                TextColumn::make('period_end')->date(),
                TextColumn::make('overall')->sortable(),
                TextColumn::make('on_time_score')->label('On-time'),
                TextColumn::make('quality_score')->label('Quality'),
            ])
            ->headerActions([
                Action::make('compute')
                    ->label('Compute scorecard')
                    ->form([
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(fn (): array => Supplier::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->required(),
                        DatePicker::make('period_start')->required()->default(now()->subQuarter()),
                        DatePicker::make('period_end')->required()->default(now()),
                    ])
                    ->action(function (array $data, ScorecardService $scorecards): void {
                        $scorecards->compute(
                            (int) $data['supplier_id'],
                            Carbon::parse($data['period_start']),
                            Carbon::parse($data['period_end']),
                        );
                        Notification::make()->title('Scorecard computed')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplierScorecards::route('/'),
        ];
    }
}
