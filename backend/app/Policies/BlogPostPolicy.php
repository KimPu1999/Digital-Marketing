<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

class BlogPostPolicy
{
    public function before(User $user): ?bool { return $user->hasRole('super-admin') ? true : null; }
    public function viewAny(User $user): bool { return $user->hasPermission('blog.read'); }
    public function view(User $user, BlogPost $post): bool { return $user->hasPermission('blog.read'); }
    public function create(User $user): bool { return $user->hasPermission('blog.manage'); }
    public function update(User $user, BlogPost $post): bool { return $user->hasPermission('blog.manage'); }
    public function delete(User $user, BlogPost $post): bool { return $user->hasPermission('blog.manage'); }
}
