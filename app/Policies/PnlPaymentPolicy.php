<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PnL\PnlPayment;

class PnlPaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PnlPayment $payment): bool
    {
        return $user->id === $payment->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PnlPayment $payment): bool
    {
        return $user->id === $payment->user_id;
    }

    public function delete(User $user, PnlPayment $payment): bool
    {
        return $user->id === $payment->user_id;
    }
}
