<?php

namespace App\Policies;

use App\Models\PackingItem;
use App\Models\User;

class PackingItemPolicy
{
    public function view(User $user, PackingItem $packingItem): bool
    {
        return $packingItem->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PackingItem $packingItem): bool
    {
        return $packingItem->user_id === $user->id;
    }

    public function delete(User $user, PackingItem $packingItem): bool
    {
        return $packingItem->user_id === $user->id;
    }
}
