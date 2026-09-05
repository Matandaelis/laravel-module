<?php

namespace Modules\Rosca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $table = 'rosca_payouts';

    protected $fillable = [
        'rosca_id',
        'round_id',
        'winner_member_id',
        'amount',
        'status'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function rosca(): BelongsTo
    {
        return $this->belongsTo(Rosca::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'winner_member_id');
    }
}
