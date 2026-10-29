<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'title' => $this->title, 'slug' => $this->slug, 'short_title' => $this->short_title, 'excerpt' => $this->excerpt, 'description' => $this->description, 'benefits' => $this->benefits ?? [], 'deliverables' => $this->deliverables ?? [], 'process' => $this->process ?? [], 'related_service_slugs' => $this->related_service_slugs ?? [], 'icon' => $this->icon, 'status' => $this->status, 'published_at' => $this->published_at, 'category' => new ServiceCategoryResource($this->whenLoaded('category')), 'created_at' => $this->created_at, 'updated_at' => $this->updated_at]; }
}
