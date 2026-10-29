<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminNavigationRequest;
use App\Models\AuditLog;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class AdminNavigationController extends Controller
{
    public function index(Request $request)
    {
        $query = NavigationItem::query()->with('children')->orderBy('location')->orderBy('sort_order');
        if ($request->filled('location')) $query->where('location', $request->string('location')->toString());
        return response()->json(['data' => $query->get()]);
    }

    public function store(AdminNavigationRequest $request)
    {
        $item = NavigationItem::create($request->validated());
        $this->audit($request, 'navigation.created', $item);
        return response()->json(['data' => $item], 201);
    }

    public function update(AdminNavigationRequest $request, int $navigation)
    {
        $item = NavigationItem::findOrFail($navigation);
        abort_if($item->id === (int) $request->input('parent_id'), 422, 'A navigation item cannot be its own parent.');
        $item->update($request->validated());
        $this->audit($request, 'navigation.updated', $item);
        return response()->json(['data' => $item->fresh()]);
    }

    public function destroy(Request $request, int $navigation)
    {
        $item = NavigationItem::findOrFail($navigation);
        abort_if($item->children()->exists(), 422, 'Move or delete child navigation items first.');
        $item->delete();
        $this->audit($request, 'navigation.deleted', $item);
        return response()->json(['data' => null, 'message' => 'Navigation item archived.']);
    }

    private function audit(Request $request, string $action, NavigationItem $item): void
    {
        AuditLog::create(['user_id' => $request->user()->id, 'auditable_type' => NavigationItem::class, 'auditable_id' => $item->id, 'action' => $action, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
