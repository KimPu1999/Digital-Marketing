<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class LeadSubmissionService
{
    public function key(array $values): string
    {
        $normalized = array_map(
            static fn (mixed $value): string => strtolower(trim((string) $value)),
            $values,
        );

        return hash('sha256', implode('|', $normalized));
    }

    public function recentLead(string $email, string $key): ?Lead
    {
        return Lead::query()->where('email', strtolower(trim($email)))->where('dedupe_key', $key)->where('created_at', '>=', now()->subMinutes(15))->latest()->first();
    }

    public function recentQuote(string $email, string $key): ?QuoteRequest
    {
        return QuoteRequest::query()->where('email', strtolower(trim($email)))->where('dedupe_key', $key)->where('created_at', '>=', now()->subMinutes(15))->latest()->first();
    }

    public function sourceIpHash(Request $request): string
    {
        return hash_hmac('sha256', (string) $request->ip(), (string) config('app.key'));
    }
}
