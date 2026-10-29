<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLeadNoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['note' => ['required', 'string', 'max:10000']]; }
}
