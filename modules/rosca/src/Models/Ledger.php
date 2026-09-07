<?php

namespace Modules\Rosca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    protected $table = 'rosca_ledger';

    protected $fillable = [
        'rosca_id',
        'member_id',
        'payout_id',
        'type', // debit|credit
        'amount',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function rosca(): BelongsTo
    {
        return $this->belongsTo(Rosca::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }
}
