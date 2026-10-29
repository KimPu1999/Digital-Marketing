<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminContentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $resource = (string) $this->route('resource');
        $slugTable = match ($resource) {
            'service-categories' => 'service_categories',
            'services' => 'services',
            'packages' => 'packages',
            'portfolio-projects' => 'portfolio_projects',
            'blog-categories' => 'blog_categories',
            'blog-posts' => 'blog_posts',
            default => null,
        };
        $content = $this->route('content');
        $contentId = is_object($content) ? $content->id : $content;
        $slug = $slugTable ? ['nullable', 'string', 'max:255', Rule::unique($slugTable, 'slug')->ignore($contentId)] : ['nullable', 'string', 'max:255'];

        return match ($resource) {
            'service-categories', 'blog-categories' => ['name' => ['required', 'string', 'max:255'], 'slug' => $slug, 'description' => ['nullable', 'string'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])], 'sort_order' => ['sometimes', 'integer', 'min:0']],
            'services' => ['title' => ['required', 'string', 'max:255'], 'slug' => $slug, 'service_category_id' => ['nullable', 'integer', 'exists:service_categories,id'], 'short_title' => ['nullable', 'string', 'max:255'], 'excerpt' => ['nullable', 'string'], 'description' => ['required', 'string'], 'benefits' => ['nullable', 'array'], 'deliverables' => ['nullable', 'array'], 'process' => ['nullable', 'array'], 'related_service_slugs' => ['nullable', 'array'], 'icon' => ['nullable', 'string', 'max:100'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])], 'published_at' => ['nullable', 'date']],
            'packages' => ['name' => ['required', 'string', 'max:255'], 'slug' => $slug, 'eyebrow' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'price' => ['nullable', 'numeric', 'min:0'], 'price_label' => ['nullable', 'string', 'max:255'], 'price_note' => ['nullable', 'string', 'max:255'], 'is_featured' => ['sometimes', 'boolean'], 'features' => ['sometimes', 'array'], 'features.*' => ['string', 'max:255'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])], 'published_at' => ['nullable', 'date']],
            'portfolio-projects' => ['title' => ['required', 'string', 'max:255'], 'slug' => $slug, 'client_label' => ['nullable', 'string', 'max:255'], 'category' => ['required', 'string', 'max:255'], 'year' => ['nullable', 'string', 'max:20'], 'summary' => ['nullable', 'string'], 'result_summary' => ['nullable', 'string'], 'challenge' => ['nullable', 'string'], 'strategy' => ['nullable', 'string'], 'accent' => ['nullable', 'string', 'max:30'], 'initials' => ['nullable', 'string', 'max:10'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])], 'published_at' => ['nullable', 'date']],
            'case-studies' => ['portfolio_project_id' => ['required', 'integer', 'exists:portfolio_projects,id'], 'results' => ['nullable', 'array'], 'gallery' => ['nullable', 'array'], 'body' => ['nullable', 'string'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])], 'published_at' => ['nullable', 'date']],
            'blog-posts' => ['title' => ['required', 'string', 'max:255'], 'slug' => $slug, 'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'], 'excerpt' => ['nullable', 'string'], 'body' => ['required', 'string'], 'reading_time' => ['nullable', 'integer', 'min:1', 'max:999'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])], 'published_at' => ['nullable', 'date']],
            'testimonials' => ['quote' => ['required', 'string'], 'name' => ['required', 'string', 'max:255'], 'role' => ['nullable', 'string', 'max:255'], 'company' => ['nullable', 'string', 'max:255'], 'is_approved' => ['sometimes', 'boolean'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])]],
            'faqs' => ['question' => ['required', 'string', 'max:500'], 'answer' => ['required', 'string'], 'category' => ['nullable', 'string', 'max:255'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])]],
            default => [],
        };
    }
}
