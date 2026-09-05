<?php

namespace Modules\Rosca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $table = 'rosca_members';

    protected $fillable = [
        'rosca_id',
        'user_id',
        'name',
        'contact'
    ];

    public function rosca(): BelongsTo
    {
        return $this->belongsTo(Rosca::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'member_id');
    }
}
