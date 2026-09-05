<?php

namespace Modules\Rosca\Gateways;

use Modules\Rosca\Contracts\GatewayInterface;
use Modules\Rosca\Models\Payout;
use Illuminate\Support\Str;

class ManualGateway implements GatewayInterface
{
    public function pay(Payout $payout): array
    {
        // Manual: mark as processed and return a local transaction id
        return [
            'success' => true,
            'transaction_id' => 'manual-' . Str::random(8),
            'message' => 'Manual payout recorded',
        ];
    }
}
