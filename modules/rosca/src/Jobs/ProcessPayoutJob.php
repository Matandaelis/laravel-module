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
use Modules\Rosca\Accounting\AkauntingAdapter;

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

            if (! ($result['success'] ?? false)) {
                // mark failed and allow retry
                $this->payout->status = 'failed';
                $this->payout->save();

                Log::warning('Payout initial request failed', ['payout_id' => $this->payout->id, 'message' => $result['message'] ?? null]);

                throw new \RuntimeException('Payout initial request failed: ' . ($result['message'] ?? 'unknown'));
            }

            // If gateway indicates queued async processing, save external_request_id and wait for callback
            if (! empty($result['queued'])) {
                $this->payout->external_request_id = $result['external_request_id'] ?? null;
                $this->payout->status = 'processing';
                $this->payout->save();

                // Schedule requery job or let webhook update later. For simplicity, we leave it to requery via cron or manual job.
                return;
            }

            // Synchronous success: record transaction id and mark processed
            if (! empty($result['transaction_id'])) {
                $this->payout->external_transaction_id = $result['transaction_id'];
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

                // Try to create Akaunting journal entries
                $adapter = new AkauntingAdapter();

                $adapter->createJournal([
                    ['account_id' => null, 'type' => 'debit', 'amount' => $this->payout->amount, 'meta' => []],
                    ['account_id' => null, 'type' => 'credit', 'amount' => $this->payout->amount, 'meta' => []],
                ], ['rosca_id' => $this->payout->rosca_id, 'payout_id' => $this->payout->id, 'description' => 'Rosca payout processed']);

                event(new PayoutProcessed($this->payout));

                return;
            }

            // Otherwise, unknown — mark processing and let callback or requery handle final state
            $this->payout->status = 'processing';
            $this->payout->save();

        } catch (\Throwable $e) {
            Log::error('ProcessPayoutJob error: ' . $e->getMessage(), ['payout_id' => $this->payout->id]);

            $this->payout->status = 'failed';
            $this->payout->save();

            throw $e;
        }
    }
}
