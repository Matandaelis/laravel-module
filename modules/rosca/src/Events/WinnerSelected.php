<?php

namespace Modules\Rosca\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\Rosca\Models\Round;
use Modules\Rosca\Models\Member;

class WinnerSelected
{
    use Dispatchable, InteractsWithSockets;

    public $round;
    public $member;

    public function __construct(Round $round, Member $member)
    {
        $this->round = $round;
        $this->member = $member;
    }
}
