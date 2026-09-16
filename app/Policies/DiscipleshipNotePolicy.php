<?php

namespace App\Policies;

use App\Models\DiscipleshipNote;
use App\Models\User;

/**
 * Notes are strictly private. Unlike every other resource in this app,
 * `view` is intentionally NOT granted to admins here — a note's content
 * belongs to its author alone. Admins may only moderate (delete) a note
 * via {@see moderate()}, never read it.
 */
class DiscipleshipNotePolicy
{
    public function view(User $user, DiscipleshipNote $note): bool
    {
        return $note->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, DiscipleshipNote $note): bool
    {
        return $note->user_id === $user->id;
    }

    public function delete(User $user, DiscipleshipNote $note): bool
    {
        return $note->user_id === $user->id;
    }

    /**
     * Admin moderation: allows removing a note without ever reading its content.
     */
    public function moderate(User $user, DiscipleshipNote $note): bool
    {
        return $user->isAdmin();
    }
}
