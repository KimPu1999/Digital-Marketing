<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class PublicSettingsController extends Controller
{
    public function seo()
    {
        $value = SiteSetting::query()->where('key', 'seo')->where('is_public', true)->value('value') ?? [];
        $media = !empty($value['og_image_id']) ? MediaAsset::find($value['og_image_id']) : null;

        return response()->json([
            'data' => [
                'title' => $value['title'] ?? 'Northstar Digital — Marketing that moves your business forward',
                'description' => $value['description'] ?? 'A strategy-led digital marketing partner for ambitious brands.',
                'homepage_title' => $value['homepage_title'] ?? ($value['title'] ?? 'Northstar Digital — Marketing that moves your business forward'),
                'homepage_description' => $value['homepage_description'] ?? ($value['description'] ?? 'A strategy-led digital marketing partner for ambitious brands.'),
                'canonical_url' => $value['canonical_url'] ?? null,
                'search_engine_visibility' => $value['search_engine_visibility'] ?? 'index',
                'pages' => $value['pages'] ?? [],
                'og_image_url' => $media ? Storage::disk($media->disk)->url($media->path) : null,
            ],
        ])->header('Cache-Control', 'public, max-age=60, s-maxage=300, stale-while-revalidate=600');
    }
}
