<?php

namespace FreelancerNishad\Ekpay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EkpayPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trnxId;
    public $payload;
    public $status;
    public $userId;

    public function __construct(string $trnxId, array $payload, string $status, ?int $userId = null)
    {
        $this->trnxId = $trnxId;
        $this->payload = $payload;
        $this->status = $status;
        $this->userId = $userId;
    }
}
