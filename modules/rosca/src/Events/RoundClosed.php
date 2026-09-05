<?php

namespace Modules\Rosca\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\Rosca\Models\Round;

class RoundClosed
{
    use Dispatchable, InteractsWithSockets;

    public $round;

    public function __construct(Round $round)
    {
        $this->round = $round;
    }
}
