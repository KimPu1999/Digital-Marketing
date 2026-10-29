<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Lead;
use App\Models\PortfolioProject;
use App\Models\QuoteRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $recentLeads = Lead::query()->latest()->limit(5)->get()->map(fn (Lead $lead) => [
            'id' => $lead->id,
            'type' => 'lead',
            'name' => $lead->full_name,
            'email' => $lead->email,
            'company_name' => $lead->company_name,
            'status' => $lead->status,
            'follow_up_at' => $lead->follow_up_at,
            'created_at' => $lead->created_at,
        ]);
        $recentQuotes = QuoteRequest::query()->with('service')->latest()->limit(5)->get()->map(fn (QuoteRequest $quote) => [
            'id' => $quote->id,
            'type' => 'quote_request',
            'name' => $quote->full_name,
            'email' => $quote->email,
            'company_name' => $quote->company_name,
            'service' => $quote->service?->title,
            'status' => $quote->status,
            'follow_up_at' => $quote->follow_up_at,
            'created_at' => $quote->created_at,
        ]);

        $statusCounts = [];
        foreach ([$this->statusTotals(Lead::query()), $this->statusTotals(QuoteRequest::query())] as $totals) {
            foreach ($totals as $status => $total) $statusCounts[$status] = ($statusCounts[$status] ?? 0) + $total;
        }
        $serviceCounts = QuoteRequest::query()->with('service:id,title')->get()->groupBy(fn (QuoteRequest $quote) => $quote->service?->title ?? 'Unspecified')->map->count()->toArray();

        return response()->json([
            'data' => [
                'counts' => [
                    'total_leads' => Lead::count() + QuoteRequest::count(),
                    'new_leads' => Lead::where('status', 'new')->count() + QuoteRequest::where('status', 'new')->count(),
                    'qualified_leads' => Lead::where('status', 'qualified')->count() + QuoteRequest::where('status', 'qualified')->count(),
                    'published_services' => Service::where('status', 'published')->count(),
                    'published_blog_posts' => BlogPost::where('status', 'published')->count(),
                    'published_portfolio_projects' => PortfolioProject::where('status', 'published')->count(),
                ],
                'leads_by_status' => $statusCounts,
                'leads_by_service' => $serviceCounts,
                'follow_ups_due' => Lead::whereNotNull('follow_up_at')->where('follow_up_at', '<=', now())->count() + QuoteRequest::whereNotNull('follow_up_at')->where('follow_up_at', '<=', now())->count(),
                'recent_inquiries' => $recentLeads->concat($recentQuotes)->sortByDesc('created_at')->values()->take(8)->values(),
            ],
        ]);
    }

    private function statusTotals($query): array
    {
        return $query->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->map(fn ($total) => (int) $total)->toArray();
    }
}
