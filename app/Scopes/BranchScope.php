<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        
        if ($user && !$user->canManageAllBranches() && $user->branch_id !== null) {
            $builder->where('branch_id', $user->branch_id);
        }
    }
}
