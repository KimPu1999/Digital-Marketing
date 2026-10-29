<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminContentRequest;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\CaseStudyResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\PortfolioProjectResource;
use App\Http\Resources\ServiceCategoryResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\TestimonialResource;
use App\Models\AuditLog;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\Package;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminContentController extends Controller
{
    private array $map = [
        'service-categories' => [ServiceCategory::class, ServiceCategoryResource::class],
        'services' => [Service::class, ServiceResource::class],
        'packages' => [Package::class, PackageResource::class],
        'portfolio-projects' => [PortfolioProject::class, PortfolioProjectResource::class],
        'case-studies' => [CaseStudy::class, CaseStudyResource::class],
        'blog-categories' => [BlogCategory::class, BlogCategoryResource::class],
        'blog-posts' => [BlogPost::class, BlogPostResource::class],
        'testimonials' => [Testimonial::class, TestimonialResource::class],
        'faqs' => [Faq::class, FaqResource::class],
    ];

    public function index(Request $request, string $resource)
    {
        [$model, $resourceClass] = $this->resolve($resource);
        $this->authorizeContentRead($request, $resource);
        $query = $model::query();
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        $searchable = $this->searchable($resource);
        if ($request->filled('search')) $query->where(function ($builder) use ($request, $searchable) { $term = $request->string('search'); foreach ($searchable as $index => $column) { $method = $index === 0 ? 'where' : 'orWhere'; $builder->{$method}($column, 'like', "%{$term}%"); } });
        $requestedSort = $request->query('sort');
        $sort = in_array($requestedSort, ['created_at', 'updated_at', 'title', 'name', 'status'], true) && ($requestedSort === 'created_at' || $requestedSort === 'updated_at' || $requestedSort === 'status' || in_array($requestedSort, $searchable, true)) ? $requestedSort : 'updated_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $items = $query->orderBy($sort, $direction)->paginate(min(max((int) $request->query('per_page', 20), 1), 100));

        return $resourceClass::collection($items);
    }

    public function show(Request $request, string $resource, int $content)
    {
        [$model, $resourceClass] = $this->resolve($resource);
        $instance = $model::query()->findOrFail($content);
        $this->authorizeContentRead($request, $resource, $instance);
        return $resourceClass::make($instance);
    }

    public function store(AdminContentRequest $request, string $resource)
    {
        [$model, $resourceClass] = $this->resolve($resource);
        $attributes = $request->validated();
        $features = $attributes['features'] ?? null;
        unset($attributes['features']);
        $attributes = $this->prepare($attributes, $resource);
        $instance = new $model();
        $this->authorizeContent($request, 'create', $instance, $resource);
        $instance->fill($attributes);
        $instance->save();
        if ($instance instanceof Package && is_array($features)) $instance->features()->createMany(array_map(fn ($feature, $index) => ['label' => $feature, 'sort_order' => $index], $features, array_keys($features)));

        return $resourceClass::make($instance->fresh())->response()->setStatusCode(201);
    }

    public function update(AdminContentRequest $request, string $resource, int $content)
    {
        [$model, $resourceClass] = $this->resolve($resource);
        $instance = $model::query()->findOrFail($content);
        $this->authorizeContent($request, 'update', $instance, $resource);
        $attributes = $request->validated();
        $features = $attributes['features'] ?? null;
        unset($attributes['features']);
        $instance->fill($this->prepare($attributes, $resource));
        $instance->save();
        if ($instance instanceof Package && is_array($features)) { $instance->features()->delete(); $instance->features()->createMany(array_map(fn ($feature, $index) => ['label' => $feature, 'sort_order' => $index], $features, array_keys($features))); }

        return $resourceClass::make($instance->fresh());
    }

    public function destroy(Request $request, string $resource, int $content)
    {
        [$model] = $this->resolve($resource);
        $instance = $model::query()->findOrFail($content);
        $this->authorizeContent($request, 'delete', $instance, $resource);
        $instance->delete();
        return response()->json(['data' => null, 'message' => 'Resource archived.']);
    }

    public function publish(Request $request, string $resource, int $content)
    {
        return $this->setPublicationState($request, $resource, $content, 'published');
    }

    public function unpublish(Request $request, string $resource, int $content)
    {
        return $this->setPublicationState($request, $resource, $content, 'draft');
    }

    private function setPublicationState(Request $request, string $resource, int $content, string $status)
    {
        [$model, $resourceClass] = $this->resolve($resource);
        $instance = $model::query()->findOrFail($content);
        $this->authorizeContent($request, 'update', $instance, $resource);
        $attributes = ['status' => $status];
        if (in_array($resource, ['services', 'packages', 'portfolio-projects', 'case-studies', 'blog-posts'], true)) $attributes['published_at'] = $status === 'published' ? now() : null;
        $instance->forceFill($attributes)->save();
        AuditLog::create(['user_id' => $request->user()->id, 'auditable_type' => $instance::class, 'auditable_id' => $instance->getKey(), 'action' => $status === 'published' ? 'content.published' : 'content.unpublished', 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(), 'metadata' => ['resource' => $resource]]);
        return $resourceClass::make($instance->fresh());
    }

    private function resolve(string $resource): array { abort_unless(isset($this->map[$resource]), 404, 'Unknown content resource.'); return $this->map[$resource]; }
    private function searchable(string $resource): array
    {
        return match ($resource) {
            'service-categories', 'blog-categories' => ['name'],
            'services' => ['title', 'short_title', 'description'],
            'packages' => ['name', 'description'],
            'portfolio-projects' => ['title', 'client_label', 'category', 'summary'],
            'case-studies' => ['body'],
            'blog-posts' => ['title', 'excerpt', 'body'],
            'testimonials' => ['quote', 'name', 'company'],
            'faqs' => ['question', 'answer', 'category'],
            default => [],
        };
    }
    private function prepare(array $attributes, string $resource): array { if (empty($attributes['slug']) && isset($attributes['name'])) $attributes['slug'] = Str::slug($attributes['name']); if (empty($attributes['slug']) && isset($attributes['title'])) $attributes['slug'] = Str::slug($attributes['title']); return $attributes; }
    private function authorizeContentRead(Request $request, string $resource, ?object $instance = null): void
    {
        $policyResources = ['services', 'packages', 'portfolio-projects', 'blog-posts', 'faqs'];
        if (in_array($resource, $policyResources, true)) {
            abort_unless($request->user()->can($instance ? 'view' : 'viewAny', $instance ?: $this->map[$resource][0]), 403);
            return;
        }
        $permission = match (true) {
            str_starts_with($resource, 'service-') => 'services.read',
            str_starts_with($resource, 'blog-') => 'blog.read',
            $resource === 'case-studies' => 'portfolio.read',
            $resource === 'testimonials' => 'testimonials.manage',
            default => 'content.manage',
        };
        abort_unless($request->user()->hasPermission($permission), 403);
    }
    private function authorizeContent(Request $request, string $ability, object $instance, string $resource): void
    {
        $policyResources = ['services' => 'services.manage', 'packages' => 'packages.manage', 'portfolio-projects' => 'portfolio.manage', 'blog-posts' => 'blog.manage', 'faqs' => 'faqs.manage'];
        if (isset($policyResources[$resource])) { abort_unless($request->user()->can($ability, $instance), 403); return; }
        $permission = match (true) { str_starts_with($resource, 'service-') => 'services.manage', str_starts_with($resource, 'blog-') => 'blog.manage', $resource === 'case-studies' => 'portfolio.manage', $resource === 'testimonials' => 'testimonials.manage', default => 'content.manage' };
        abort_unless($request->user()->hasPermission($permission), 403);
    }
}
