<?php

namespace Database\Seeders;

use App\Facades\Module;
use App\Models\Award;
use App\Models\AwardLine;
use App\Models\NegotiationRound;
use App\Models\NegotiationThread;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Rfq;
use App\Models\RfqInvitation;
use App\Models\RfqLine;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\catalog\Models\CatalogCategory;
use App\Modules\catalog\Models\CatalogUnit;
use App\Modules\multi_currency\Database\Seeders\MultiCurrencyModuleSeeder;
use App\Modules\suppliers\Enums\SupplierStatus;
use App\Modules\suppliers\Enums\SupplierTier;
use App\Modules\suppliers\Models\Supplier;
use App\Modules\suppliers\Models\SupplierCategory;
use App\Services\Tenancy\TenantContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TenantVolumeSeeder extends Seeder
{
    public ?Tenant $tenant = null;

    /** @var array<string, User> */
    public array $users = [];

    /** @var array<string, mixed> */
    public array $definition = [];

    /** @var list<Supplier> */
    protected array $suppliers = [];

    protected ?CatalogUnit $unit = null;

    protected ?CatalogCategory $catalogCategory = null;

    public function run(?Tenant $tenant = null, array $users = [], array $definition = []): void
    {
        $this->tenant = $tenant;
        $this->users = $users;
        $this->definition = $definition;

        if (! $this->tenant) {
            return;
        }

        TenantContext::set($this->tenant);

        $this->callWith(MultiCurrencyModuleSeeder::class, ['tenant' => $this->tenant]);
        $this->seedFoundation();
        $this->seedSuppliers();
        $this->seedSupplierPortalUsers();
        $this->seedCatalog();
        $this->seedRequisitions();
        [$rfqs, $quotes] = $this->seedRfqsAndQuotes();
        $this->seedNegotiations($rfqs);
        $this->seedAwardsAndP2P($rfqs, $quotes);
        $this->seedPerformanceAndSavings($rfqs, $quotes);
        $this->seedReverseAuctions($rfqs);
        $this->seedStockItems();
        $this->seedSupportAndAttendance();

        TenantContext::clear();
    }

    protected function seedFoundation(): void
    {
        CatalogUnit::query()->firstOrCreate(
            ['tenant_id' => null, 'code' => 'EA'],
            ['name' => 'Each'],
        );

        $this->unit = CatalogUnit::query()->firstOrCreate(
            ['tenant_id' => $this->tenant->id, 'code' => 'EA'],
            ['name' => 'Each'],
        );

        $this->catalogCategory = CatalogCategory::query()->firstOrCreate(
            ['tenant_id' => $this->tenant->id, 'slug' => 'general'],
            ['name' => 'General'],
        );

        SupplierCategory::query()->firstOrCreate(
            ['tenant_id' => $this->tenant->id, 'slug' => 'preferred'],
            ['name' => 'Preferred', 'description' => 'Approved vendor pool'],
        );
    }

    protected function seedSuppliers(): void
    {
        if (! Module::enabled('suppliers', $this->tenant->id)) {
            return;
        }

        $category = SupplierCategory::query()
            ->where('tenant_id', $this->tenant->id)
            ->first();

        Supplier::withoutEvents(function () use ($category): void {
            for ($i = 1; $i <= 28; $i++) {
                $name = fake()->company();
                $this->suppliers[] = Supplier::query()->create([
                    'tenant_id' => $this->tenant->id,
                    'category_id' => $category?->id,
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.$this->tenant->id.'-'.$i,
                    'code' => strtoupper($this->tenant->slug).'-S'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'email' => "vendor{$i}@{$this->tenant->slug}.test",
                    'country' => fake()->countryCode(),
                    'city' => fake()->city(),
                    'currency_code' => $this->tenant->currency,
                    'tier' => $i <= 5 ? SupplierTier::Preferred->value : SupplierTier::Standard->value,
                    'status' => SupplierStatus::Approved->value,
                    'risk_score' => fake()->numberBetween(8, 92),
                ]);
            }
        });
    }

    protected function seedSupplierPortalUsers(): void
    {
        foreach (array_slice($this->suppliers, 0, 3) as $index => $supplier) {
            $n = $index + 1;
            $user = User::query()->updateOrCreate(
                ['email' => "supplier{$n}@{$this->tenant->slug}.test"],
                [
                    'name' => "Supplier {$n} Portal",
                    'password' => Hash::make('password'),
                    'type' => 'supplier',
                    'tenant_id' => $this->tenant->id,
                    'supplier_id' => $supplier->id,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
            $user->syncRoles(['supplier']);
            $this->users["supplier{$n}"] = $user;
        }
    }

    protected function seedCatalog(): void
    {
        if (! Module::enabled('catalog', $this->tenant->id) || ! $this->unit || ! $this->catalogCategory) {
            return;
        }

        $localizedSamples = [
            ['name' => 'স্টील বার ১২মিমি', 'translations' => ['bn' => ['name' => 'স্টील বार ১২মিমি']]],
            ['name' => 'مثقاب كهربائي صناعي', 'translations' => ['ar' => ['name' => 'مثقاب كهربائي صناعي']]],
        ];

        $rows = [];
        $now = now();

        for ($i = 1; $i <= 155; $i++) {
            $sample = $i <= 2 ? $localizedSamples[$i - 1] : null;
            $name = $sample['name'] ?? fake()->words(3, true);
            $rows[] = [
                'tenant_id' => $this->tenant->id,
                'category_id' => $this->catalogCategory->id,
                'sku' => strtoupper(substr($this->tenant->slug, 0, 3)).'-SKU-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'name' => $name,
                'slug' => Str::slug($name).'-'.$this->tenant->id.'-'.$i,
                'description' => fake()->sentence(),
                'unit_id' => $this->unit->id,
                'base_price' => fake()->randomFloat(2, 2, 2500),
                'currency_code' => $this->tenant->currency,
                'is_service' => $i % 17 === 0,
                'is_active' => true,
                'specs' => json_encode([]),
                'translations' => json_encode($sample['translations'] ?? []),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= 50) {
                DB::table('catalog_items')->insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('catalog_items')->insert($rows);
        }
    }

    protected function seedRequisitions(): void
    {
        if (! Module::enabled('requisitions', $this->tenant->id)) {
            return;
        }

        $requester = $this->users['requester'] ?? User::query()->where('tenant_id', $this->tenant->id)->first();
        if (! $requester) {
            return;
        }

        $statuses = [
            PurchaseRequisition::STATUS_DRAFT,
            PurchaseRequisition::STATUS_SUBMITTED,
            PurchaseRequisition::STATUS_APPROVED,
            PurchaseRequisition::STATUS_REJECTED,
            PurchaseRequisition::STATUS_CONVERTED,
        ];

        foreach ($statuses as $idx => $status) {
            $pr = PurchaseRequisition::query()->create([
                'tenant_id' => $this->tenant->id,
                'number' => 'PR-'.strtoupper($this->tenant->slug).'-'.str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT),
                'requester_id' => $requester->id,
                'department' => fake()->randomElement(['Operations', 'IT', 'Facilities']),
                'status' => $status,
                'urgency' => fake()->randomElement(['low', 'normal', 'high']),
                'currency_code' => $this->tenant->currency,
                'budget_amount' => fake()->randomFloat(2, 500, 50000),
            ]);

            PurchaseRequisitionLine::query()->create([
                'requisition_id' => $pr->id,
                'description' => 'Line item for '.$pr->number,
                'qty' => fake()->numberBetween(1, 50),
                'estimated_unit_price' => fake()->randomFloat(2, 10, 500),
                'currency_code' => $this->tenant->currency,
            ]);
        }
    }

    /**
     * @return array{0: list<Rfq>, 1: list<Quote>}
     */
    protected function seedRfqsAndQuotes(): array
    {
        $rfqs = [];
        $quotes = [];

        if (! Module::enabled('rfq', $this->tenant->id) || $this->suppliers === []) {
            return [$rfqs, $quotes];
        }

        $statuses = ['draft', 'published', 'closed', 'awarded', 'cancelled', 'published', 'published', 'closed', 'published', 'draft', 'published', 'published'];

        foreach ($statuses as $idx => $status) {
            $rfq = Rfq::query()->create([
                'tenant_id' => $this->tenant->id,
                'number' => 'RFQ-'.strtoupper($this->tenant->slug).'-'.str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT),
                'title' => fake()->sentence(4),
                'status' => $status,
                'type' => 'rfq',
                'currency_code' => $this->tenant->currency,
                'published_at' => in_array($status, ['published', 'closed', 'awarded'], true) ? now()->subDays(3) : null,
                'deadline_at' => now()->addDays(14),
            ]);

            $line = RfqLine::query()->create([
                'rfq_id' => $rfq->id,
                'description' => 'RFQ line '.$rfq->number,
                'qty' => fake()->numberBetween(5, 200),
                'target_price' => fake()->randomFloat(2, 20, 400),
            ]);

            $rfqs[] = $rfq;

            $inviteSuppliers = collect($this->suppliers)->random(min(5, count($this->suppliers)));

            foreach ($inviteSuppliers as $supplier) {
                RfqInvitation::query()->firstOrCreate(
                    ['rfq_id' => $rfq->id, 'supplier_id' => $supplier->id],
                    [
                        'token' => Str::random(32),
                        'status' => 'invited',
                        'invited_at' => now()->subDay(),
                    ],
                );

                if (! Module::enabled('quotes', $this->tenant->id)) {
                    continue;
                }

                $base = (float) $line->target_price * (float) $line->qty;
                $outlier = $supplier->id % 7 === 0;
                $hiddenFees = $outlier ? $base * 0.18 : 0;
                $unitPrice = $outlier ? (float) $line->target_price * 1.35 : (float) $line->target_price * fake()->randomFloat(2, 0.92, 1.08);
                $subtotal = $unitPrice * (float) $line->qty;

                $quote = Quote::query()->create([
                    'tenant_id' => $this->tenant->id,
                    'rfq_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                    'number' => 'Q-'.$rfq->id.'-'.$supplier->id,
                    'status' => $status === 'draft' ? 'draft' : 'submitted',
                    'currency_code' => $this->tenant->currency,
                    'subtotal' => $subtotal,
                    'tax_total' => round($subtotal * 0.1, 2),
                    'other_fees' => $hiddenFees,
                    'shipping_total' => $outlier ? 250 : 45,
                    'grand_total' => round($subtotal * 1.1 + $hiddenFees + ($outlier ? 250 : 45), 2),
                    'submitted_at' => $status === 'draft' ? null : now()->subHours(6),
                    'source' => 'manual',
                ]);

                QuoteLine::query()->create([
                    'quote_id' => $quote->id,
                    'rfq_line_id' => $line->id,
                    'description' => $outlier ? 'Widget (quoted per dozen, RFQ in EA)' : 'Widget',
                    'qty' => (float) $line->qty,
                    'unit_price' => $unitPrice,
                    'tax_rate' => 10,
                    'line_total' => $subtotal,
                    'notes' => $outlier ? 'Unit mismatch: hidden pallet surcharge in other_fees' : null,
                ]);

                $quotes[] = $quote;
            }
        }

        return [$rfqs, $quotes];
    }

    /**
     * @param  list<Rfq>  $rfqs
     */
    protected function seedNegotiations(array $rfqs): void
    {
        if (! Module::enabled('ai_negotiation', $this->tenant->id) || $rfqs === []) {
            return;
        }

        $buyer = $this->users['buyer'] ?? null;

        foreach (array_slice($rfqs, 0, 6) as $rfq) {
            $supplier = $this->suppliers[array_key_first($this->suppliers)];
            $thread = NegotiationThread::query()->create([
                'tenant_id' => $this->tenant->id,
                'rfq_id' => $rfq->id,
                'supplier_id' => $supplier->id,
                'status' => 'open',
            ]);

            for ($round = 1; $round <= 3; $round++) {
                NegotiationRound::query()->create([
                    'thread_id' => $thread->id,
                    'round_number' => $round,
                    'direction' => $round % 2 === 1 ? 'outbound' : 'inbound',
                    'message_body' => "Round {$round}: counter-offer on {$rfq->number}",
                    'channel' => 'email',
                    'created_by' => $buyer?->id,
                    'ai_generated' => $round === 2,
                    'delta_amount' => 500 - ($round * 120),
                    'savings_amount' => $round * 80,
                    'status' => 'sent',
                ]);
            }
        }
    }

    /**
     * @param  list<Rfq>  $rfqs
     * @param  list<Quote>  $quotes
     */
    protected function seedAwardsAndP2P(array $rfqs, array $quotes): void
    {
        if (! Module::enabled('awards', $this->tenant->id) || $quotes === []) {
            return;
        }

        $rfq = collect($rfqs)->firstWhere('status', 'published') ?? $rfqs[0] ?? null;
        $quote = $quotes[0] ?? null;
        if (! $rfq || ! $quote) {
            return;
        }

        $buyer = $this->users['buyer'] ?? null;
        $award = Award::query()->create([
            'tenant_id' => $this->tenant->id,
            'rfq_id' => $rfq->id,
            'status' => 'finalized',
            'awarded_by' => $buyer?->id,
            'awarded_at' => now()->subDay(),
            'justification' => 'Best TCO after AI comparison.',
        ]);

        $rfqLine = $rfq->lines()->first();
        $quoteLine = $quote->lines()->first();
        if ($rfqLine && $quoteLine) {
            AwardLine::query()->create([
                'award_id' => $award->id,
                'rfq_line_id' => $rfqLine->id,
                'supplier_id' => $quote->supplier_id,
                'quote_id' => $quote->id,
                'quote_line_id' => $quoteLine->id,
                'qty' => $rfqLine->qty,
                'unit_price' => $quoteLine->unit_price,
                'currency_code' => $this->tenant->currency,
            ]);
        }

        if (! Module::enabled('purchase_orders', $this->tenant->id)) {
            return;
        }

        $po = PurchaseOrder::query()->create([
            'tenant_id' => $this->tenant->id,
            'award_id' => $award->id,
            'supplier_id' => $quote->supplier_id,
            'number' => 'PO-'.strtoupper($this->tenant->slug).'-0001',
            'status' => 'issued',
            'currency_code' => $this->tenant->currency,
            'subtotal' => $quote->subtotal,
            'tax' => $quote->tax_total,
            'total' => $quote->grand_total,
        ]);

        if ($quoteLine) {
            PurchaseOrderLine::query()->create([
                'purchase_order_id' => $po->id,
                'rfq_line_id' => $rfqLine?->id,
                'description' => $quoteLine->description,
                'qty' => $quoteLine->qty,
                'qty_received' => 0,
                'unit_price' => $quoteLine->unit_price,
                'currency_code' => $this->tenant->currency,
            ]);
        }

        if (Module::enabled('goods_receiving', $this->tenant->id) && Schema::hasTable('goods_receipts')) {
            $poLine = $po->lines()->first();
            $grnId = DB::table('goods_receipts')->insertGetId([
                'tenant_id' => $this->tenant->id,
                'po_id' => $po->id,
                'number' => 'GRN-'.strtoupper($this->tenant->slug).'-0001',
                'received_by' => $this->users['warehouse']->id ?? null,
                'status' => 'posted',
                'received_at' => now()->subHours(4),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($poLine) {
                DB::table('goods_receipt_lines')->insert([
                    'grn_id' => $grnId,
                    'po_line_id' => $poLine->id,
                    'qty_received' => $poLine->qty,
                    'qty_accepted' => $poLine->qty,
                    'qty_rejected' => 0,
                    'qc_status' => 'passed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (Module::enabled('invoices', $this->tenant->id)) {
                $variance = $this->tenant->slug === 'buildright-construction' ? 125.5 : 0;
                $invoiceId = DB::table('supplier_invoices')->insertGetId([
                    'tenant_id' => $this->tenant->id,
                    'supplier_id' => $quote->supplier_id,
                    'po_id' => $po->id,
                    'number' => 'INV-'.strtoupper($this->tenant->slug).'-0001',
                    'status' => $variance > 0 ? 'exception' : 'approved',
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->addDays(30)->toDateString(),
                    'currency_code' => $this->tenant->currency,
                    'subtotal' => $quote->subtotal + $variance,
                    'tax' => $quote->tax_total,
                    'total' => $quote->grand_total + $variance,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($poLine) {
                    DB::table('supplier_invoice_lines')->insert([
                        'supplier_invoice_id' => $invoiceId,
                        'po_line_id' => $poLine->id,
                        'description' => $poLine->description,
                        'qty' => $poLine->qty,
                        'unit_price' => $poLine->unit_price,
                        'line_total' => $poLine->qty * $poLine->unit_price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('three_way_matches')->insert([
                    'tenant_id' => $this->tenant->id,
                    'po_id' => $po->id,
                    'grn_id' => $grnId,
                    'invoice_id' => $invoiceId,
                    'status' => $variance > 0 ? 'exception' : 'matched',
                    'variance_amount' => $variance,
                    'exception_reason' => $variance > 0 ? 'Invoice qty exceeds GRN' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (Module::enabled('payments', $this->tenant->id)) {
                    DB::table('payments')->insert([
                        'tenant_id' => $this->tenant->id,
                        'invoice_id' => $invoiceId,
                        'amount' => $quote->grand_total,
                        'currency_code' => $this->tenant->currency,
                        'status' => $variance > 0 ? 'on_hold' : 'paid',
                        'paid_at' => $variance > 0 ? null : now()->subHour(),
                        'method' => 'ach',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if (Module::enabled('contracts', $this->tenant->id) && Schema::hasTable('contracts')) {
            DB::table('contracts')->insert([
                'tenant_id' => $this->tenant->id,
                'supplier_id' => $quote->supplier_id,
                'po_id' => $po->id,
                'title' => 'Master supply agreement — renewal due',
                'status' => 'active',
                'starts_at' => now()->subYear(),
                'ends_at' => now()->addDays(21),
                'renewal_alert_days' => 30,
                'signed_at' => now()->subYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Module::enabled('budgets', $this->tenant->id) && Schema::hasTable('budgets')) {
            $ccId = DB::table('cost_centers')->insertGetId([
                'tenant_id' => $this->tenant->id,
                'code' => 'CC-OPS',
                'name' => 'Operations',
                'department' => 'Ops',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('budgets')->insert([
                'tenant_id' => $this->tenant->id,
                'cost_center_id' => $ccId,
                'year' => (int) now()->format('Y'),
                'amount' => 500000,
                'currency_code' => $this->tenant->currency,
                'committed' => $quote->grand_total,
                'actual' => $quote->grand_total * 0.4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Module::enabled('inventory', $this->tenant->id) && Schema::hasTable('warehouses')) {
            DB::table('warehouses')->updateOrInsert(
                ['tenant_id' => $this->tenant->id, 'code' => 'WH-MAIN'],
                [
                    'name' => 'Main warehouse',
                    'address' => fake()->address(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    /**
     * @param  list<Rfq>  $rfqs
     */
    protected function seedReverseAuctions(array $rfqs): void
    {
        if (! Module::enabled('reverse_auctions', $this->tenant->id) || ! Schema::hasTable('reverse_auctions') || $rfqs === []) {
            return;
        }

        $published = collect($rfqs)->firstWhere('status', 'published') ?? $rfqs[0];

        $auctionId = DB::table('reverse_auctions')->insertGetId([
            'tenant_id' => $this->tenant->id,
            'rfq_id' => $published->id,
            'title' => 'Live reverse auction — '.$published->number,
            'status' => 'live',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDays(2),
            'auto_extend_minutes' => 5,
            'min_bid_decrement' => 25,
            'currency_code' => $this->tenant->currency,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('reverse_auctions')->insert([
            'tenant_id' => $this->tenant->id,
            'rfq_id' => $published->id,
            'title' => 'Draft reverse auction — '.$published->number,
            'status' => 'draft',
            'starts_at' => null,
            'ends_at' => null,
            'auto_extend_minutes' => 5,
            'min_bid_decrement' => 10,
            'currency_code' => $this->tenant->currency,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! Schema::hasTable('auction_bids') || $this->suppliers === []) {
            return;
        }

        $amount = 10000;
        foreach (array_slice($this->suppliers, 0, 3) as $index => $supplier) {
            $amount -= 250 * ($index + 1);
            DB::table('auction_bids')->insert([
                'auction_id' => $auctionId,
                'supplier_id' => $supplier->id,
                'amount' => $amount,
                'placed_at' => now()->subMinutes(30 - ($index * 5)),
                'is_winning' => $index === 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function seedStockItems(): void
    {
        if (! Module::enabled('inventory', $this->tenant->id) || ! Schema::hasTable('stock_items') || ! Schema::hasTable('warehouses')) {
            return;
        }

        $warehouseId = DB::table('warehouses')
            ->where('tenant_id', $this->tenant->id)
            ->where('code', 'WH-MAIN')
            ->value('id');

        if (! $warehouseId) {
            $warehouseId = DB::table('warehouses')->insertGetId([
                'tenant_id' => $this->tenant->id,
                'code' => 'WH-MAIN',
                'name' => 'Main warehouse',
                'address' => fake()->address(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $catalogItemIds = DB::table('catalog_items')
            ->where('tenant_id', $this->tenant->id)
            ->orderBy('id')
            ->limit(12)
            ->pluck('id');

        foreach ($catalogItemIds as $index => $catalogItemId) {
            DB::table('stock_items')->updateOrInsert(
                [
                    'warehouse_id' => $warehouseId,
                    'catalog_item_id' => $catalogItemId,
                ],
                [
                    'tenant_id' => $this->tenant->id,
                    'qty_on_hand' => fake()->randomFloat(2, 5, 500),
                    'reorder_point' => 25,
                    'reorder_qty' => 100,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );

            if ($index >= 5 && Schema::hasTable('stock_movements')) {
                $stockItemId = DB::table('stock_items')
                    ->where('warehouse_id', $warehouseId)
                    ->where('catalog_item_id', $catalogItemId)
                    ->value('id');

                if ($stockItemId) {
                    DB::table('stock_movements')->insert([
                        'stock_item_id' => $stockItemId,
                        'type' => 'receipt',
                        'qty' => 50,
                        'user_id' => $this->users['warehouse']->id ?? null,
                        'created_at' => now()->subDays($index),
                        'updated_at' => now()->subDays($index),
                    ]);
                }
            }
        }
    }

    /**
     * @param  list<Rfq>  $rfqs
     * @param  list<Quote>  $quotes
     */
    protected function seedPerformanceAndSavings(array $rfqs, array $quotes): void
    {
        if ($this->suppliers === []) {
            return;
        }

        $supplier = $this->suppliers[0];

        if (Module::enabled('supplier_performance', $this->tenant->id) && Schema::hasTable('supplier_scorecards')) {
            DB::table('supplier_scorecards')->insert([
                'tenant_id' => $this->tenant->id,
                'supplier_id' => $supplier->id,
                'period_start' => now()->subQuarter()->toDateString(),
                'period_end' => now()->toDateString(),
                'on_time_score' => 88.5,
                'quality_score' => 91.0,
                'price_score' => 84.0,
                'responsiveness_score' => 90.0,
                'overall' => 88.4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Module::enabled('supplier_risk', $this->tenant->id) && Schema::hasTable('supplier_risk_assessments')) {
            DB::table('supplier_risk_assessments')->insert([
                'tenant_id' => $this->tenant->id,
                'supplier_id' => $supplier->id,
                'risk_score' => 42,
                'financial_flag' => false,
                'geo_flag' => true,
                'compliance_flag' => false,
                'ai_reasoning' => 'Demo: elevated geo risk due to single-source region.',
                'assessed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Module::enabled('savings', $this->tenant->id) && Schema::hasTable('savings_records') && isset($quotes[0])) {
            $quote = $quotes[0];
            $rfq = $rfqs[0] ?? null;
            DB::table('savings_records')->insert([
                'tenant_id' => $this->tenant->id,
                'rfq_id' => $rfq?->id,
                'quote_id' => $quote->id,
                'buyer_id' => $this->users['buyer']->id ?? null,
                'baseline_amount' => $quote->grand_total * 1.12,
                'negotiated_amount' => $quote->grand_total,
                'hard_savings' => $quote->grand_total * 0.12,
                'soft_savings' => 500,
                'currency_code' => $this->tenant->currency,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function seedSupportAndAttendance(): void
    {
        if (Module::enabled('support', $this->tenant->id) && Schema::hasTable('support_tickets')) {
            $catId = DB::table('ticket_categories')->insertGetId([
                'tenant_id' => $this->tenant->id,
                'name' => 'Sourcing',
                'slug' => 'sourcing',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('support_tickets')->insert([
                'tenant_id' => $this->tenant->id,
                'user_id' => $this->users['buyer']->id ?? null,
                'category_id' => $catId,
                'subject' => 'Quote comparison export question',
                'body' => 'How do I include AI flags in the PDF export?',
                'status' => 'open',
                'priority' => 'normal',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Module::enabled('attendance', $this->tenant->id) && Schema::hasTable('attendance_logs')) {
            for ($i = 0; $i < 5; $i++) {
                DB::table('attendance_logs')->insert([
                    'tenant_id' => (string) $this->tenant->id,
                    'device_user_id' => 'EMP-'.$this->tenant->id,
                    'employee_id' => 'EMP-1001',
                    'punched_at' => now()->subDays($i)->setTime(8, 30),
                    'verify_mode' => 'fingerprint',
                    'punch_type' => 'check_in',
                    'punch_hash' => hash('sha256', $this->tenant->id.'-in-'.$i),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('activity_log') && isset($this->users['tenant_owner'])) {
            activity()
                ->causedBy($this->users['tenant_owner'])
                ->withProperties(['tenant' => $this->tenant->slug])
                ->log('Demo: procurement activity digest');
        }
    }
}
