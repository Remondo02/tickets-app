<?php
namespace App\Policies\v1;

use App\Models\Ticket;
use App\Models\User;
use App\Permissions\V1\Abilities;

class TicketPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::UpdateTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::DeleteTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    public function replace(User $user, Ticket $ticket): bool
    {
        return $user->tokenCan(Abilities::ReplaceTicket);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateTicket) ||
        $user->tokenCan(Abilities::CreateOwnTicket);
    }
}
