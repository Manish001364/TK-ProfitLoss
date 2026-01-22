<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PnL\PnlEvent;

class PnlEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PnlEvent $event): bool
    {
        return (int) $user->id === (int) $event->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PnlEvent $event): bool
    {
        return (int) $user->id === (int) $event->user_id;
    }

    public function delete(User $user, PnlEvent $event): bool
    {
        return (int) $user->id === (int) $event->user_id;
    }

    public function restore(User $user, PnlEvent $event): bool
    {
        return (int) $user->id === (int) $event->user_id;
    }

    public function forceDelete(User $user, PnlEvent $event): bool
    {
        return (int) $user->id === (int) $event->user_id;
    }
}
