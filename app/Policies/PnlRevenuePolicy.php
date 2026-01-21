<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PnL\PnlRevenue;

class PnlRevenuePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PnlRevenue $revenue): bool
    {
        return $user->id === $revenue->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PnlRevenue $revenue): bool
    {
        return $user->id === $revenue->user_id;
    }

    public function delete(User $user, PnlRevenue $revenue): bool
    {
        return $user->id === $revenue->user_id;
    }
}
