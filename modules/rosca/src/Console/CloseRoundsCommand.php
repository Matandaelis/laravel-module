<?php

namespace Modules\Rosca\Console;

use Illuminate\Console\Command;
use Modules\Rosca\Models\Round;
use Modules\Rosca\Services\SelectionService;
use Modules\Rosca\Models\Payout;
use Modules\Rosca\Jobs\ProcessPayoutJob;
use Modules\Rosca\Events\WinnerSelected;
use Modules\Rosca\Events\RoundClosed;
use Carbon\Carbon;

class CloseRoundsCommand extends Command
{
    protected $signature = 'rosca:close-rounds';

    protected $description = 'Close due rosca rounds, select winners and create payouts';

    public function handle(SelectionService $selection)
    {
        $now = Carbon::now();

        $rounds = Round::whereNotNull('due_date')
            ->where('due_date', '<=', $now)
            ->whereNull('winner_member_id')
            ->get();

        foreach ($rounds as $round) {
            $this->info('Processing round ' . $round->id . ' for rosca ' . $round->rosca_id);

            $winner = $selection->selectWinner($round);

            if (! $winner) {
                $this->warn('No winner found for round ' . $round->id);
                continue;
            }

            // Mark winner on round
            $round->winner_member_id = $winner->id;
            $round->save();

            // Create payout record
            $payout = Payout::create([
                'rosca_id' => $round->rosca_id,
                'round_id' => $round->id,
                'winner_member_id' => $winner->id,
                'amount' => $round->collected_amount ?? 0,
                'status' => 'pending',
            ]);

            // Dispatch job to process payout
            ProcessPayoutJob::dispatch($payout);

            // Fire events
            event(new WinnerSelected($round, $winner));
            event(new RoundClosed($round));

            $this->info('Winner selected: member_id=' . $winner->id . ' payout_id=' . $payout->id);
        }

        return 0;
    }
}
