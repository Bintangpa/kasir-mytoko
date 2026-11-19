<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\DuitkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DuitkuWebhookController extends Controller
{
    public function __construct(protected DuitkuService $duitku)
    {
    }

    /**
     * Endpoint Webhook / Callback URL notifikasi dari Duitku
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (!$this->duitku->verifyWebhookSignature($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Duitku signature.',
            ], 403);
        }

        $orderId = (string) ($payload['merchantOrderId'] ?? '');
        $transaction = Transaction::where('invoice_code', $orderId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        $resultCode = (string) ($payload['resultCode'] ?? '01');
        $gatewayTrxId = $payload['reference'] ?? null;

        $updated = $this->duitku->syncTransactionStatus($transaction, $resultCode, $gatewayTrxId);

        return response()->json([
            'success' => true,
            'invoice_code' => $updated->invoice_code,
            'payment_status' => $updated->payment_status,
            'message' => 'Duitku callback successfully processed.',
        ]);
    }
}
