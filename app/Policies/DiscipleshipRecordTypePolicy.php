<?php

namespace App\Policies;

use App\Models\DiscipleshipRecordType;
use App\Models\User;

class DiscipleshipRecordTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DiscipleshipRecordType $recordType): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, DiscipleshipRecordType $recordType): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, DiscipleshipRecordType $recordType): bool
    {
        return $user->isAdmin();
    }
}
