<?php

namespace App\Policies;

use App\Models\PortfolioProject;
use App\Models\User;

class PortfolioProjectPolicy
{
    public function before(User $user): ?bool { return $user->hasRole('super-admin') ? true : null; }
    public function viewAny(User $user): bool { return $user->hasPermission('portfolio.read'); }
    public function view(User $user, PortfolioProject $project): bool { return $user->hasPermission('portfolio.read'); }
    public function create(User $user): bool { return $user->hasPermission('portfolio.manage'); }
    public function update(User $user, PortfolioProject $project): bool { return $user->hasPermission('portfolio.manage'); }
    public function delete(User $user, PortfolioProject $project): bool { return $user->hasPermission('portfolio.manage'); }
}
