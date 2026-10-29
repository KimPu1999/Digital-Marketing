<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaUploadRequest;
use App\Http\Resources\MediaAssetResource;
use App\Models\AuditLog;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaAsset::query()->with('uploader');
        if ($request->filled('search')) $query->where('name', 'like', '%'.$request->string('search')->toString().'%');
        return MediaAssetResource::collection($query->latest()->paginate(min(max((int) $request->query('per_page', 24), 1), 100)));
    }

    public function store(MediaUploadRequest $request)
    {
        $file = $request->file('file');
        $disk = 'public';
        $path = $file->store('media', $disk);
        $asset = MediaAsset::create(['uploaded_by' => $request->user()->id, 'name' => $file->getClientOriginalName(), 'disk' => $disk, 'path' => $path, 'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'alt_text' => $request->input('alt_text')]);
        $this->audit($request, 'media.uploaded', $asset);
        return MediaAssetResource::make($asset->load('uploader'))->response()->setStatusCode(201);
    }

    public function update(Request $request, int $media)
    {
        $request->validate(['alt_text' => ['nullable', 'string', 'max:255'], 'name' => ['sometimes', 'string', 'max:255']]);
        $asset = MediaAsset::findOrFail($media);
        $asset->update($request->only(['alt_text', 'name']));
        $this->audit($request, 'media.updated', $asset);
        return MediaAssetResource::make($asset->load('uploader'));
    }

    public function destroy(Request $request, int $media)
    {
        $asset = MediaAsset::findOrFail($media);
        $referenced = SiteSetting::query()->get()->contains(fn (SiteSetting $setting) => str_contains(json_encode($setting->value), '"'.$asset->id.'"') || str_contains(json_encode($setting->value), (string) $asset->id));
        abort_if($referenced, 422, 'This media asset is still referenced by site settings.');
        Storage::disk($asset->disk)->delete($asset->path);
        $asset->delete();
        $this->audit($request, 'media.deleted', $asset);
        return response()->json(['data' => null, 'message' => 'Media asset deleted.']);
    }

    private function audit(Request $request, string $action, MediaAsset $asset): void
    {
        AuditLog::create(['user_id' => $request->user()->id, 'auditable_type' => MediaAsset::class, 'auditable_id' => $asset->id, 'action' => $action, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
