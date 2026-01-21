<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PnL\PnlExpense;

class PnlExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PnlExpense $expense): bool
    {
        return (int) $user->id === (int) $expense->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PnlExpense $expense): bool
    {
        return (int) $user->id === (int) $expense->user_id;
    }

    public function delete(User $user, PnlExpense $expense): bool
    {
        return (int) $user->id === (int) $expense->user_id;
    }
}
