<?php

namespace Modules\Rosca\Services;

use Modules\Rosca\Models\Round;
use Modules\Rosca\Models\Member;
use Modules\Rosca\Models\Contribution;
use Illuminate\Support\Arr;

class SelectionService
{
    public function selectWinner(Round $round): ?Member
    {
        $strategy = config('rosca.selection_strategy', 'round_robin');

        if ($strategy === 'weighted_random') {
            return $this->weightedRandom($round);
        }

        return $this->roundRobin($round);
    }

    protected function roundRobin(Round $round): ?Member
    {
        $rosca = $round->rosca;

        // Find last winner for this rosca
        $last = $rosca->rounds()->whereNotNull('winner_member_id')->orderByDesc('due_date')->first();

        $members = $rosca->members()->orderBy('id')->get();

        if ($members->isEmpty()) {
            return null;
        }

        if (! $last) {
            return $members->first();
        }

        $index = $members->search(function ($m) use ($last) {
            return $m->id === $last->winner_member_id;
        });

        $nextIndex = ($index === false) ? 0 : ($index + 1) % $members->count();

        return $members->get($nextIndex);
    }

    protected function weightedRandom(Round $round): ?Member
    {
        $rosca = $round->rosca;

        $contribs = Contribution::where('rosca_id', $rosca->id)
            ->selectRaw('member_id, SUM(amount) as total')
            ->groupBy('member_id')
            ->pluck('total', 'member_id')
            ->toArray();

        if (empty($contribs)) {
            // fallback to round robin
            return $this->roundRobin($round);
        }

        $members = Member::whereIn('id', array_keys($contribs))->get()->keyBy('id');

        $winnerId = $this->pickWeighted(array_map('floatval', $contribs));

        return $members->get($winnerId) ?? null;
    }

    protected function pickWeighted(array $weights)
    {
        $total = array_sum($weights);

        if ($total <= 0) {
            // pick random key
            $keys = array_keys($weights);
            return $keys[array_rand($keys)];
        }

        $r = mt_rand() / mt_getrandmax() * $total;

        foreach ($weights as $id => $w) {
            if ($r <= $w) {
                return $id;
            }

            $r -= $w;
        }

        // fallback
        $keys = array_keys($weights);
        return $keys[array_key_last($keys)];
    }
}
