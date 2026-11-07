<?php

use App\Models\Budget;
use App\Models\CostCenter;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Rfq;
use App\Models\RfqLine;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\awards\Services\AwardService;
use App\Modules\goods_receiving\Services\GoodsReceivingService;
use App\Modules\invoices\Services\InvoiceService;
use App\Modules\invoices\Services\ThreeWayMatchService;
use App\Modules\payments\Services\PaymentService;
use App\Modules\purchase_orders\Services\POService;
use App\Services\Settings\SettingsService;
use App\Services\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Permission::findOrCreate('awards.manage');
});

it('runs award through payment with three-way match', function (): void {
    $tenant = Tenant::query()->create([
        'name' => 'Acme',
        'slug' => 'acme',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    TenantContext::set($tenant);

    $buyer = User::factory()->create([
        'tenant_id' => $tenant->id,
        'type' => 'tenant',
    ]);
    $buyer->givePermissionTo('awards.manage');

    $supplier = Supplier::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Widget Co',
        'code' => 'WIDGET',
        'email' => 'vendor@widget.test',
        'currency_code' => 'USD',
    ]);

    $costCenter = CostCenter::query()->create([
        'tenant_id' => $tenant->id,
        'code' => 'CC-100',
        'name' => 'Operations',
    ]);

    Budget::query()->create([
        'tenant_id' => $tenant->id,
        'cost_center_id' => $costCenter->id,
        'year' => (int) now()->year,
        'amount' => 50000,
        'currency_code' => 'USD',
    ]);

    $rfq = Rfq::query()->create([
        'tenant_id' => $tenant->id,
        'number' => 'RFQ-0001',
        'title' => 'Office supplies',
        'currency_code' => 'USD',
    ]);

    $rfqLine = RfqLine::query()->create([
        'rfq_id' => $rfq->id,
        'description' => 'Paper reams',
        'qty' => 100,
    ]);

    $quote = Quote::query()->create([
        'tenant_id' => $tenant->id,
        'rfq_id' => $rfq->id,
        'supplier_id' => $supplier->id,
        'number' => 'Q-0001',
        'currency_code' => 'USD',
    ]);

    $quoteLine = QuoteLine::query()->create([
        'quote_id' => $quote->id,
        'rfq_line_id' => $rfqLine->id,
        'description' => 'Paper reams',
        'qty' => 100,
        'unit_price' => 5,
    ]);

    $awardService = app(AwardService::class);
    $award = $awardService->createFromComparison($rfq, [[
        'rfq_line_id' => $rfqLine->id,
        'supplier_id' => $supplier->id,
        'quote_id' => $quote->id,
        'quote_line_id' => $quoteLine->id,
        'qty' => 100,
        'unit_price' => 5,
    ]]);

    expect($award->status)->toBe('pending_confirmation');

    $award = $awardService->confirm($award, $buyer, 'Best value');

    expect($award->status)->toBe('confirmed');

    $poService = app(POService::class);
    $orders = $poService->generateFromAward($award, $costCenter->id);

    expect($orders)->toHaveCount(1);
    $po = $orders->first();
    expect((float) $po->total)->toBe(500.0);

    $grnService = app(GoodsReceivingService::class);
    $grn = $grnService->receivePartial($po, [[
        'po_line_id' => $po->lines->first()->id,
        'qty_received' => 100,
        'qty_accepted' => 100,
    ]], $buyer);

    expect($po->fresh()->status)->toBe('received');

    app(SettingsService::class)->set('procurement', 'three_way_match_tolerance_percent', 2, $tenant->id);

    $invoiceService = app(InvoiceService::class);
    $invoice = $invoiceService->createFromPurchaseOrder($po->fresh('lines'), 'INV-1001');

    $match = app(ThreeWayMatchService::class)->match($po->fresh(), $grn, $invoice);

    expect($match->status)->toBe('matched');

    $payment = app(PaymentService::class)->schedule($invoice);
    $payment = app(PaymentService::class)->markPaid($payment, 'WIRE-1');

    expect($payment->status)->toBe('paid');

    $budget = Budget::query()->first();

    expect((float) $budget->committed)->toBe(500.0)
        ->and((float) $budget->actual)->toBe(500.0);

    TenantContext::clear();
});
