<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PhaseThreeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_administrator_can_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.test', 'password' => Hash::make('correct-password')]);
        $user->roles()->attach(Role::where('slug', 'super-admin')->first());

        $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'correct-password'])->assertOk()->assertJsonPath('data.email', $user->email);
        $this->actingAs($user, 'sanctum')->postJson('/api/auth/logout')->assertOk();
    }

    public function test_unauthorized_admin_access_is_rejected(): void
    {
        $this->getJson('/api/admin/content/services')->assertUnauthorized();
    }

    public function test_role_permissions_control_content_writes(): void
    {
        $editor = User::factory()->create();
        $editor->roles()->attach(Role::where('slug', 'content-editor')->first());
        $sales = User::factory()->create();
        $sales->roles()->attach(Role::where('slug', 'sales-manager')->first());
        $payload = ['title' => 'Search', 'slug' => 'search', 'description' => 'A service description.', 'status' => 'draft'];

        $this->actingAs($editor, 'sanctum')->postJson('/api/admin/content/services', $payload)->assertCreated();
        $this->actingAs($sales, 'sanctum')->postJson('/api/admin/content/services', $payload + ['slug' => 'search-two'])->assertForbidden();
    }

    public function test_content_validation_returns_errors(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('slug', 'super-admin')->first());
        $this->actingAs($admin, 'sanctum')->postJson('/api/admin/content/services', ['title' => 'Missing description'])->assertUnprocessable()->assertJsonValidationErrors(['description']);
    }

    public function test_public_content_hides_drafts_and_supports_search_and_pagination(): void
    {
        Service::create(['title' => 'Visible service', 'slug' => 'visible-service', 'description' => 'Published', 'status' => 'published']);
        Service::create(['title' => 'Hidden service', 'slug' => 'hidden-service', 'description' => 'Draft', 'status' => 'draft']);

        $this->getJson('/api/content/services?search=Visible&per_page=1')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'visible-service');
        $this->getJson('/api/content/services?search=Hidden')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_model_relationships_are_available(): void
    {
        $package = Package::create(['name' => 'Growth', 'slug' => 'growth', 'status' => 'published']);
        PackageFeature::create(['package_id' => $package->id, 'label' => 'Strategy session', 'sort_order' => 0]);

        $this->assertSame('Strategy session', $package->fresh()->features->first()->label);
    }
}
