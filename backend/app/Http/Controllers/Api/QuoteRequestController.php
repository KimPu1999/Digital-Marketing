<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteRequest;
use App\Models\Lead;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Notifications\LeadSubmissionNotification;
use App\Services\LeadSubmissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class QuoteRequestController extends Controller
{
    public function store(StoreQuoteRequest $request, LeadSubmissionService $submissions)
    {
        $data = $request->validated();
        $key = $submissions->key([$data['email'], $data['service'], $data['budget_range'], $data['project_description']]);

        if ($submissions->recentQuote($data['email'], $key)) {
            return response()->json(['message' => 'Your quote request has already been received.'], 200);
        }

        $service = Service::where('slug', $data['service'])->firstOrFail();
        $quote = DB::transaction(function () use ($data, $key, $request, $submissions, $service): QuoteRequest {
            $attachmentPath = $request->hasFile('attachment')
                ? $request->file('attachment')->store('quote-attachments', 'local')
                : null;

            $lead = Lead::create([
                'full_name' => $data['full_name'],
                'email' => strtolower(trim($data['email'])),
                'phone' => $data['phone'],
                'company_name' => $data['company_name'],
                'subject' => 'Quote request: '.$service->title,
                'message' => $data['project_description'],
                'source' => 'quote_form',
                'status' => 'new',
                'privacy_consented_at' => now(),
                'source_ip_hash' => $submissions->sourceIpHash($request),
                'dedupe_key' => $key,
                'metadata' => ['service_slug' => $service->slug],
            ]);

            return QuoteRequest::create([
                'lead_id' => $lead->id,
                'full_name' => $data['full_name'],
                'email' => strtolower(trim($data['email'])),
                'phone' => $data['phone'],
                'company_name' => $data['company_name'],
                'service_id' => $service->id,
                'budget_range' => $data['budget_range'],
                'project_description' => $data['project_description'],
                'preferred_start_date' => $data['preferred_start_date'] ?? null,
                'attachment_path' => $attachmentPath,
                'status' => 'new',
                'privacy_consented_at' => now(),
                'source_ip_hash' => $submissions->sourceIpHash($request),
                'dedupe_key' => $key,
            ]);
        });

        $details = [
            'full_name' => $quote->full_name,
            'email' => $quote->email,
            'company_name' => $quote->company_name,
            'service' => $service->title,
            'budget_range' => $quote->budget_range,
            'project_description' => $quote->project_description,
        ];
        Notification::route('mail', config('services.agency.notification_email'))->notify(new LeadSubmissionNotification('quote', $details, true));
        Notification::route('mail', $quote->email)->notify(new LeadSubmissionNotification('quote', $details));

        return response()->json(['message' => 'Your quote request has been received.'], 201);
    }
}
