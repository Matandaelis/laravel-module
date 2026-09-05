<?php

namespace Modules\Rosca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    protected $table = 'rosca_contributions';

    protected $fillable = [
        'rosca_id',
        'member_id',
        'amount',
        'contributed_at'
    ];

    protected $casts = [
        'contributed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function rosca(): BelongsTo
    {
        return $this->belongsTo(Rosca::class);
    }
}
