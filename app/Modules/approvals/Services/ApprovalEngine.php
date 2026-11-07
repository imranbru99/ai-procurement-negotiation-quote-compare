<?php

namespace App\Modules\approvals\Services;

use App\Models\ApprovalAction;
use App\Models\ApprovalRequest;
use App\Models\ApprovalWorkflow;
use App\Models\PurchaseRequisition;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ApprovalEngine
{
    public function findWorkflowFor(Model $approvable): ?ApprovalWorkflow
    {
        return ApprovalWorkflow::query()
            ->where('entity_type', $approvable::class)
            ->where('is_active', true)
            ->first();
    }

    public function start(Model $approvable, ?ApprovalWorkflow $workflow = null): ApprovalRequest
    {
        $workflow ??= $this->findWorkflowFor($approvable);

        if (! $workflow) {
            throw new InvalidArgumentException('No active approval workflow found for this entity.');
        }

        $tenantId = $approvable->getAttribute('tenant_id');

        return ApprovalRequest::query()->create([
            'tenant_id' => $tenantId,
            'approvable_type' => $approvable::class,
            'approvable_id' => $approvable->getKey(),
            'workflow_id' => $workflow->id,
            'status' => ApprovalRequest::STATUS_PENDING,
            'current_level' => 1,
        ]);
    }

    public function approve(ApprovalRequest $request, User $approver, ?string $comment = null): ApprovalRequest
    {
        if ($request->status !== ApprovalRequest::STATUS_PENDING) {
            throw new InvalidArgumentException('Only pending requests can be approved.');
        }

        $this->recordAction($request, $approver, 'approved', $comment);

        $maxLevel = $this->maxLevel($request);

        if ($request->current_level >= $maxLevel) {
            $request->update(['status' => ApprovalRequest::STATUS_APPROVED]);
            $this->syncApprovableStatus($request, approved: true);
        } else {
            $request->update(['current_level' => $request->current_level + 1]);
        }

        return $request->fresh();
    }

    public function reject(ApprovalRequest $request, User $approver, ?string $comment = null): ApprovalRequest
    {
        if ($request->status !== ApprovalRequest::STATUS_PENDING) {
            throw new InvalidArgumentException('Only pending requests can be rejected.');
        }

        $this->recordAction($request, $approver, 'rejected', $comment);

        $request->update(['status' => ApprovalRequest::STATUS_REJECTED]);
        $this->syncApprovableStatus($request, approved: false);

        return $request->fresh();
    }

    public function escalate(ApprovalRequest $request, User $approver, ?string $comment = null): ApprovalRequest
    {
        if ($request->status !== ApprovalRequest::STATUS_PENDING) {
            throw new InvalidArgumentException('Only pending requests can be escalated.');
        }

        $this->recordAction($request, $approver, 'escalated', $comment);

        $maxLevel = $this->maxLevel($request);

        if ($request->current_level >= $maxLevel) {
            $request->update(['status' => ApprovalRequest::STATUS_ESCALATED]);
        } else {
            $request->update([
                'current_level' => $request->current_level + 1,
                'status' => ApprovalRequest::STATUS_ESCALATED,
            ]);
            $request->update(['status' => ApprovalRequest::STATUS_PENDING]);
        }

        return $request->fresh();
    }

    protected function recordAction(ApprovalRequest $request, User $approver, string $action, ?string $comment): void
    {
        ApprovalAction::query()->create([
            'request_id' => $request->id,
            'approver_id' => $approver->id,
            'level' => $request->current_level,
            'action' => $action,
            'comment' => $comment,
            'acted_at' => now(),
        ]);
    }

    protected function maxLevel(ApprovalRequest $request): int
    {
        $levels = $request->workflow?->rules['levels'] ?? [['level' => 1]];

        return max(1, count($levels));
    }

    protected function syncApprovableStatus(ApprovalRequest $request, bool $approved): void
    {
        $approvable = $request->approvable;

        if (! $approvable instanceof PurchaseRequisition) {
            return;
        }

        $approvable->update([
            'status' => $approved
                ? PurchaseRequisition::STATUS_APPROVED
                : PurchaseRequisition::STATUS_REJECTED,
        ]);
    }
}
