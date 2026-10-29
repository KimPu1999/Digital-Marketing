<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    use SoftDeletes;
    protected $fillable = ['lead_id', 'full_name', 'email', 'phone', 'company_name', 'service_id', 'budget_range', 'project_description', 'preferred_start_date', 'attachment_path', 'status', 'reviewed_by', 'assigned_to', 'follow_up_at', 'privacy_consented_at', 'source_ip_hash', 'dedupe_key', 'is_spam', 'archived_at'];
    protected $casts = ['preferred_start_date' => 'date', 'follow_up_at' => 'datetime', 'privacy_consented_at' => 'datetime', 'archived_at' => 'datetime', 'is_spam' => 'boolean'];
    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
}
