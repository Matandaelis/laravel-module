<?php

namespace Modules\Rosca\Services;

use Illuminate\Support\Facades\Http;
use Modules\Rosca\Models\Payout;
use Illuminate\Support\Facades\Log;

class RequeryService
{
    public function requery(Payout $payout): array
    {
        $config = config('rosca.mpesa', []);

        $key = $config['consumer_key'] ?? null;
        $secret = $config['consumer_secret'] ?? null;
        $env = $config['environment'] ?? 'sandbox';

        if (! $key || ! $secret) {
            return ['success' => false, 'message' => 'MPesa credentials missing'];
        }

        $base = $env === 'production' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

        try {
            // Acquire oauth token
            $tokenResp = Http::withBasicAuth($key, $secret)->get($base . '/oauth/v1/generate?grant_type=client_credentials');
            if (! $tokenResp->successful()) {
                return ['success' => false, 'message' => 'oauth_failed'];
            }
            $token = $tokenResp->json('access_token');

            $body = [
                'TransactionID' => $payout->external_transaction_id ?? $payout->external_request_id,
            ];

            $resp = Http::withToken($token)->post($base . '/mpesa/transactionstatus/v1/query', $body);

            if (! $resp->successful()) {
                return ['success' => false, 'message' => $resp->body()];
            }

            $data = $resp->json();
            Log::info('MPesa requery result', ['payout_id' => $payout->id, 'response' => $data]);

            // Interpret response
            $status = data_get($data, 'Result.ResultParameters.ResultParameter.0.Value') ?? null;

            return ['success' => true, 'data' => $data, 'status' => $status];
        } catch (\Throwable $e) {
            Log::error('MPesa requery error: ' . $e->getMessage(), ['payout_id' => $payout->id]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
