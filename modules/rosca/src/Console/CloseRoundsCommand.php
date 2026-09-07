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
use Illuminate\Support\Facades\DB;

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

            // Use transaction and lock to prevent concurrent winners
            DB::transaction(function () use ($round, $selection) {
                $r = Round::where('id', $round->id)->lockForUpdate()->first();

                if ($r->winner_member_id) {
                    // already processed
                    return;
                }

                $winner = $selection->selectWinner($r);

                if (! $winner) {
                    $this->warn('No winner found for round ' . $r->id);
                    return;
                }

                // Mark winner on round
                $r->winner_member_id = $winner->id;
                $r->save();

                // Create payout record with idempotency key
                $payout = Payout::create([
                    'rosca_id' => $r->rosca_id,
                    'round_id' => $r->id,
                    'winner_member_id' => $winner->id,
                    'amount' => $r->collected_amount ?? 0,
                    'status' => 'pending',
                    'idempotency_key' => 'payout-' . $r->id . '-' . time(),
                ]);

                // Dispatch job to process payout
                ProcessPayoutJob::dispatch($payout);

                // Fire events
                event(new WinnerSelected($r, $winner));
                event(new RoundClosed($r));

            });

            $this->info('Queued processing for round ' . $round->id);
        }

        return 0;
    }
}
