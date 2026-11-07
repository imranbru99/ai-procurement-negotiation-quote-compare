<?php

namespace App\Filament\App\Resources\PurchaseRequisitions;

use App\Filament\App\Resources\PurchaseRequisitions\Pages\CreatePurchaseRequisition;
use App\Filament\App\Resources\PurchaseRequisitions\Pages\EditPurchaseRequisition;
use App\Filament\App\Resources\PurchaseRequisitions\Pages\ListPurchaseRequisitions;
use App\Models\PurchaseRequisition;
use App\Support\Modules\ChecksModuleAccess;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseRequisitionResource extends Resource
{
    use ChecksModuleAccess;

    protected static ?string $model = PurchaseRequisition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Sourcing';

    protected static ?int $navigationSort = 10;

    public static function moduleKey(): string
    {
        return 'requisitions';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Header')
                ->schema([
                    TextInput::make('department'),
                    TextInput::make('cost_center'),
                    Select::make('urgency')
                        ->options([
                            'low' => 'Low',
                            'normal' => 'Normal',
                            'high' => 'High',
                            'critical' => 'Critical',
                        ])
                        ->default('normal')
                        ->required(),
                    DatePicker::make('needed_by'),
                    TextInput::make('budget_amount')->numeric(),
                    TextInput::make('currency_code')->default('USD')->maxLength(3),
                    Textarea::make('notes')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Lines')
                ->schema([
                    Repeater::make('lines')
                        ->relationship()
                        ->schema([
                            TextInput::make('description')->required(),
                            TextInput::make('qty')->numeric()->default(1)->required(),
                            TextInput::make('estimated_unit_price')->numeric(),
                        ])
                        ->defaultItems(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('urgency'),
                TextColumn::make('requester.name')->label('Requester'),
                TextColumn::make('needed_by')->date(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseRequisitions::route('/'),
            'create' => CreatePurchaseRequisition::route('/create'),
            'edit' => EditPurchaseRequisition::route('/{record}/edit'),
        ];
    }
}
