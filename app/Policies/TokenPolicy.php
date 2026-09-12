<?php

namespace App\Policies;

use App\Models\Token;
use App\Models\User;

class TokenPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('serials.view');
    }

    public function view(User $user, Token $token): bool
    {
        return $user->hasPermission('serials.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('serials.create');
    }
}
