<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'title' => $this->title, 'slug' => $this->slug, 'excerpt' => $this->excerpt, 'body' => $this->body, 'reading_time' => $this->reading_time, 'status' => $this->status, 'published_at' => $this->published_at, 'category' => new BlogCategoryResource($this->whenLoaded('category')), 'author' => $this->whenLoaded('author', fn () => ['id' => $this->author?->id, 'name' => $this->author?->name]), 'created_at' => $this->created_at, 'updated_at' => $this->updated_at]; }
}
