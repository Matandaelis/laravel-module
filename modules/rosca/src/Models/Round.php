<?php

namespace Modules\Rosca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Round extends Model
{
    protected $table = 'rosca_rounds';

    protected $fillable = [
        'rosca_id',
        'round_number',
        'due_date',
        'collected_amount',
        'winner_member_id'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function rosca(): BelongsTo
    {
        return $this->belongsTo(Rosca::class);
    }
}
