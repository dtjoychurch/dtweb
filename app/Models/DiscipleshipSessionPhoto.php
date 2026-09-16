<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscipleshipSessionPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'uploaded_by',
        'path',
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
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
