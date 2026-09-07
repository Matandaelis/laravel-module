<?php

namespace Modules\Rosca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rosca extends Model
{
    protected $table = 'roscas';

    protected $fillable = [
        'name',
        'description',
        'cycle_period',
        'contribution_amount',
        'start_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }
}
