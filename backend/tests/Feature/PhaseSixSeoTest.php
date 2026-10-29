<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseSixSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_public_seo_endpoint_exposes_only_public_seo_values(): void
    {
        SiteSetting::create(['key' => 'seo', 'group' => 'seo', 'is_public' => true, 'value' => ['title' => 'Published title', 'description' => 'Published description', 'search_engine_visibility' => 'index', 'secret' => 'not exposed']]);

        $this->getJson('/api/content/settings/seo')->assertOk()->assertJsonPath('data.title', 'Published title')->assertJsonMissingPath('data.secret');
    }

    public function test_seo_settings_reject_unsafe_canonical_urls(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('slug', 'super-admin')->firstOrFail());
        $payload = ['title' => 'Title', 'description' => 'Description', 'homepage_title' => 'Homepage', 'homepage_description' => 'Homepage description', 'canonical_url' => 'javascript:alert(1)', 'search_engine_visibility' => 'index', 'og_image_id' => null, 'pages' => []];

        $this->actingAs($admin, 'sanctum')->putJson('/api/admin/settings/seo', ['value' => $payload])->assertUnprocessable();
    }
}
