<?php

namespace App\Filament\App\Resources\Quotes;

use App\Filament\App\Resources\Quotes\Pages\EditQuote;
use App\Filament\App\Resources\Quotes\Pages\ListQuotes;
use App\Models\Quote;
use App\Modules\quotes\Imports\QuoteLinesImport;
use App\Modules\quotes\Services\QuoteService;
use App\Support\Modules\ChecksModuleAccess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class QuoteResource extends Resource
{
    use ChecksModuleAccess;

    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Sourcing';

    protected static ?int $navigationSort = 30;

    public static function moduleKey(): string
    {
        return 'quotes';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Quote')
                ->schema([
                    TextInput::make('number')->disabled(),
                    TextInput::make('status')->disabled(),
                    TextInput::make('currency_code')->maxLength(3),
                    TextInput::make('shipping_total')->numeric(),
                    TextInput::make('other_fees')->numeric(),
                    TextInput::make('grand_total')->disabled(),
                ])
                ->columns(2),
            Section::make('Lines')
                ->schema([
                    Repeater::make('lines')
                        ->relationship()
                        ->schema([
                            TextInput::make('description')->required(),
                            TextInput::make('qty')->numeric(),
                            TextInput::make('unit_price')->numeric(),
                            TextInput::make('tax_rate')->numeric(),
                            TextInput::make('line_total')->disabled(),
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable(),
                TextColumn::make('supplier.name'),
                TextColumn::make('status')->badge(),
                TextColumn::make('grand_total')->money(fn (Quote $record): string => $record->currency_code),
            ])
            ->recordActions([
                Action::make('importExcel')
                    ->form([
                        FileUpload::make('file')->required()->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ]),
                    ])
                    ->action(function (Quote $record, array $data, QuoteService $service): void {
                        $path = storage_path('app/public/'.$data['file']);
                        Excel::import(new QuoteLinesImport($record, $service), $path);
                        $service->normalizeTotals($record);
                        Notification::make()->title('Lines imported')->success()->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotes::route('/'),
            'edit' => EditQuote::route('/{record}/edit'),
        ];
    }
}
