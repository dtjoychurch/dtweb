<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscipleshipNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'session_id',
        'user_id',
        'title',
        'content',
    ];

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
