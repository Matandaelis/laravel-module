<?php

namespace Modules\Rosca\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\Rosca\Models\Payout;

class PayoutProcessed
{
    use Dispatchable, InteractsWithSockets;

    public $payout;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout;
    }
}
