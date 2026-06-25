<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PortfolioPolicy
{
    use HandlesAuthorization;

    /**
     * Admins can do everything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Guests can view published portfolios. Auth users can always view their own.
     */
    public function view(?User $user, Portfolio $portfolio): bool
    {
        if ($portfolio->is_published) {
            return true;
        }
        return $user && $user->id === $portfolio->user_id;
    }

    /**
     * Only the owner can update their portfolio.
     */
    public function update(User $user, Portfolio $portfolio): bool
    {
        return $user->id === $portfolio->user_id;
    }

    /**
     * Only the owner can delete their portfolio.
     */
    public function delete(User $user, Portfolio $portfolio): bool
    {
        return $user->id === $portfolio->user_id;
    }
}
