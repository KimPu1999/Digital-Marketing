<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use SoftDeletes;
    protected $fillable = ['full_name', 'email', 'phone', 'company_name', 'subject', 'message', 'source', 'status', 'metadata', 'assigned_to', 'follow_up_at', 'privacy_consented_at', 'source_ip_hash', 'dedupe_key', 'is_spam', 'archived_at', 'last_contacted_at'];
    protected $casts = ['metadata' => 'array', 'follow_up_at' => 'datetime', 'privacy_consented_at' => 'datetime', 'archived_at' => 'datetime', 'last_contacted_at' => 'datetime', 'is_spam' => 'boolean'];
    public function notes(): HasMany { return $this->hasMany(LeadNote::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function quoteRequests(): HasMany { return $this->hasMany(QuoteRequest::class); }
}
