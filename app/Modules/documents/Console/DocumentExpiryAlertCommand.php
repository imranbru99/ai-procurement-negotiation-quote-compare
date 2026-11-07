<?php

namespace App\Modules\documents\Console;

use App\Modules\documents\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DocumentExpiryAlertCommand extends Command
{
    protected $signature = 'documents:expiry-alerts {--days=30 : Days ahead to warn}';

    protected $description = 'Log and notify about documents nearing expiry';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $until = now()->addDays($days);

        $expiring = Document::query()
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), $until])
            ->orderBy('expires_at')
            ->get(['id', 'tenant_id', 'title', 'expires_at']);

        foreach ($expiring as $document) {
            Log::channel('stack')->info('Document nearing expiry', [
                'document_id' => $document->id,
                'tenant_id' => $document->tenant_id,
                'title' => $document->title,
                'expires_at' => optional($document->expires_at)->toIso8601String(),
            ]);
        }

        $this->info("Found {$expiring->count()} document(s) expiring within {$days} day(s).");

        return self::SUCCESS;
    }
}
