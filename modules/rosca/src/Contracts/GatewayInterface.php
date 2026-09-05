<?php

namespace Modules\Rosca\Contracts;

use Modules\Rosca\Models\Payout;

interface GatewayInterface
{
    /**
     * Attempt to perform a payout. Returns an array with keys: success (bool), transaction_id (string|null), message (string|null)
     */
    public function pay(Payout $payout): array;
}
