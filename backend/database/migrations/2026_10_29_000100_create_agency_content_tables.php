<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->primary(['permission_id', 'role_id']);
            });
        }

        if (! Schema::hasTable('service_categories')) {
            Schema::create('service_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('status')->default('published')->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('short_title')->nullable();
                $table->text('excerpt')->nullable();
                $table->longText('description');
                $table->json('benefits')->nullable();
                $table->json('deliverables')->nullable();
                $table->json('process')->nullable();
                $table->json('related_service_slugs')->nullable();
                $table->string('icon')->nullable();
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['status', 'published_at']);
            });
        }

        if (! Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('eyebrow')->nullable();
                $table->text('description')->nullable();
                $table->decimal('price', 12, 2)->nullable();
                $table->string('price_label')->nullable();
                $table->string('price_note')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('package_features')) {
            Schema::create('package_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained()->cascadeOnDelete();
                $table->string('label');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('portfolio_projects')) {
            Schema::create('portfolio_projects', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('client_label')->nullable();
                $table->string('category')->index();
                $table->string('year')->nullable();
                $table->text('summary')->nullable();
                $table->text('result_summary')->nullable();
                $table->longText('challenge')->nullable();
                $table->longText('strategy')->nullable();
                $table->string('accent')->nullable();
                $table->string('initials', 10)->nullable();
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('case_studies')) {
            Schema::create('case_studies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('portfolio_project_id')->unique()->constrained('portfolio_projects')->cascadeOnDelete();
                $table->json('results')->nullable();
                $table->json('gallery')->nullable();
                $table->longText('body')->nullable();
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('status')->default('published')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('blog_category_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->longText('body');
                $table->unsignedSmallInteger('reading_time')->nullable();
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['status', 'published_at']);
            });
        }

        if (! Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->text('quote');
                $table->string('name');
                $table->string('role')->nullable();
                $table->string('company')->nullable();
                $table->boolean('is_approved')->default(false)->index();
                $table->string('status')->default('draft')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->longText('answer');
                $table->string('category')->nullable()->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('status')->default('draft')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('email')->index();
                $table->string('phone')->nullable();
                $table->string('company_name')->nullable();
                $table->string('source')->nullable()->index();
                $table->string('status')->default('new')->index();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('lead_notes')) {
            Schema::create('lead_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->longText('note');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('quote_requests')) {
            Schema::create('quote_requests', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('email')->index();
                $table->string('phone')->nullable();
                $table->string('company_name')->nullable();
                $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
                $table->string('budget_range')->nullable();
                $table->longText('project_description');
                $table->date('preferred_start_date')->nullable();
                $table->string('attachment_path')->nullable();
                $table->string('status')->default('new')->index();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('media_assets')) {
            Schema::create('media_assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('disk')->default('public');
                $table->string('path');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->string('alt_text')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['disk', 'path']);
            });
        }

        if (! Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->json('value')->nullable();
                $table->string('group')->default('general')->index();
                $table->boolean('is_public')->default(false)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('navigation_items')) {
            Schema::create('navigation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
                $table->string('label');
                $table->string('href');
                $table->string('location')->default('header')->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_visible')->default(true)->index();
                $table->string('status')->default('published')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('seo_metadata')) {
            Schema::create('seo_metadata', function (Blueprint $table) {
                $table->id();
                $table->morphs('seoable');
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('og_image')->nullable();
                $table->timestamps();
                $table->unique(['seoable_type', 'seoable_id']);
            });
        }

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->nullableMorphs('auditable');
                $table->string('action')->index();
                $table->ipAddress('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['created_at', 'action']);
            });
        }
    }

    public function down(): void
    {
        // Content migrations are intentionally non-destructive. Rollback is handled by an explicit,
        // reviewed migration per environment rather than dropping production content tables here.
    }
};
