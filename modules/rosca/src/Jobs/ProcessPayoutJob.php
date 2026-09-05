<?php

namespace Modules\Rosca\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Rosca\Models\Payout;
use Modules\Rosca\Events\PayoutProcessed;
use Modules\Rosca\Contracts\GatewayInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Rosca\Models\Ledger;

class ProcessPayoutJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $payout;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout;
    }

    public function handle(GatewayInterface $gateway)
    {
        // Idempotency: if already processed, skip
        if ($this->payout->status === 'processed') {
            return;
        }

        try {
            $this->payout->status = 'processing';
            $this->payout->save();

            $result = $gateway->pay($this->payout);

            if ($result['success'] ?? false) {
                $this->payout->external_transaction_id = $result['transaction_id'] ?? null;
                $this->payout->status = 'processed';
                $this->payout->processed_at = Carbon::now();
                $this->payout->save();

                // Ledger entry: credit winner
                Ledger::create([
                    'rosca_id' => $this->payout->rosca_id,
                    'member_id' => $this->payout->winner_member_id,
                    'payout_id' => $this->payout->id,
                    'type' => 'credit',
                    'amount' => $this->payout->amount,
                    'meta' => ['external_tx' => $this->payout->external_transaction_id],
                ]);

                event(new PayoutProcessed($this->payout));
            } else {
                $this->payout->status = 'failed';
                $this->payout->save();

                Log::warning('Payout failed: ' . ($result['message'] ?? 'unknown'), ['payout_id' => $this->payout->id]);

                // Let exceptions bubble to allow job retry if needed
                throw new \RuntimeException('Payout failed: ' . ($result['message'] ?? 'unknown'));
            }
        } catch (\Throwable $e) {
            Log::error('ProcessPayoutJob error: ' . $e->getMessage(), ['payout_id' => $this->payout->id]);

            // mark failed but allow automatic retries from the queue worker
            $this->payout->status = 'failed';
            $this->payout->save();

            throw $e;
        }
    }
}
