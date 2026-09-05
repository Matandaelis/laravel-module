<?php

namespace Modules\Rosca\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Rosca\Models\Payout;
use Modules\Rosca\Events\PayoutProcessed;
use Carbon\Carbon;

class ProcessPayoutJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $payout;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout;
    }

    public function handle()
    {
        // In a real system integrate payment gateway here.
        // For now mark payout as processed.
        $this->payout->status = 'processed';
        $this->payout->processed_at = Carbon::now();
        $this->payout->save();

        event(new PayoutProcessed($this->payout));
    }
}
