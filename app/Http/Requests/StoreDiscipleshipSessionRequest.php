<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscipleshipSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'session_date' => ['required', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'shared' => ['sometimes', 'boolean'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'max:4096'],
        ];
    }

}
