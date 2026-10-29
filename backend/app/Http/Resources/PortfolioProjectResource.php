<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioProjectResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'title' => $this->title, 'slug' => $this->slug, 'client_label' => $this->client_label, 'category' => $this->category, 'year' => $this->year, 'summary' => $this->summary, 'result' => $this->result_summary, 'challenge' => $this->challenge, 'strategy' => $this->strategy, 'accent' => $this->accent, 'initials' => $this->initials, 'status' => $this->status, 'published_at' => $this->published_at, 'case_study' => new CaseStudyResource($this->whenLoaded('caseStudy')), 'created_at' => $this->created_at, 'updated_at' => $this->updated_at]; }
}
