<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PnL\PnlVendor;

class PnlVendorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PnlVendor $vendor): bool
    {
        return (int) $user->id === (int) $vendor->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PnlVendor $vendor): bool
    {
        return (int) $user->id === (int) $vendor->user_id;
    }

    public function delete(User $user, PnlVendor $vendor): bool
    {
        return (int) $user->id === (int) $vendor->user_id;
    }
}
