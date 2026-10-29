<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\QuoteRequest;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Notifications\LeadSubmissionNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhaseFiveLeadWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Notification::fake();
    }

    public function test_valid_contact_submission_persists_and_queues_two_notifications(): void
    {
        $payload = ['full_name' => 'Ada Lovelace', 'email' => 'ada@example.test', 'phone' => '+959123456789', 'subject' => 'Growth planning', 'message' => 'We need help planning our next campaign.', 'privacy_consent' => '1'];

        $this->postJson('/api/leads', $payload)->assertCreated();

        $this->assertDatabaseHas('leads', ['email' => 'ada@example.test', 'subject' => 'Growth planning', 'status' => 'new']);
        Notification::assertSentOnDemand(LeadSubmissionNotification::class, 2);
    }

    public function test_contact_validation_and_honeypot_are_rejected(): void
    {
        $this->postJson('/api/leads', ['full_name' => 'A', 'email' => 'bad-email', 'subject' => '', 'message' => 'short'])->assertUnprocessable()->assertJsonValidationErrors(['email', 'subject', 'privacy_consent']);
        $this->postJson('/api/leads', ['full_name' => 'Bot', 'email' => 'bot@example.test', 'subject' => 'Spam', 'message' => 'This is a spam message.', 'privacy_consent' => '1', 'website' => 'https://spam.example'])->assertUnprocessable()->assertJsonValidationErrors(['website']);
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_duplicate_contact_submission_does_not_create_a_second_record(): void
    {
        $payload = ['full_name' => 'Grace Hopper', 'email' => 'grace@example.test', 'subject' => 'Analytics', 'message' => 'Please help us make our reporting easier to use.', 'privacy_consent' => '1'];

        $this->postJson('/api/leads', $payload)->assertCreated();
        $this->postJson('/api/leads', $payload)->assertOk()->assertJsonPath('message', 'Your inquiry has already been received.');

        $this->assertDatabaseCount('leads', 1);
    }

    public function test_quote_submission_persists_linked_lead_and_private_attachment(): void
    {
        Storage::fake('local');
        $service = Service::create(['title' => 'SEO Optimization', 'slug' => 'seo-optimization', 'description' => 'Search strategy', 'status' => 'published']);
        $payload = ['full_name' => 'Katherine Johnson', 'email' => 'katherine@example.test', 'phone' => '+959123456789', 'company_name' => 'Example Co', 'service' => $service->slug, 'budget_range' => '$3k-$6k monthly service fee', 'project_description' => 'We need a search and content plan for the next quarter.', 'privacy_consent' => '1', 'attachment' => UploadedFile::fake()->create('brief.pdf', 120, 'application/pdf')];

        $this->post('/api/quote-requests', $payload)->assertCreated();

        $quote = QuoteRequest::firstOrFail();
        $this->assertDatabaseHas('leads', ['id' => $quote->lead_id, 'source' => 'quote_form']);
        $this->assertNotNull($quote->attachment_path);
        Storage::disk('local')->assertExists($quote->attachment_path);
        Notification::assertSentOnDemand(LeadSubmissionNotification::class, 2);
    }

    public function test_quote_rejects_unsafe_attachment_and_rate_limits_repeated_requests(): void
    {
        $service = Service::create(['title' => 'SEO Optimization', 'slug' => 'seo-optimization', 'description' => 'Search strategy', 'status' => 'published']);
        $payload = ['full_name' => 'Test User', 'email' => 'test@example.test', 'phone' => '+959123456789', 'company_name' => 'Example Co', 'service' => $service->slug, 'budget_range' => 'Project scope', 'project_description' => 'This is a sufficiently long project description.', 'privacy_consent' => '1', 'attachment' => UploadedFile::fake()->create('payload.exe', 20, 'application/octet-stream')];

        $this->post('/api/quote-requests', $payload)->assertUnprocessable()->assertJsonValidationErrors(['attachment']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/leads', ['full_name' => 'Rate User', 'email' => 'rate@example.test', 'subject' => 'Rate test '.$attempt, 'message' => 'This message is long enough for validation.', 'privacy_consent' => '1']);
        }
        $this->postJson('/api/leads', ['full_name' => 'Rate User', 'email' => 'rate@example.test', 'subject' => 'Rate final', 'message' => 'This message is long enough for validation.', 'privacy_consent' => '1'])->assertStatus(429);
    }

    public function test_sales_manager_can_assign_update_and_export_leads(): void
    {
        $sales = $this->userWithRole('sales-manager');
        $assignee = User::factory()->create(['name' => 'Sales colleague']);
        $lead = Lead::create(['full_name' => 'Sales prospect', 'email' => 'prospect@example.test', 'subject' => 'New project', 'message' => 'A prospect message for the sales team.', 'status' => 'new']);

        $this->actingAs($sales, 'sanctum')->patchJson('/api/admin/leads/'.$lead->id, ['status' => 'proposal_sent', 'assigned_to' => $assignee->id, 'follow_up_at' => '2026-11-01 10:00:00'])->assertOk();
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'proposal_sent', 'assigned_to' => $assignee->id]);
        $this->actingAs($sales, 'sanctum')->get('/api/admin/leads/export')->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_lead_data_requires_authorization_and_dashboard_returns_workflow_stats(): void
    {
        $this->getJson('/api/admin/leads')->assertUnauthorized();
        $sales = $this->userWithRole('sales-manager');
        Lead::create(['full_name' => 'Dashboard prospect', 'email' => 'dashboard@example.test', 'subject' => 'Dashboard', 'message' => 'A dashboard inquiry.', 'status' => 'qualified']);

        $this->actingAs($sales, 'sanctum')->getJson('/api/admin/dashboard')->assertOk()->assertJsonStructure(['data' => ['counts', 'leads_by_status', 'leads_by_service', 'follow_ups_due', 'recent_inquiries']]);
    }

    private function userWithRole(string $slug): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('slug', $slug)->firstOrFail());

        return $user;
    }
}
