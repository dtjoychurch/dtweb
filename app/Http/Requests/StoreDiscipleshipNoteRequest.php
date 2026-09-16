<?php

namespace App\Http\Requests;

use App\Models\DiscipleshipSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDiscipleshipNoteRequest extends FormRequest
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
            'session_id' => ['nullable', 'integer', 'exists:discipleship_sessions,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('session_id')) {
                return;
            }

            $session = DiscipleshipSession::with('relationship')->find($this->input('session_id'));

            if (! $session || ! $session->relationship->isParticipant($this->user())) {
                $validator->errors()->add('session_id', '無法對這場門訓新增私人筆記。');
            }
        });
    }
}
