<?php

namespace App\Filament\Supplier\Resources\Quotes;

use App\Filament\Supplier\Resources\Quotes\Pages\CreateQuote;
use App\Filament\Supplier\Resources\Quotes\Pages\EditQuote;
use App\Filament\Supplier\Resources\Quotes\Pages\ListQuotes;
use App\Models\Quote;
use App\Models\Rfq;
use App\Models\RfqInvitation;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        $supplierId = Auth::user()?->supplier_id;

        return parent::getEloquentQuery()
            ->when($supplierId, fn (Builder $query) => $query->where('supplier_id', $supplierId));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Quote')
                ->schema([
                    Select::make('rfq_id')
                        ->relationship('rfq', 'title')
                        ->required()
                        ->options(function (): array {
                            $supplierId = Auth::user()?->supplier_id;

                            if (! $supplierId) {
                                return [];
                            }

                            $rfqIds = RfqInvitation::query()
                                ->where('supplier_id', $supplierId)
                                ->pluck('rfq_id');

                            return Rfq::query()->whereIn('id', $rfqIds)->pluck('title', 'id')->all();
                        }),
                    TextInput::make('currency_code')->default('USD')->maxLength(3),
                    TextInput::make('shipping_total')->numeric()->default(0),
                ]),
            Section::make('Lines')
                ->schema([
                    Repeater::make('lines')
                        ->relationship()
                        ->schema([
                            TextInput::make('description')->required(),
                            TextInput::make('qty')->numeric()->default(1),
                            TextInput::make('unit_price')->numeric()->default(0),
                            TextInput::make('tax_rate')->numeric()->default(0),
                        ])
                        ->defaultItems(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number'),
                TextColumn::make('rfq.number'),
                TextColumn::make('status')->badge(),
                TextColumn::make('grand_total'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotes::route('/'),
            'create' => CreateQuote::route('/create'),
            'edit' => EditQuote::route('/{record}/edit'),
        ];
    }
}
