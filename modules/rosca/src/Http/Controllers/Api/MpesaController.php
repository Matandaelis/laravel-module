<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Payout;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function callback(Request $request)
    {
        // Daraja B2C callback structure varies; we attempt to handle common fields.
        $data = $request->all();

        Log::info('MPesa callback received', ['payload' => $data]);

        // Try to extract ConversationID or TransactionID
        $conversation = data_get($data, 'ConversationID') ?? data_get($data, 'Body.stkCallback.CheckoutRequestID') ?? data_get($data, 'Body.stkCallback.MerchantRequestID');
        $transaction = data_get($data, 'Result.Desc') ?? data_get($data, 'TransactionID') ?? data_get($data, 'Body.stkCallback.CallbackMetadata.Item') ;

        // Handle B2C result structure
        if ($conversation) {
            $payout = Payout::where('external_request_id', $conversation)->first();
        } else {
            // attempt to match by external_transaction_id
            $txn = data_get($data, 'TransactionID') ?? null;
            $payout = $txn ? Payout::where('external_transaction_id', $txn)->first() : null;
        }

        if (! $payout) {
            Log::warning('MPesa callback could not find payout', ['conversation' => $conversation, 'payload' => $data]);
            return response()->json(['status' => 'not_found'], 404);
        }

        // Determine success/failure
        // Daraja B2C includes ResultCode and ResultDesc in Body
        $resultCode = data_get($data, 'Result.ResultParameters.ResultParameter[0].ResultCode') ?? data_get($data, 'Body.ResultCode') ?? data_get($data, 'Result.ResultParameters.ResultParameter.0.Value');
        $resultDesc = data_get($data, 'Result.ResultParameters.ResultParameter.0.ResultDesc') ?? data_get($data, 'Body.ResultDesc') ?? data_get($data, 'Result.ResultParameters.ResultParameter.0.Value');

        // Fallback: inspect known keys
        if (is_null($resultCode)) {
            // look for a status key
            $resultCode = data_get($data, 'status') ?? data_get($data, 'ResultCode') ?? data_get($data, 'Body.stkCallback.ResultCode');
        }

        // If transaction id exists in payload set external_transaction_id
        $tx = data_get($data, 'TransactionID') ?? data_get($data, 'Body.stkCallback.CallbackMetadata.Item.1.Value');
        if ($tx) {
            $payout->external_transaction_id = $tx;
        }

        if ($resultCode == 0 || $resultCode === '0' || stripos((string)$resultCode, 'success') !== false) {
            $payout->status = 'processed';
            $payout->processed_at = now();
            $payout->save();

            // create ledger/journal via accounting adapter event handled elsewhere

            return response()->json(['status' => 'ok']);
        }

        $payout->status = 'failed';
        $payout->save();

        return response()->json(['status' => 'failed']);
    }
}
