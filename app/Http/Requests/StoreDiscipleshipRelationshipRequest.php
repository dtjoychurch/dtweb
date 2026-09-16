<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDiscipleshipRelationshipRequest extends FormRequest
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
            'mentor_id' => ['required', 'integer', 'exists:users,id', 'different:disciple_id'],
            'disciple_id' => ['required', 'integer', 'exists:users,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'status' => ['required', 'in:active,paused,completed'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('mentor_id') === $this->input('disciple_id')) {
                $validator->errors()->add('disciple_id', 'Mentor 與 Disciple 不能是同一人。');
            }
        });
    }
}
