<?php

namespace App\Http\Requests;

use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDiscipleshipRecordRequest extends FormRequest
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
            'type' => ['required', 'in:'.implode(',', DiscipleshipRecord::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'visibility' => ['required', 'in:shared,private'],
            'occurred_at' => ['required', 'date'],
        ];
    }

    /**
     * Enforce that a linked session actually belongs to the relationship
     * this record is being attached to (see spec §23) — a record from one
     * relationship must never be attachable to another relationship's session.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('session_id')) {
                return;
            }

            $relationshipId = (int) $this->route('relationship')->id;
            $session = DiscipleshipSession::find($this->input('session_id'));

            if (! $session || $session->relationship_id !== $relationshipId) {
                $validator->errors()->add('session_id', '所選門訓紀錄不屬於這段門訓關係。');
            }
        });
    }
}
