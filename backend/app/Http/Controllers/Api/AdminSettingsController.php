<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSettingsRequest;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    private const KEYS = ['general', 'appearance', 'hero', 'homepage_sections', 'footer', 'seo', 'contact'];
    private const SECTIONS = ['services', 'about', 'why_choose_us', 'portfolio', 'process', 'pricing', 'results', 'testimonials', 'blog', 'faq', 'cta'];

    public function index()
    {
        $settings = SiteSetting::query()->whereIn('key', self::KEYS)->get()->keyBy('key');
        return response()->json(['data' => collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => $settings->get($key)?->value ?? $this->defaults($key)])->all()]);
    }

    public function show(string $key)
    {
        abort_unless(in_array($key, self::KEYS, true), 404, 'Unknown settings group.');
        return response()->json(['data' => SiteSetting::firstWhere('key', $key)?->value ?? $this->defaults($key)]);
    }

    public function update(AdminSettingsRequest $request, string $key)
    {
        abort_unless(in_array($key, self::KEYS, true), 404, 'Unknown settings group.');
        $value = $this->validatedValue($key, $request->validated('value'));
        $setting = SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $key, 'is_public' => true]);
        AuditLog::create(['user_id' => $request->user()->id, 'auditable_type' => SiteSetting::class, 'auditable_id' => $setting->id, 'action' => 'settings.updated', 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(), 'metadata' => ['key' => $key]]);
        return response()->json(['data' => $setting->value, 'message' => 'Settings saved.']);
    }

    private function validatedValue(string $key, array $value): array
    {
        $rules = match ($key) {
            'appearance' => ['primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'typography' => ['required', Rule::in(['plus-jakarta-sans', 'inter', 'noto-sans-myanmar'])], 'logo_media_id' => ['nullable', 'integer', 'exists:media_assets,id'], 'favicon_media_id' => ['nullable', 'integer', 'exists:media_assets,id']],
            'hero' => ['heading' => ['required', 'string', 'max:255'], 'description' => ['required', 'string', 'max:1000'], 'cta_label' => ['required', 'string', 'max:80'], 'cta_href' => ['required', 'string', 'max:500'], 'desktop_image_id' => ['nullable', 'integer', 'exists:media_assets,id'], 'mobile_image_id' => ['nullable', 'integer', 'exists:media_assets,id'], 'overlay_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'overlay_opacity' => ['required', 'numeric', 'between:0,1'], 'is_visible' => ['required', 'boolean']],
            'footer' => ['description' => ['required', 'string', 'max:1000'], 'logo_media_id' => ['nullable', 'integer', 'exists:media_assets,id'], 'contact_email' => ['required', 'email', 'max:255'], 'contact_phone' => ['nullable', 'string', 'max:50'], 'social_links' => ['nullable', 'array', 'max:12'], 'social_links.*.label' => ['required', 'string', 'max:80'], 'social_links.*.icon' => ['required', Rule::in(['linkedin', 'instagram', 'facebook', 'x', 'youtube'])], 'social_links.*.url' => ['required', 'url', 'max:500']],
            'seo' => ['title' => ['required', 'string', 'max:60'], 'description' => ['required', 'string', 'max:160'], 'homepage_title' => ['required', 'string', 'max:60'], 'homepage_description' => ['required', 'string', 'max:160'], 'canonical_url' => ['nullable', 'regex:/^https?:\/\/[^\s]+$/i', 'max:500'], 'search_engine_visibility' => ['required', Rule::in(['index', 'noindex'])], 'og_image_id' => ['nullable', 'integer', 'exists:media_assets,id'], 'pages' => ['nullable', 'array', 'max:30'], 'pages.*.path' => ['required', 'string', 'regex:/^\/(?!admin(?:\/|$)|api(?:\/|$))[A-Za-z0-9\/_-]*$/'], 'pages.*.title' => ['nullable', 'string', 'max:60'], 'pages.*.description' => ['nullable', 'string', 'max:160'], 'pages.*.canonical_url' => ['nullable', 'regex:/^https?:\/\/[^\s]+$/i', 'max:500'], 'pages.*.search_engine_visibility' => ['nullable', Rule::in(['index', 'noindex'])]],
            'homepage_sections' => ['required', 'array'],
            default => ['*' => ['nullable']],
        };
        Validator::make($value, $rules)->validate();
        if ($key === 'homepage_sections') {
            $sections = $value['sections'] ?? $value;
            Validator::make(['sections' => $sections], ['sections' => ['required', 'array', 'min:1']])->validate();
            foreach ($sections as $section) {
                Validator::make($section, ['key' => ['required', Rule::in(self::SECTIONS)], 'enabled' => ['required', 'boolean'], 'heading' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:1000'], 'sort_order' => ['required', 'integer', 'min:0'], 'featured_ids' => ['nullable', 'array', 'max:12'], 'featured_ids.*' => ['integer']])->validate();
            }
            $keys = array_column($sections, 'key');
            abort_if(count($keys) !== count(array_unique($keys)), 422, 'Homepage section keys must be unique.');
            return ['sections' => array_values($sections)];
        }
        return $value;
    }

    private function defaults(string $key): array
    {
        return match ($key) {
            'appearance' => ['primary_color' => '#143D8D', 'secondary_color' => '#2563EB', 'typography' => 'plus-jakarta-sans', 'logo_media_id' => null, 'favicon_media_id' => null],
            'hero' => ['heading' => 'Marketing that moves your business forward.', 'description' => 'A strategy-led digital marketing partner for ambitious brands.', 'cta_label' => 'Start a conversation', 'cta_href' => '/get-a-quote', 'desktop_image_id' => null, 'mobile_image_id' => null, 'overlay_color' => '#0F172A', 'overlay_opacity' => 0.35, 'is_visible' => true],
            'homepage_sections' => ['sections' => collect(self::SECTIONS)->values()->map(fn ($key, $order) => ['key' => $key, 'enabled' => true, 'heading' => null, 'description' => null, 'sort_order' => $order, 'featured_ids' => []])->all()],
            'footer' => ['description' => 'A strategy-led digital marketing partner for ambitious brands.', 'logo_media_id' => null, 'contact_email' => 'hello@northstardigital.test', 'contact_phone' => '', 'social_links' => []],
            'seo' => ['title' => 'Northstar Digital — Marketing that moves your business forward', 'description' => 'A strategy-led digital marketing partner for ambitious brands.', 'homepage_title' => 'Northstar Digital — Marketing that moves your business forward', 'homepage_description' => 'A strategy-led digital marketing partner for ambitious brands.', 'canonical_url' => null, 'search_engine_visibility' => 'index', 'og_image_id' => null, 'pages' => []],
            'contact' => ['email' => 'hello@northstardigital.test', 'phone' => '', 'address' => 'Yangon · Remote worldwide'],
            default => ['site_name' => 'Northstar Digital'],
        };
    }
}
