<?php

namespace Modules\Rosca\Accounting;

use Modules\Rosca\Models\Payout;
use Modules\Rosca\Models\Contribution;
use Modules\Rosca\Models\Ledger;
use Illuminate\Support\Facades\Log;

class AkauntingAdapter
{
    /**
     * Create journal entries in Akaunting if available, otherwise fallback to ledger rows.
     * $entries is array of ['account_id' => int|null, 'type' => 'debit'|'credit', 'amount' => float, 'meta' => []]
     */
    public function createJournal(array $entries, array $meta = [])
    {
        // If Akaunting models are present, try to use them
        if (class_exists('Akaunting\Account\Models\Journal')) {
            try {
                $journalClass = '\\Akaunting\\Account\\Models\\Journal';
                $journal = new $journalClass();
                $journal->date = now();
                $journal->description = $meta['description'] ?? 'Rosca transaction';
                $journal->save();

                foreach ($entries as $e) {
                    // Akaunting expects journal rows; adjust as needed
                    $rowClass = '\\Akaunting\\Account\\Models\\JournalLine';
                    $row = new $rowClass();
                    $row->journal_id = $journal->id;
                    $row->account_id = $e['account_id'];
                    $row->type = $e['type'];
                    $row->amount = $e['amount'];
                    $row->meta = $e['meta'] ?? null;
                    $row->save();
                }

                return ['success' => true, 'journal_id' => $journal->id];
            } catch (\Throwable $e) {
                Log::error('Akaunting journal creation failed: ' . $e->getMessage());
            }
        }

        // Fallback: write to module ledger
        foreach ($entries as $e) {
            Ledger::create([
                'rosca_id' => $meta['rosca_id'] ?? null,
                'member_id' => $meta['member_id'] ?? null,
                'payout_id' => $meta['payout_id'] ?? null,
                'type' => $e['type'],
                'amount' => $e['amount'],
                'meta' => $e['meta'] ?? null,
            ]);
        }

        return ['success' => true, 'fallback' => true];
    }
}
