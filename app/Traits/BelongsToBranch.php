<?php

namespace App\Traits;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function (Model $model) {
            if (auth()->check() && !$model->branch_id) {
                $model->branch_id = auth()->user()->branch_id;
            }
        });
    }

    public function scopeWithoutBranchScope($query)
    {
        return $query->withoutGlobalScope(BranchScope::class);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->withoutGlobalScope(BranchScope::class)
            ->where('branch_id', $branchId);
    }

    public function scopeForAllBranches($query)
    {
        return $query->withoutGlobalScope(BranchScope::class);
    }
}
