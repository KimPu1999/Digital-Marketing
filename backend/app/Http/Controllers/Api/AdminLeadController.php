<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLeadNoteRequest;
use App\Http\Requests\AdminLeadUpdateRequest;
use App\Models\AuditLog;
use App\Models\Lead;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminLeadController extends Controller
{
    public function leads(Request $request)
    {
        $query = Lead::query()->with(['notes.user', 'assignee', 'quoteRequests.service']);
        $this->applyFilters($query, $request, 'lead');

        return response()->json(['data' => $query->paginate($this->perPage($request))]);
    }

    public function staff()
    {
        return response()->json(['data' => User::query()->select('id', 'name', 'email')->orderBy('name')->get()]);
    }

    public function lead(int $lead)
    {
        return response()->json(['data' => Lead::with(['notes.user', 'assignee', 'quoteRequests.service'])->findOrFail($lead)]);
    }

    public function updateLead(AdminLeadUpdateRequest $request, int $lead)
    {
        $model = Lead::findOrFail($lead);
        $data = $request->validated();
        if ($data['status'] === 'archived') $data['archived_at'] = now();
        if ($data['status'] !== 'archived') $data['archived_at'] = null;
        $model->update($data);
        $this->audit($request, 'lead.updated', $model, ['changed' => array_keys($data)]);

        return response()->json(['data' => $model->fresh(['notes.user', 'assignee', 'quoteRequests.service'])]);
    }

    public function archiveLead(Request $request, int $lead)
    {
        $model = Lead::findOrFail($lead);
        $model->update(['status' => 'archived', 'archived_at' => now()]);
        $this->audit($request, 'lead.archived', $model);

        return response()->json(['data' => $model->fresh()]);
    }

    public function addLeadNote(AdminLeadNoteRequest $request, int $lead)
    {
        $model = Lead::findOrFail($lead);
        $note = $model->notes()->create(['user_id' => $request->user()->id, 'note' => $request->string('note')->toString()]);
        $this->audit($request, 'lead.note_added', $model, ['note_id' => $note->id]);

        return response()->json(['data' => $note->load('user')], 201);
    }

    public function quotes(Request $request)
    {
        $query = QuoteRequest::query()->with(['service', 'reviewer', 'assignee', 'lead']);
        $this->applyFilters($query, $request, 'quote');

        return response()->json(['data' => $query->paginate($this->perPage($request))]);
    }

    public function quote(int $quote)
    {
        return response()->json(['data' => QuoteRequest::with(['service', 'reviewer', 'assignee', 'lead'])->findOrFail($quote)]);
    }

    public function updateQuote(AdminLeadUpdateRequest $request, int $quote)
    {
        $model = QuoteRequest::findOrFail($quote);
        $data = $request->validated();
        if ($data['status'] === 'archived') $data['archived_at'] = now();
        if ($data['status'] !== 'archived') $data['archived_at'] = null;
        $data['reviewed_by'] = $request->user()->id;
        $model->update($data);
        $this->audit($request, 'quote_request.updated', $model, ['changed' => array_keys($data)]);

        return response()->json(['data' => $model->fresh(['service', 'reviewer', 'assignee', 'lead'])]);
    }

    public function archiveQuote(Request $request, int $quote)
    {
        $model = QuoteRequest::findOrFail($quote);
        $model->update(['status' => 'archived', 'archived_at' => now(), 'reviewed_by' => $request->user()->id]);
        $this->audit($request, 'quote_request.archived', $model);

        return response()->json(['data' => $model->fresh()]);
    }

    public function export(Request $request): StreamedResponse
    {
        $kind = $request->query('kind') === 'quote-requests' ? 'quote' : 'lead';
        $query = $kind === 'quote' ? QuoteRequest::query()->with(['service', 'assignee']) : Lead::query()->with(['assignee', 'quoteRequests.service']);
        $this->applyFilters($query, $request, $kind);
        $items = $query->get();

        return response()->streamDownload(function () use ($items, $kind): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['id', 'type', 'full_name', 'email', 'phone', 'company_name', 'service', 'status', 'assigned_to', 'follow_up_at', 'submitted_at']);
            foreach ($items as $item) {
                $service = $kind === 'quote' ? ($item->service?->title ?? '') : ($item->quoteRequests->first()?->service?->title ?? '');
                fputcsv($output, [$item->id, $kind === 'quote' ? 'quote_request' : 'lead', $item->full_name, $item->email, $item->phone, $item->company_name, $service, $item->status, $item->assignee?->name, $item->follow_up_at?->toIso8601String(), $item->created_at?->toIso8601String()]);
            }
            fclose($output);
        }, $kind === 'quote' ? 'quote-requests.csv' : 'leads.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function purgeArchived(Request $request)
    {
        $days = min(max((int) $request->input('retention_days', 365), 30), 3650);
        $cutoff = now()->subDays($days);
        $leadCount = Lead::withTrashed()->where('status', 'archived')->where('archived_at', '<', $cutoff)->forceDelete();
        $quoteCount = QuoteRequest::withTrashed()->where('status', 'archived')->where('archived_at', '<', $cutoff)->forceDelete();
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'lead.retention_purged', 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(), 'metadata' => ['retention_days' => $days, 'leads' => $leadCount, 'quote_requests' => $quoteCount]]);

        return response()->json(['message' => 'Archived records past the retention window were permanently removed.', 'data' => ['leads' => $leadCount, 'quote_requests' => $quoteCount]]);
    }

    private function applyFilters(Builder $query, Request $request, string $kind): void
    {
        $columns = ['full_name', 'email', 'company_name'];
        $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
        if ($request->filled('status')) $query->where('status', $request->string('status')->toString());
        if ($request->filled('assigned_to')) $query->where('assigned_to', (int) $request->input('assigned_to'));
        if ($request->boolean('follow_up_due')) $query->whereNotNull('follow_up_at')->where('follow_up_at', '<=', now());
        if ($request->filled('service')) {
            $slug = $request->string('service')->toString();
            $query->whereHas($kind === 'quote' ? 'service' : 'quoteRequests.service', fn (Builder $service) => $service->where('slug', $slug));
        }
        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(fn (Builder $builder) => collect($columns)->each(fn (string $column, int $index) => $builder->{$index === 0 ? 'where' : 'orWhere'}($column, $operator, "%{$term}%")));
        }
        $allowedSorts = ['created_at', 'updated_at', 'full_name', 'status', 'follow_up_at'];
        $sort = in_array($request->query('sort'), $allowedSorts, true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->query('per_page', 20), 1), 100);
    }

    private function audit(Request $request, string $action, object $model, array $metadata = []): void
    {
        AuditLog::create(['user_id' => $request->user()->id, 'auditable_type' => $model::class, 'auditable_id' => $model->getKey(), 'action' => $action, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(), 'metadata' => $metadata]);
    }
}
