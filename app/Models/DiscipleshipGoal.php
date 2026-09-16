<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscipleshipGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'relationship_id',
        'created_by',
        'title',
        'description',
        'status',
        'started_at',
        'completed_at',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'completed_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<DiscipleshipRelationship, $this>
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(DiscipleshipRelationship::class, 'relationship_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isVisibleTo(User $user): bool
    {
        if ($this->visibility === 'shared') {
            return $this->relationship->isParticipant($user) || $user->isAdmin();
        }

        return $this->created_by === $user->id;
    }
}
