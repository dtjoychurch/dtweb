<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscipleshipRecordType extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<DiscipleshipRecord, $this>
     */
    public function records(): HasMany
    {
        return $this->hasMany(DiscipleshipRecord::class, 'type_id');
    }
}
