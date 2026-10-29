<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['full_name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'], 'phone' => ['required', 'string', 'max:50'], 'company_name' => ['required', 'string', 'max:255'], 'service' => ['required', 'string', 'exists:services,slug'], 'budget_range' => ['required', 'string', 'max:100'], 'project_description' => ['required', 'string', 'min:10', 'max:10000'], 'preferred_start_date' => ['nullable', 'date'], 'privacy_consent' => ['accepted'], 'website' => ['nullable', 'string', 'max:100'], 'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,png,jpg,jpeg']]; }
    public function withValidator(Validator $validator): void { $validator->after(function (Validator $validator): void { if ($this->filled('website')) $validator->errors()->add('website', 'Spam check failed.'); }); }
}
