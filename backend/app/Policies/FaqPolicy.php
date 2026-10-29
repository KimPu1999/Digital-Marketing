<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function before(User $user): ?bool { return $user->hasRole('super-admin') ? true : null; }
    public function viewAny(User $user): bool { return $user->hasPermission('faqs.read'); }
    public function view(User $user, Faq $faq): bool { return $user->hasPermission('faqs.read'); }
    public function create(User $user): bool { return $user->hasPermission('faqs.manage'); }
    public function update(User $user, Faq $faq): bool { return $user->hasPermission('faqs.manage'); }
    public function delete(User $user, Faq $faq): bool { return $user->hasPermission('faqs.manage'); }
}
