<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['full_name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:50'], 'subject' => ['required', 'string', 'max:255'], 'message' => ['required', 'string', 'min:10', 'max:10000'], 'privacy_consent' => ['accepted'], 'website' => ['nullable', 'string', 'max:100'], 'source' => ['nullable', 'string', 'max:100']]; }
    public function withValidator(Validator $validator): void { $validator->after(function (Validator $validator): void { if ($this->filled('website')) $validator->errors()->add('website', 'Spam check failed.'); }); }
}
