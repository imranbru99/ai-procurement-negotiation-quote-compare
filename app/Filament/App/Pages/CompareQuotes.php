<?php

namespace App\Filament\App\Pages;

use App\Facades\Module;
use App\Models\QuoteComparison;
use App\Models\Rfq;
use App\Modules\quote_comparison\Exports\QuoteComparisonExport;
use App\Modules\quote_comparison\Services\ComparisonEngine;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;

class CompareQuotes extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'Compare quotes';

    protected static ?string $title = 'Quote comparison';

    protected static ?int $navigationSort = 45;

    protected string $view = 'filament.app.pages.compare-quotes';

    #[Url]
    public ?int $rfqId = null;

    /** @var array<string, float> */
    public array $weights = [
        'price' => 35,
        'lead_time' => 15,
        'payment_terms' => 10,
        'warranty' => 10,
        'moq' => 10,
        'supplier_tier' => 20,
    ];

    /** @var array<string, mixed>|null */
    public ?array $results = null;

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled('quote_comparison');
    }

    public static function canAccess(): bool
    {
        return Module::enabled('quote_comparison')
            && auth()->user()?->can('quote_comparison.view');
    }

    public function mount(): void
    {
        $this->recalculate();
    }

    public function updatedWeights(): void
    {
        $this->recalculate();
    }

    public function recalculate(): void
    {
        if (! $this->rfqId) {
            $this->results = null;

            return;
        }

        $rfq = Rfq::query()->with(['quotes.supplier'])->findOrFail($this->rfqId);
        $engine = app(ComparisonEngine::class);

        $weightMap = collect($this->weights)->map(fn (float $v): float => $v / 100)->all();
        $this->results = $engine->compare($rfq->quotes, $weightMap, $rfq->currency_code);
    }

    public function saveComparison(): void
    {
        abort_unless($this->rfqId && $this->results, 422);

        QuoteComparison::query()->create([
            'rfq_id' => $this->rfqId,
            'name' => 'Comparison '.now()->format('Y-m-d H:i'),
            'weights' => $this->weights,
            'results' => $this->results,
            'created_by' => Auth::id(),
        ]);

        Notification::make()->title('Comparison saved')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('askAi')
                ->label('Ask AI')
                ->icon('heroicon-o-sparkles')
                ->visible(fn (): bool => Module::enabled('ai_negotiation') && auth()->user()?->can('ai_negotiation.view'))
                ->requiresConfirmation()
                ->modalDescription('Opens AI quote analysis (human review required).')
                ->action(function (): void {
                    Notification::make()
                        ->title('AI analysis queued')
                        ->body('Review results in AI Negotiation — no auto-award.')
                        ->success()
                        ->send();
                }),
            Action::make('exportExcel')
                ->label('Export Excel')
                ->action(fn () => Excel::download(
                    new QuoteComparisonExport($this->results ?? []),
                    'quote-comparison.xlsx',
                )),
            Action::make('exportPdf')
                ->label('Export PDF')
                ->action(function (): void {
                    $pdf = UnicodePdf::loadView('pdf.quote-comparison', [
                        'results' => $this->results,
                        'title' => 'Quote comparison',
                        'locale' => 'bn',
                    ])->preset('bengali');

                    $pdf->download('quote-comparison.pdf');
                }),
        ];
    }
}
