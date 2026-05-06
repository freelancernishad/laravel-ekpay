<?php

namespace FreelancerNishad\Ekpay\Listeners;

use FreelancerNishad\Ekpay\Events\EkpayPaymentEvent;
use Illuminate\Support\Facades\Log;

class ProcessEkpayPayment
{
    public function handle(EkpayPaymentEvent $event)
    {
        Log::info("ProcessEkpayPayment Listener Triggered for Trnx: {$event->trnxId} - Status: {$event->status}");

        $paymentModel = config('ekpay.models.payment');
        $payment = $paymentModel::where('transaction_id', $event->trnxId)->first();
        
        $logModel = config('ekpay.models.log');
        $ekpayLog = $logModel::where('trnx_id', $event->trnxId)->first();

        if (!$payment) {
            Log::error("Payment record not found for Trnx: {$event->trnxId}");
            return;
        }

        if ($event->status === 'Paid') {
            $payment->update([
                'status' => 'Paid',
                'webhook_status' => 'processed',
                'webhook_received_at' => now(),
            ]);

            if (method_exists($payment, 'items')) {
                $payment->items()->update(['status' => 'Paid']);
            }

            if ($ekpayLog) {
                $ekpayLog->update(['status' => 'success']);
            }
            
            Log::info("Ekpay Payment Succeeded and records updated for Trnx: {$event->trnxId}");

        } elseif ($event->status === 'failed') {
            $payment->update(['status' => 'failed']);
            if (method_exists($payment, 'items')) {
                $payment->items()->update(['status' => 'failed']);
            }
            if ($ekpayLog) {
                $ekpayLog->update(['status' => 'failed']);
            }
            Log::warning("Ekpay Payment Failed for Trnx: {$event->trnxId}");
        }
    }
}
