<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PnL\PnlExpenseCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class PnlExpenseCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Users can view system-default categories (user_id = null) and their own categories.
     */
    public function view(User $user, PnlExpenseCategory $category): bool
    {
        return $category->user_id === null || $category->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only user's own categories can be updated, not system-default.
     */
    public function update(User $user, PnlExpenseCategory $category): bool
    {
        // System-default categories (user_id = null) cannot be edited
        if ($category->user_id === null) {
            return false;
        }
        return $category->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Only user's own categories can be deleted, not system-default.
     */
    public function delete(User $user, PnlExpenseCategory $category): bool
    {
        // System-default categories cannot be deleted
        if ($category->user_id === null) {
            return false;
        }
        return $category->user_id === $user->id;
    }
}
