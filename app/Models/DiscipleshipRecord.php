<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscipleshipRecord extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPES = [
        'growth', 'struggle', 'reflection', 'prayer',
        'milestone', 'observation', 'decision', 'testimony',
    ];

    protected $fillable = [
        'relationship_id',
        'session_id',
        'created_by',
        'type',
        'title',
        'content',
        'visibility',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'date',
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
     * @return BelongsTo<DiscipleshipSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(DiscipleshipSession::class, 'session_id');
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
