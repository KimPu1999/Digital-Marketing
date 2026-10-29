<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Http\Resources\UserResource;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles');
        if ($request->filled('search')) { $term = $request->string('search')->toString(); $query->where(fn ($builder) => $builder->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")); }
        return UserResource::collection($query->latest()->paginate(min(max((int) $request->query('per_page', 20), 1), 100)));
    }

    public function roles()
    {
        return response()->json(['data' => Role::with('permissions')->orderBy('name')->get()]);
    }

    public function store(AdminUserRequest $request)
    {
        $data = $request->validated();
        $roleIds = $data['role_ids'];
        unset($data['role_ids']);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->roles()->sync($roleIds);
        $this->audit($request, 'user.created', $user);
        return UserResource::make($user->load('roles'))->response()->setStatusCode(201);
    }

    public function update(AdminUserRequest $request, int $user)
    {
        $model = User::findOrFail($user);
        $data = $request->validated();
        $roleIds = $data['role_ids'];
        unset($data['role_ids']);
        if (blank($data['password'] ?? null)) unset($data['password']); else $data['password'] = Hash::make($data['password']);
        $model->update($data);
        $model->roles()->sync($roleIds);
        $this->audit($request, 'user.updated', $model);
        return UserResource::make($model->load('roles'));
    }

    public function destroy(Request $request, int $user)
    {
        abort_if((int) $request->user()->id === $user, 422, 'You cannot delete your own administrator account.');
        $model = User::findOrFail($user);
        $model->delete();
        $this->audit($request, 'user.deleted', $model);
        return response()->json(['data' => null, 'message' => 'Administrator deleted.']);
    }

    private function audit(Request $request, string $action, User $user): void
    {
        AuditLog::create(['user_id' => $request->user()->id, 'auditable_type' => User::class, 'auditable_id' => $user->id, 'action' => $action, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
