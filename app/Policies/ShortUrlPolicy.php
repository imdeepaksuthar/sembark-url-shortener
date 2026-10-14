<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShortUrlPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_MEMBER,
        ], true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return true;
        }

        if ($user->role === User::ROLE_ADMIN) {
            return $user->company_id === $shortUrl->company_id;
        }

        if ($user->role === User::ROLE_MEMBER) {
            return $user->id === $shortUrl->user_id
                && $user->company_id === $shortUrl->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_MEMBER,
        ], true)
            && $user->company_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ShortUrl $shortUrl): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return true;
        }

        if ($user->role === User::ROLE_ADMIN) {
            return $user->company_id === $shortUrl->company_id;
        }

        if ($user->role === User::ROLE_MEMBER) {
            return $user->id === $shortUrl->user_id
                && $user->company_id === $shortUrl->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ShortUrl $shortUrl): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ShortUrl $shortUrl): bool
    {
        return false;
    }
}
