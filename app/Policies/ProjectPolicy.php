<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->portfolio->user_id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->portfolio->user_id;
    }
}
