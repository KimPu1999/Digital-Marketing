<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminLeadUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['status' => ['required', 'string', Rule::in(['new', 'contacted', 'qualified', 'proposal_sent', 'won', 'lost', 'spam', 'archived', 'converted', 'closed'])], 'assigned_to' => ['nullable', 'integer', 'exists:users,id'], 'follow_up_at' => ['nullable', 'date'], 'last_contacted_at' => ['nullable', 'date']];
    }
}
