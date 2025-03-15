<?php
namespace App\Policies\v1;

use App\Models\Ticket;
use App\Models\User;
use App\Permissions\V1\Abilities;

class UserPolicy
{
    public function update(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::UpdateUser);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::DeleteUser);
    }

    public function replace(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::ReplaceTicket);
    }

    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateUser);
    }
}
