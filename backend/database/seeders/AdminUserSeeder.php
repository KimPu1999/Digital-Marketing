<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        if (! $email || ! $password) return;
        $user = User::firstOrCreate(['email' => $email], ['name' => 'Northstar Administrator', 'password' => Hash::make($password), 'email_verified_at' => now()]);
        $user->roles()->syncWithoutDetaching([Role::where('slug', 'super-admin')->value('id')]);
    }
}
