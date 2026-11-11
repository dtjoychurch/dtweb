<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelationshipInvite extends Model
{
    use HasFactory;

    private const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 不含 0/O、1/I/L，避免辨識錯誤

    private const CODE_LENGTH = 8;

    private const EXPIRES_AFTER_DAYS = 2;

    public const MAX_PENDING_PER_USER = 3;

    protected $fillable = [
        'code',
        'created_by',
        'role',
        'status',
        'expires_at',
        'redeemed_by',
        'redeemed_at',
        'relationship_id',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'redeemed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function redeemer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    /**
     * @return BelongsTo<DiscipleshipRelationship, $this>
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(DiscipleshipRelationship::class, 'relationship_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = collect(range(1, self::CODE_LENGTH))
                ->map(fn () => self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)])
                ->implode('');
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public static function defaultExpiry(): \Illuminate\Support\Carbon
    {
        return now()->addDays(self::EXPIRES_AFTER_DAYS);
    }

    /**
     * 這個人目前「正在進行中」的申請數——已經產生但還沒被兌換的邀請碼，
     * 加上已經兌換、還在等管理員審核的門訓關係。
     */
    public static function pendingSubmissionCountFor(User $user): int
    {
        $ownInvites = static::where('created_by', $user->id)->where('status', 'pending')->count();

        $pendingRelationships = DiscipleshipRelationship::where('approval_status', 'pending')
            ->where(fn ($q) => $q->where('mentor_id', $user->id)->orWhere('disciple_id', $user->id))
            ->count();

        return $ownInvites + $pendingRelationships;
    }
}
