<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\CaseStudyResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\PortfolioProjectResource;
use App\Http\Resources\ServiceCategoryResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\TestimonialResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\Package;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PublicContentController extends Controller
{
    public function services(Request $request) { return $this->collection($request, Service::query()->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())), ServiceResource::class, ['category'], ['title', 'short_title', 'description']); }
    public function service(string $slug) { return new ServiceResource(Service::query()->where('slug', $slug)->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->with('category')->firstOrFail()); }
    public function serviceCategories(Request $request) { return $this->collection($request, ServiceCategory::query()->where('status', 'published'), ServiceCategoryResource::class, ['services' => fn ($query) => $query->where('status', 'published')], ['name', 'description']); }
    public function packages(Request $request) { return $this->collection($request, Package::query()->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())), PackageResource::class, ['features'], ['name', 'description']); }
    public function portfolio(Request $request) { return $this->collection($request, PortfolioProject::query()->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())), PortfolioProjectResource::class, ['caseStudy'], ['title', 'client_label', 'category', 'summary']); }
    public function project(string $slug) { return new PortfolioProjectResource(PortfolioProject::query()->where('slug', $slug)->where('status', 'published')->with('caseStudy')->firstOrFail()); }
    public function caseStudies(Request $request) { return $this->collection($request, CaseStudy::query()->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())), CaseStudyResource::class, ['project'], ['body']); }
    public function caseStudy(string $slug) { return new CaseStudyResource(CaseStudy::query()->where('status', 'published')->whereHas('project', fn ($query) => $query->where('slug', $slug)->where('status', 'published'))->with('project')->firstOrFail()); }
    public function blogCategories(Request $request) { return $this->collection($request, BlogCategory::query()->where('status', 'published'), BlogCategoryResource::class, [], ['name']); }
    public function blog(Request $request) { return $this->collection($request, BlogPost::query()->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())), BlogPostResource::class, ['category', 'author'], ['title', 'excerpt', 'body']); }
    public function post(string $slug) { return new BlogPostResource(BlogPost::query()->where('slug', $slug)->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->with(['category', 'author'])->firstOrFail()); }
    public function faqs(Request $request) { return $this->collection($request, Faq::query()->where('status', 'published')->orderBy('sort_order'), FaqResource::class, [], ['question', 'answer', 'category']); }
    public function testimonials(Request $request) { return $this->collection($request, Testimonial::query()->where('status', 'published')->where('is_approved', true), TestimonialResource::class, [], ['quote', 'name', 'company']); }

    private function collection(Request $request, Builder $query, string $resource, array $with = [], array $searchable = [])
    {
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search, $searchable) {
                foreach ($searchable as $index => $column) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $builder->{$method}($column, 'like', "%{$search}%");
                }
            });
        }
        $sort = in_array($request->query('sort'), ['created_at', 'published_at', 'sort_order', 'title', 'name'], true) && in_array($request->query('sort'), $searchable, true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        if ($sort !== 'sort_order' || $request->query('sort')) $query->orderBy($sort, $direction);
        $items = $query->with($with)->paginate(min(max((int) $request->query('per_page', 12), 1), 100));

        return $resource::collection($items);
    }
}
