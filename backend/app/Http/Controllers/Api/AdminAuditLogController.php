<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = AuditLog::query()->with('user')->latest();
        if ($request->filled('action')) $query->where('action', $request->string('action')->toString());
        if ($request->filled('search')) $query->where('action', 'like', '%'.$request->string('search')->toString().'%');
        return response()->json(['data' => $query->paginate(min(max((int) $request->query('per_page', 30), 1), 100))]);
    }
}
