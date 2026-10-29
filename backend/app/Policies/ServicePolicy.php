<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function before(User $user): ?bool { return $user->hasRole('super-admin') ? true : null; }
    public function viewAny(User $user): bool { return $user->hasPermission('services.read'); }
    public function view(User $user, Service $service): bool { return $user->hasPermission('services.read'); }
    public function create(User $user): bool { return $user->hasPermission('services.manage'); }
    public function update(User $user, Service $service): bool { return $user->hasPermission('services.manage'); }
    public function delete(User $user, Service $service): bool { return $user->hasPermission('services.manage'); }
}
