<?php

namespace App\Policies;

use App\Models\FavoriteDestination;
use App\Models\User;

class FavoriteDestinationPolicy
{
    public function view(User $user, FavoriteDestination $favoriteDestination): bool
    {
        return $favoriteDestination->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, FavoriteDestination $favoriteDestination): bool
    {
        return $favoriteDestination->user_id === $user->id;
    }
}
