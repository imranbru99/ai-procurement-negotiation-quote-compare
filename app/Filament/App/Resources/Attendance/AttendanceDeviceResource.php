<?php

namespace App\Filament\App\Resources\Attendance;

use App\Filament\App\Resources\Attendance\Pages\ListAttendanceDevices;
use App\Filament\Concerns\RegistersModuleNavigation;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;

class AttendanceDeviceResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = AttendanceDevice::class;

    protected static ?string $navigationLabel = 'Attendance Devices';

    protected static string|\UnitEnum|null $navigationGroup = 'Platform';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFingerPrint;

    protected static function moduleKey(): string
    {
        return 'attendance';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Device')->schema([
                TextInput::make('name')->required(),
                TextInput::make('provider')->required(),
                TextInput::make('model'),
                TextInput::make('ip'),
                TextInput::make('port')->numeric(),
                TextInput::make('serial_number'),
                Toggle::make('auto_clear_logs'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('provider'),
            TextColumn::make('ip'),
            TextColumn::make('status')->badge(),
            TextColumn::make('last_synced_at')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceDevices::route('/'),
        ];
    }
}
