<?php

namespace App\Filament\App\Resources\Rfqs;

use App\Filament\App\Resources\Rfqs\Pages\EditRfq;
use App\Filament\App\Resources\Rfqs\Pages\ListRfqs;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Modules\rfq\Services\RfqService;
use App\Support\Modules\ChecksModuleAccess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RfqResource extends Resource
{
    use ChecksModuleAccess;

    protected static ?string $model = Rfq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Sourcing';

    protected static ?int $navigationSort = 20;

    public static function moduleKey(): string
    {
        return 'rfq';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('RFQ')
                ->schema([
                    TextInput::make('number')->disabled()->dehydrated(false),
                    TextInput::make('title')->required(),
                    Textarea::make('description')->columnSpanFull(),
                    Select::make('type')->options(['rfq' => 'RFQ', 'rfp' => 'RFP', 'rfi' => 'RFI'])->default('rfq'),
                    Select::make('status')->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                        'awarded' => 'Awarded',
                        'cancelled' => 'Cancelled',
                    ])->disabled(),
                    DateTimePicker::make('deadline_at'),
                    TextInput::make('currency_code')->default('USD')->maxLength(3),
                    Textarea::make('terms')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Lines')
                ->schema([
                    Repeater::make('lines')
                        ->relationship()
                        ->schema([
                            TextInput::make('description')->required(),
                            TextInput::make('qty')->numeric()->default(1),
                            TextInput::make('target_price')->numeric(),
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
                TextColumn::make('title')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('deadline_at')->dateTime(),
            ])
            ->recordActions([
                Action::make('publish')
                    ->visible(fn (Rfq $record): bool => $record->status === 'draft')
                    ->action(function (Rfq $record, RfqService $service): void {
                        $service->publish($record);
                        Notification::make()->title('RFQ published')->success()->send();
                    }),
                Action::make('invite')
                    ->visible(fn (Rfq $record): bool => $record->status === 'published')
                    ->form([
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(fn (): array => Supplier::query()->pluck('name', 'id')->all())
                            ->required(),
                    ])
                    ->action(function (Rfq $record, array $data, RfqService $service): void {
                        $supplier = Supplier::query()->findOrFail($data['supplier_id']);
                        $invitation = $service->invite($record, $supplier);
                        Notification::make()
                            ->title('Supplier invited')
                            ->body($service->signedInvitationUrl($invitation))
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRfqs::route('/'),
            'edit' => EditRfq::route('/{record}/edit'),
        ];
    }
}
