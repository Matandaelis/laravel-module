<?php

namespace Modules\Rosca\Gateways;

use Modules\Rosca\Contracts\GatewayInterface;
use Modules\Rosca\Models\Payout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MPesaGateway implements GatewayInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function oauthToken()
    {
        $key = $this->config['consumer_key'] ?? null;
        $secret = $this->config['consumer_secret'] ?? null;

        if (! $key || ! $secret) {
            throw new \RuntimeException('MPesa credentials not configured');
        }

        $base = $this->config['environment'] === 'production' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

        $response = Http::withBasicAuth($key, $secret)->get($base . '/oauth/v1/generate?grant_type=client_credentials');

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to fetch MPesa oauth token');
        }

        return $response->json('access_token');
    }

    public function pay(Payout $payout): array
    {
        try {
            $token = $this->oauthToken();

            $base = $this->config['environment'] === 'production' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

            $body = [
                'InitiatorName' => $this->config['b2c_initiator_name'] ?? $this->config['shortcode'],
                'SecurityCredential' => $this->config['b2c_security_credential'] ?? '',
                'CommandID' => 'BusinessPayment',
                'Amount' => (string) max(0, $payout->amount),
                'PartyA' => $this->config['b2c_shortcode'] ?? $this->config['shortcode'],
                'PartyB' => $payout->winner->contact ?? $payout->winner->user_id ?? '',
                'Remarks' => 'Rosca payout - round ' . $payout->round_id,
                'QueueTimeOutURL' => $this->config['callback_url'] ?? '',
                'ResultURL' => $this->config['callback_url'] ?? '',
                'Occasion' => 'Rosca Payout',
            ];

            $response = Http::withToken($token)->post($base . '/mpesa/b2c/v1/paymentrequest', $body);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'queued' => false,
                    'transaction_id' => null,
                    'external_request_id' => null,
                    'message' => $response->body(),
                ];
            }

            $data = $response->json();

            // Daraja often returns ConversationID for async processing
            $conversation = $data['ConversationID'] ?? $data['ConversationID'] ?? Str::random(12);

            return [
                'success' => true,
                'queued' => true,
                'transaction_id' => null,
                'external_request_id' => $conversation,
                'message' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('MPesa pay error: ' . $e->getMessage(), ['payout_id' => $payout->id]);
            return [
                'success' => false,
                'queued' => false,
                'transaction_id' => null,
                'external_request_id' => null,
                'message' => $e->getMessage(),
            ];
        }
    }
}
