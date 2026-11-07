<?php

namespace App\Filament\App\Pages;

use App\Facades\Module;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;

class StaffAttendancePage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Staff Attendance';

    protected static string|\UnitEnum|null $navigationGroup = 'Platform';

    protected static ?int $navigationSort = 45;

    protected string $view = 'filament.app.pages.staff-attendance';

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled('attendance');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(AttendanceLog::query()->latest('punched_at'))
            ->columns([
                TextColumn::make('device_id'),
                TextColumn::make('employee_id'),
                TextColumn::make('punch_type'),
                TextColumn::make('punched_at')->dateTime(),
            ])
            ->headerActions([
                Action::make('syncVirtual')
                    ->label('Sync virtual device')
                    ->action(function (): void {
                        $device = AttendanceDevice::query()
                            ->where('provider', 'virtual')
                            ->first();
                        if ($device) {
                            AttendanceHub::sync($device);
                        }
                    }),
            ]);
    }
}
