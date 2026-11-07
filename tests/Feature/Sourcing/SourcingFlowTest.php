<?php

use App\Facades\Module;
use App\Models\ApprovalRequest;
use App\Models\ApprovalWorkflow;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Rfq;
use App\Models\RfqLine;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\approvals\Services\ApprovalEngine;
use App\Modules\quotes\Services\QuoteService;
use App\Modules\requisitions\Services\RequisitionService;
use App\Modules\rfq\Services\RfqService;
use App\Modules\suppliers\Models\Supplier;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->tenant = Tenant::query()->where('slug', 'acme-manufacturing')->first()
        ?? Tenant::query()->create([
            'name' => 'Acme Manufacturing',
            'slug' => 'acme-manufacturing',
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
            'is_active' => true,
        ]);

    foreach (['catalog', 'suppliers', 'requisitions', 'approvals', 'rfq', 'quotes'] as $module) {
        Module::enable($module, $this->tenant->id);
    }

    TenantContext::set($this->tenant);

    $this->requester = User::factory()->create([
        'type' => 'tenant',
        'tenant_id' => $this->tenant->id,
        'email' => 'requester@test.local',
    ]);
    $this->requester->assignRole('requester');

    $this->approver = User::factory()->create([
        'type' => 'tenant',
        'tenant_id' => $this->tenant->id,
        'email' => 'approver@test.local',
    ]);
    $this->approver->assignRole('approver');

    ApprovalWorkflow::query()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'PR workflow',
        'entity_type' => PurchaseRequisition::class,
        'rules' => ['levels' => [['level' => 1]]],
        'is_active' => true,
    ]);
});

afterEach(function (): void {
    TenantContext::clear();
});

it('submits a requisition and completes approval', function (): void {
    $requisition = PurchaseRequisition::query()->create([
        'tenant_id' => $this->tenant->id,
        'number' => 'PR-TEST-001',
        'requester_id' => $this->requester->id,
        'status' => PurchaseRequisition::STATUS_DRAFT,
        'urgency' => 'normal',
        'currency_code' => 'USD',
    ]);

    PurchaseRequisitionLine::query()->create([
        'requisition_id' => $requisition->id,
        'description' => 'Laptops',
        'qty' => 5,
        'estimated_unit_price' => 1000,
        'currency_code' => 'USD',
    ]);

    app(RequisitionService::class)->submit($requisition->fresh());

    expect($requisition->fresh()->status)->toBe(PurchaseRequisition::STATUS_SUBMITTED);

    $approval = ApprovalRequest::query()->first();

    expect($approval)->not->toBeNull()
        ->and($approval->status)->toBe(ApprovalRequest::STATUS_PENDING);

    app(ApprovalEngine::class)->approve($approval, $this->approver);

    expect($requisition->fresh()->status)->toBe(PurchaseRequisition::STATUS_APPROVED);
});

it('publishes an rfq and invites a supplier with a signed url', function (): void {
    $supplier = Supplier::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Globex Corp',
        'code' => 'GLOBEX',
    ]);

    $rfq = Rfq::query()->create([
        'tenant_id' => $this->tenant->id,
        'number' => 'RFQ-TEST-001',
        'title' => 'Office supplies',
        'status' => 'draft',
        'type' => 'rfq',
        'currency_code' => 'USD',
    ]);

    RfqLine::query()->create([
        'rfq_id' => $rfq->id,
        'description' => 'Paper',
        'qty' => 100,
    ]);

    $service = app(RfqService::class);
    $service->publish($rfq->fresh());

    expect($rfq->fresh()->status)->toBe('published');

    $invitation = $service->invite($rfq->fresh(), $supplier);
    $url = $service->signedInvitationUrl($invitation);

    expect($url)->toContain('signature=');

    $this->get($url)->assertOk();
});

it('submits a supplier quote with normalized totals', function (): void {
    $supplier = Supplier::factory()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Initech',
        'code' => 'INI',
    ]);

    $rfq = Rfq::query()->create([
        'tenant_id' => $this->tenant->id,
        'number' => 'RFQ-TEST-002',
        'title' => 'Hardware',
        'status' => 'published',
        'type' => 'rfq',
        'currency_code' => 'USD',
        'published_at' => now(),
    ]);

    $quote = Quote::query()->create([
        'tenant_id' => $this->tenant->id,
        'rfq_id' => $rfq->id,
        'supplier_id' => $supplier->id,
        'number' => 'Q-001',
        'status' => 'draft',
        'currency_code' => 'USD',
        'source' => 'manual',
    ]);

    QuoteLine::query()->create([
        'quote_id' => $quote->id,
        'description' => 'Widget',
        'qty' => 2,
        'unit_price' => 50,
        'tax_rate' => 10,
    ]);

    $quote = app(QuoteService::class)->submit($quote->fresh());

    expect($quote->status)->toBe('submitted')
        ->and((float) $quote->subtotal)->toBe(100.0)
        ->and((float) $quote->tax_total)->toBe(10.0)
        ->and((float) $quote->grand_total)->toBe(110.0);
});
