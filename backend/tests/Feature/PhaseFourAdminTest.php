<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhaseFourAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_dashboard_requires_an_authenticated_administrator(): void
    {
        $this->getJson('/api/admin/dashboard')->assertUnauthorized();
    }

    public function test_super_admin_can_publish_content(): void
    {
        $user = $this->userWithRole('super-admin');
        $service = Service::create(['title' => 'Search', 'slug' => 'search', 'description' => 'Description', 'status' => 'draft']);

        $this->actingAs($user, 'sanctum')->postJson("/api/admin/content/services/{$service->id}/publish")->assertOk();
        $this->assertDatabaseHas('services', ['id' => $service->id, 'status' => 'published']);
    }

    public function test_media_upload_requires_permission_and_persists_metadata(): void
    {
        Storage::fake('public');
        $editor = $this->userWithRole('content-editor');

        $response = $this->actingAs($editor, 'sanctum')->post('/api/admin/media', ['file' => UploadedFile::fake()->image('brand.png'), 'alt_text' => 'Brand mark']);

        $response->assertCreated()->assertJsonPath('data.alt_text', 'Brand mark');
        $this->assertDatabaseHas('media_assets', ['name' => 'brand.png', 'alt_text' => 'Brand mark']);
    }

    public function test_sales_manager_cannot_manage_navigation(): void
    {
        $sales = $this->userWithRole('sales-manager');
        $this->actingAs($sales, 'sanctum')->getJson('/api/admin/navigation')->assertForbidden();
    }

    public function test_settings_validation_rejects_unapproved_social_icons(): void
    {
        $admin = $this->userWithRole('super-admin');
        $this->actingAs($admin, 'sanctum')->putJson('/api/admin/settings/footer', ['value' => ['description' => 'Footer', 'contact_email' => 'hello@example.test', 'contact_phone' => '', 'social_links' => [['label' => 'Example', 'icon' => 'unknown', 'url' => 'https://example.test']]]])->assertUnprocessable();
    }

    private function userWithRole(string $slug): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('slug', $slug)->firstOrFail());
        return $user;
    }
}
