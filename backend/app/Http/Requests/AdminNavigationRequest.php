<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminNavigationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'href' => ['required', 'string', 'max:500'],
            'location' => ['required', Rule::in(['header', 'footer'])],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_visible' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'parent_id' => ['nullable', 'integer', 'exists:navigation_items,id'],
        ];
    }
}
