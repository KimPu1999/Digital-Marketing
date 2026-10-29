<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'portfolio_project_id' => $this->portfolio_project_id, 'results' => $this->results ?? [], 'gallery' => $this->gallery ?? [], 'body' => $this->body, 'status' => $this->status, 'published_at' => $this->published_at, 'project' => new PortfolioProjectResource($this->whenLoaded('project'))]; }
}
