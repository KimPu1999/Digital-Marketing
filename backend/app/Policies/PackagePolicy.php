<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    public function before(User $user): ?bool { return $user->hasRole('super-admin') ? true : null; }
    public function viewAny(User $user): bool { return $user->hasPermission('packages.read'); }
    public function view(User $user, Package $package): bool { return $user->hasPermission('packages.read'); }
    public function create(User $user): bool { return $user->hasPermission('packages.manage'); }
    public function update(User $user, Package $package): bool { return $user->hasPermission('packages.manage'); }
    public function delete(User $user, Package $package): bool { return $user->hasPermission('packages.manage'); }
}
