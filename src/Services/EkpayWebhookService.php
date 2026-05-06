<?php

namespace FreelancerNishad\Ekpay\Services;

use FreelancerNishad\Ekpay\Events\EkpayPaymentEvent;
use Illuminate\Support\Facades\Log;

class EkpayWebhookService
{
    public function handleWebhook(array $data)
    {
        Log::info('Ekpay Webhook Received:', $data);

        $mer_trnx_id = $data['trnx_info']['mer_trnx_id'] ?? null;
        
        if (!$mer_trnx_id) {
            Log::error('Ekpay Webhook Error: mer_trnx_id missing');
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $paymentModel = config('ekpay.models.payment');
        $payment = $paymentModel::where('transaction_id', $mer_trnx_id)->first();

        if (!$payment) {
            Log::warning("Payment not found for transaction: {$mer_trnx_id}");
            EkpayPaymentEvent::dispatch($mer_trnx_id, $data, 'unknown');
            return response()->json(['status' => 'received_but_not_found']);
        }

        $status = 'failed';
        if ($data['msg_code'] == '1020' || $data['msg_code'] == '1000') {
            $status = 'Paid';
        }

        $payment->update([
            'status' => $status,
            'webhook_received_at' => now(),
            'webhook_status' => $data['msg_det'] ?? 'Received',
            'gateway_response' => $data,
            'payment_method' => $data['pi_det_info']['pi_name'] ?? 'Ekpay',
        ]);

        Log::info("Payment updated via Ekpay Webhook: {$mer_trnx_id} - Status: {$status}");

        EkpayPaymentEvent::dispatch($mer_trnx_id, $data, $status, $payment->user_id);

        return response()->json(['status' => 'success']);
    }
}
