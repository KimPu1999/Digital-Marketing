<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'services.read' => 'Read services', 'services.manage' => 'Manage services',
            'packages.read' => 'Read packages', 'packages.manage' => 'Manage packages',
            'portfolio.read' => 'Read portfolio', 'portfolio.manage' => 'Manage portfolio',
            'blog.read' => 'Read blog', 'blog.manage' => 'Manage blog',
            'faqs.read' => 'Read FAQs', 'faqs.manage' => 'Manage FAQs',
            'testimonials.manage' => 'Manage testimonials', 'content.manage' => 'Manage content',
            'media.manage' => 'Manage media', 'navigation.manage' => 'Manage navigation',
            'leads.read' => 'Read leads', 'leads.manage' => 'Manage leads',
            'users.manage' => 'Manage users', 'settings.manage' => 'Manage site settings', 'audit.read' => 'Read audit logs',
        ];
        $permissionModels = collect($permissions)->mapWithKeys(fn ($name, $slug) => [$slug => Permission::firstOrCreate(['slug' => $slug], ['name' => $name])]);
        $roles = [
            'super-admin' => ['name' => 'Super Admin', 'permissions' => array_keys($permissions)],
            'content-editor' => ['name' => 'Content Editor', 'permissions' => ['services.read', 'services.manage', 'packages.read', 'packages.manage', 'portfolio.read', 'portfolio.manage', 'blog.read', 'blog.manage', 'faqs.read', 'faqs.manage', 'testimonials.manage', 'media.manage', 'navigation.manage']],
            'sales-manager' => ['name' => 'Sales Manager', 'permissions' => ['services.read', 'packages.read', 'portfolio.read', 'blog.read', 'faqs.read', 'leads.read', 'leads.manage']],
        ];
        foreach ($roles as $slug => $definition) {
            $role = Role::firstOrCreate(['slug' => $slug], ['name' => $definition['name']]);
            $role->permissions()->sync(array_map(fn ($permission) => $permissionModels[$permission]->id, $definition['permissions']));
        }
    }
}
