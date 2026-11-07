<?php

namespace App\Filament\App\Resources\Suppliers\Schemas;

use App\Modules\suppliers\Enums\SupplierStatus;
use App\Modules\suppliers\Enums\SupplierTier;
use App\Modules\suppliers\Models\SupplierCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')->maxLength(64),
                    Select::make('category_id')
                        ->label('Category')
                        ->options(fn (): array => SupplierCategory::query()->pluck('name', 'id')->all())
                        ->searchable(),
                    TextInput::make('legal_name')->maxLength(255),
                ]),
            Section::make('Contact & location')
                ->columns(2)
                ->schema([
                    TextInput::make('email')->email(),
                    TextInput::make('phone')->tel(),
                    TextInput::make('website')->url(),
                    TextInput::make('country')->maxLength(2),
                    TextInput::make('city'),
                    Textarea::make('address')->columnSpanFull(),
                ]),
            Section::make('Commercial')
                ->columns(2)
                ->schema([
                    TextInput::make('tax_id'),
                    TextInput::make('payment_terms'),
                    TextInput::make('currency_code')->default('USD')->maxLength(3),
                    Select::make('tier')
                        ->options(collect(SupplierTier::cases())->mapWithKeys(fn (SupplierTier $t) => [$t->value => str($t->name)->headline()])->all())
                        ->required(),
                    Select::make('status')
                        ->options(collect(SupplierStatus::cases())->mapWithKeys(fn (SupplierStatus $s) => [$s->value => str($s->name)->headline()])->all())
                        ->required(),
                    TextInput::make('risk_score')->numeric()->minValue(0)->maxValue(100),
                    Textarea::make('onboarding_notes')->columnSpanFull(),
                ]),
            Section::make('Banking')
                ->columns(2)
                ->schema([
                    TextInput::make('bank_name'),
                    TextInput::make('bank_account')->password()->revealable(),
                    TextInput::make('bank_iban')->password()->revealable(),
                ]),
        ]);
    }
}
