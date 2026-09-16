<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscipleshipSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'relationship_id',
        'created_by',
        'session_date',
        'title',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
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

    /**
     * @return HasMany<DiscipleshipComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(DiscipleshipComment::class, 'session_id');
    }

    /**
     * @return HasMany<DiscipleshipNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(DiscipleshipNote::class, 'session_id');
    }

    /**
     * @return HasMany<DiscipleshipSessionPhoto, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(DiscipleshipSessionPhoto::class, 'session_id');
    }

    /**
     * The 1-based ordinal of this session within its relationship ("第 N 次門訓").
     */
    public function ordinal(): int
    {
        return $this->relationship
            ->sessions()
            ->where('session_date', '<=', $this->session_date)
            ->where(function ($query) {
                $query->where('session_date', '<', $this->session_date)
                    ->orWhere('id', '<=', $this->id);
            })
            ->count();
    }
}
