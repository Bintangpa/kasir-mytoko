<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __construct(protected MidtransService $midtrans)
    {
    }

    /**
     * Endpoint Webhook / Payment Notification URL dari Midtrans
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (!$this->midtrans->verifyWebhookSignature($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
            ], 403);
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $transaction = Transaction::where('invoice_code', $orderId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        $midtransStatus = (string) ($payload['transaction_status'] ?? 'pending');
        $gatewayTrxId = $payload['transaction_id'] ?? null;

        $updated = $this->midtrans->syncTransactionStatus($transaction, $midtransStatus, $gatewayTrxId);

        return response()->json([
            'success' => true,
            'invoice_code' => $updated->invoice_code,
            'payment_status' => $updated->payment_status,
        ]);
    }
}
