<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * The role a newly self-registering user should get. Solves the
     * bootstrap problem (an empty prod DB has no admin to promote anyone
     * else) without needing manual DB access: the very first account ever
     * created becomes admin, everyone after that is a normal member.
     */
    public static function nextRegistrationRole(): string
    {
        return static::query()->doesntExist() ? 'admin' : 'member';
    }

    /**
     * Relationships in which this user is the mentor.
     *
     * @return HasMany<DiscipleshipRelationship, $this>
     */
    public function mentoredRelationships(): HasMany
    {
        return $this->hasMany(DiscipleshipRelationship::class, 'mentor_id');
    }

    /**
     * Relationships in which this user is the disciple.
     *
     * @return HasMany<DiscipleshipRelationship, $this>
     */
    public function discipleRelationships(): HasMany
    {
        return $this->hasMany(DiscipleshipRelationship::class, 'disciple_id');
    }

    /**
     * All discipleship relationships this user is a member of (mentor or disciple).
     *
     * @return \Illuminate\Support\Collection<int, DiscipleshipRelationship>
     */
    public function discipleshipRelationships(): \Illuminate\Support\Collection
    {
        return $this->mentoredRelationships->merge($this->discipleRelationships);
    }

    /**
     * @return HasMany<DiscipleshipSession, $this>
     */
    public function createdSessions(): HasMany
    {
        return $this->hasMany(DiscipleshipSession::class, 'created_by');
    }

    /**
     * @return HasMany<DiscipleshipComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(DiscipleshipComment::class);
    }

    /**
     * @return HasMany<DiscipleshipNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(DiscipleshipNote::class);
    }

    /**
     * @return HasMany<DiscipleshipRecord, $this>
     */
    public function records(): HasMany
    {
        return $this->hasMany(DiscipleshipRecord::class, 'created_by');
    }

    /**
     * @return HasMany<DiscipleshipGoal, $this>
     */
    public function goals(): HasMany
    {
        return $this->hasMany(DiscipleshipGoal::class, 'created_by');
    }

    /**
     * @return HasMany<Feedback, $this>
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}
