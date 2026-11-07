<?php

namespace App\Providers;

use App\Models\Award;
use App\Models\Budget;
use App\Models\Contract;
use App\Models\ContractClause;
use App\Models\CostCenter;
use App\Models\GoodsReceipt;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\StockItem;
use App\Models\SupplierInvoice;
use App\Models\ThreeWayMatch;
use App\Models\Warehouse;
use App\Policies\AwardPolicy;
use App\Policies\BudgetPolicy;
use App\Policies\ContractClausePolicy;
use App\Policies\ContractPolicy;
use App\Policies\CostCenterPolicy;
use App\Policies\GoodsReceiptPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\StockItemPolicy;
use App\Policies\SupplierInvoicePolicy;
use App\Policies\ThreeWayMatchPolicy;
use App\Policies\WarehousePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Award::class => AwardPolicy::class,
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        Contract::class => ContractPolicy::class,
        ContractClause::class => ContractClausePolicy::class,
        GoodsReceipt::class => GoodsReceiptPolicy::class,
        Warehouse::class => WarehousePolicy::class,
        StockItem::class => StockItemPolicy::class,
        SupplierInvoice::class => SupplierInvoicePolicy::class,
        ThreeWayMatch::class => ThreeWayMatchPolicy::class,
        Payment::class => PaymentPolicy::class,
        CostCenter::class => CostCenterPolicy::class,
        Budget::class => BudgetPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
