<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table): void {
                if (! Schema::hasColumn('leads', 'subject')) $table->string('subject')->nullable();
                if (! Schema::hasColumn('leads', 'message')) $table->longText('message')->nullable();
                if (! Schema::hasColumn('leads', 'assigned_to')) $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                if (! Schema::hasColumn('leads', 'follow_up_at')) $table->timestamp('follow_up_at')->nullable()->index();
                if (! Schema::hasColumn('leads', 'privacy_consented_at')) $table->timestamp('privacy_consented_at')->nullable();
                if (! Schema::hasColumn('leads', 'source_ip_hash')) $table->string('source_ip_hash', 128)->nullable();
                if (! Schema::hasColumn('leads', 'dedupe_key')) $table->string('dedupe_key', 128)->nullable()->index();
                if (! Schema::hasColumn('leads', 'is_spam')) $table->boolean('is_spam')->default(false)->index();
                if (! Schema::hasColumn('leads', 'archived_at')) $table->timestamp('archived_at')->nullable()->index();
                if (! Schema::hasColumn('leads', 'last_contacted_at')) $table->timestamp('last_contacted_at')->nullable();
            });
        }

        if (Schema::hasTable('quote_requests')) {
            Schema::table('quote_requests', function (Blueprint $table): void {
                if (! Schema::hasColumn('quote_requests', 'lead_id')) $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
                if (! Schema::hasColumn('quote_requests', 'assigned_to')) $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                if (! Schema::hasColumn('quote_requests', 'follow_up_at')) $table->timestamp('follow_up_at')->nullable()->index();
                if (! Schema::hasColumn('quote_requests', 'privacy_consented_at')) $table->timestamp('privacy_consented_at')->nullable();
                if (! Schema::hasColumn('quote_requests', 'source_ip_hash')) $table->string('source_ip_hash', 128)->nullable();
                if (! Schema::hasColumn('quote_requests', 'dedupe_key')) $table->string('dedupe_key', 128)->nullable()->index();
                if (! Schema::hasColumn('quote_requests', 'is_spam')) $table->boolean('is_spam')->default(false)->index();
                if (! Schema::hasColumn('quote_requests', 'archived_at')) $table->timestamp('archived_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // Workflow data is retained for production safety. Remove fields only through a reviewed migration.
    }
};
