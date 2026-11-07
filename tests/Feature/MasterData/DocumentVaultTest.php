<?php

use App\Facades\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\documents\Models\Document;
use App\Modules\documents\Services\DocumentVaultService;
use App\Modules\suppliers\Models\Supplier;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    Module::syncFromManifests();
    Storage::fake('public');

    $this->tenant = Tenant::query()->create([
        'name' => 'Doc Tenant',
        'slug' => 'doc-tenant',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    Module::enable('documents', $this->tenant->id);
    Module::enable('suppliers', $this->tenant->id);
    TenantContext::set($this->tenant);

    $this->supplier = Supplier::query()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Doc Supplier',
        'code' => 'DOC-SUP',
        'currency_code' => 'USD',
    ]);

    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'type' => 'tenant',
    ]);
});

afterEach(fn () => TenantContext::clear());

it('stores morph documents through the vault service', function (): void {
    $file = UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf');

    $document = app(DocumentVaultService::class)->store(
        $this->supplier,
        ['title' => 'ISO Certificate', 'type' => 'certificate'],
        $file,
        $this->user,
    );

    expect($document)->toBeInstanceOf(Document::class)
        ->and($document->tenant_id)->toBe($this->tenant->id)
        ->and($document->documentable_id)->toBe($this->supplier->id)
        ->and($document->getFirstMedia('file'))->not->toBeNull();
});
