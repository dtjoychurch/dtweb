<?php

namespace App\Http\Requests;

use App\Models\DiscipleshipSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'max:4096'],
        ];
    }

    /**
     * 同一段門訓關係、同一天只能有一筆門訓紀錄——避免同一次見面被重複記錄，
     * 也讓「第 N 次門訓」的編號單純用日期排序就有意義。
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('session_date')) {
                return;
            }

            // Create routes bind {relationship}; the admin edit route binds
            // only {session} (no relationship in the URL) — derive from
            // whichever is present, and exclude the session itself on update.
            $relationship = $this->route('relationship');
            $session = $this->route('session');
            $relationshipId = $relationship?->id ?? $session?->relationship_id;

            if (! $relationshipId) {
                return;
            }

            $duplicate = DiscipleshipSession::where('relationship_id', $relationshipId)
                ->whereDate('session_date', $this->input('session_date'))
                ->when($session, fn ($query) => $query->whereKeyNot($session->id))
                ->exists();

            if ($duplicate) {
                $validator->errors()->add(
                    'session_date',
                    '這段門訓關係在這一天已經有一筆紀錄了，請選別的日期，或直接編輯原本那一筆。'
                );
            }
        });
    }
}
