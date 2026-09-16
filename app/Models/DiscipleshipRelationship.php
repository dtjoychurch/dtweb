<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscipleshipRelationship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mentor_id',
        'disciple_id',
        'started_at',
        'ended_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function disciple(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disciple_id');
    }

    /**
     * @return HasMany<DiscipleshipSession, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(DiscipleshipSession::class, 'relationship_id');
    }

    /**
     * @return HasMany<DiscipleshipRecord, $this>
     */
    public function records(): HasMany
    {
        return $this->hasMany(DiscipleshipRecord::class, 'relationship_id');
    }

    /**
     * @return HasMany<DiscipleshipGoal, $this>
     */
    public function goals(): HasMany
    {
        return $this->hasMany(DiscipleshipGoal::class, 'relationship_id');
    }

    public function isParticipant(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->id === $this->mentor_id || $user->id === $this->disciple_id;
    }

    public function counterpartFor(User $user): ?User
    {
        if ($user->id === $this->mentor_id) {
            return $this->disciple;
        }

        if ($user->id === $this->disciple_id) {
            return $this->mentor;
        }

        return null;
    }
}
