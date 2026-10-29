<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'quote' => $this->quote, 'name' => $this->name, 'role' => $this->role, 'company' => $this->company, 'is_approved' => $this->is_approved, 'status' => $this->status, 'created_at' => $this->created_at, 'updated_at' => $this->updated_at]; }
}
