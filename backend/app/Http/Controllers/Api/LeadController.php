<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use App\Notifications\LeadSubmissionNotification;
use App\Services\LeadSubmissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request, LeadSubmissionService $submissions)
    {
        $data = $request->validated();
        $key = $submissions->key([$data['email'], $data['subject'], $data['message']]);

        if ($submissions->recentLead($data['email'], $key)) {
            return response()->json(['message' => 'Your inquiry has already been received.'], 200);
        }

        $lead = DB::transaction(function () use ($data, $key, $request, $submissions): Lead {
            return Lead::create([
                'full_name' => $data['full_name'],
                'email' => strtolower(trim($data['email'])),
                'phone' => $data['phone'] ?? null,
                'subject' => $data['subject'],
                'message' => $data['message'],
                'source' => $data['source'] ?? 'contact_form',
                'status' => 'new',
                'privacy_consented_at' => now(),
                'source_ip_hash' => $submissions->sourceIpHash($request),
                'dedupe_key' => $key,
            ]);
        });

        $details = [
            'full_name' => $lead->full_name,
            'email' => $lead->email,
            'company_name' => $lead->company_name,
            'subject' => $lead->subject,
            'message' => $lead->message,
        ];
        Notification::route('mail', config('services.agency.notification_email'))->notify(new LeadSubmissionNotification('contact', $details, true));
        Notification::route('mail', $lead->email)->notify(new LeadSubmissionNotification('contact', $details));

        return response()->json(['message' => 'Your inquiry has been received.'], 201);
    }
}
