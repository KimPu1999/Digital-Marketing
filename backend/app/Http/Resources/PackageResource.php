<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'name' => $this->name, 'slug' => $this->slug, 'eyebrow' => $this->eyebrow, 'description' => $this->description, 'price' => $this->price, 'price_label' => $this->price_label, 'price_note' => $this->price_note, 'is_featured' => $this->is_featured, 'status' => $this->status, 'published_at' => $this->published_at, 'features' => $this->whenLoaded('features', fn () => $this->features->pluck('label')->values()), 'created_at' => $this->created_at, 'updated_at' => $this->updated_at]; }
}
