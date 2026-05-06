<?php

namespace FreelancerNishad\Ekpay\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EkpayService
{
    protected $apiUrl;
    protected $merRegId;
    protected $merPassKey;
    protected $ipnUrl;
    protected $whitelistIp;

    public function __construct()
    {
        $this->apiUrl = config('ekpay.api_url');
        $this->merRegId = config('ekpay.mer_reg_id');
        $this->merPassKey = config('ekpay.mer_pass_key');
        $this->ipnUrl = config('ekpay.ipn_url');
        $this->whitelistIp = config('ekpay.whitelist_ip');
    }

    public function initiatePayment(array $trns_info, array $cust_info, array $redirect_urls = [])
    {
        $req_timestamp = now()->format('Y-m-d H:i:s');
        $prefix = config('ekpay.route_prefix');

        $s_uri = $redirect_urls['success'] ?? url("/{$prefix}/success");
        $f_uri = $redirect_urls['fail'] ?? url("/{$prefix}/fail");
        $c_uri = $redirect_urls['cancel'] ?? url("/{$prefix}/cancel");

        $payload = [
            'mer_info' => [
                "mer_reg_id" => $this->merRegId,
                "mer_pas_key" => $this->merPassKey
            ],
            "req_timestamp" => "$req_timestamp GMT+6",
            "feed_uri" => [
                "c_uri" => $c_uri,
                "f_uri" => $f_uri,
                "s_uri" => $s_uri
            ],
            "cust_info" => $cust_info,
            "trns_info" => $trns_info,
            "ipn_info" => [
                "ipn_channel" => "3",
                "ipn_email" => config('mail.from.address', 'admin@example.com'),
                "ipn_uri" => rtrim($this->ipnUrl, '/') . "/{$prefix}/webhook"
            ],
            "mac_addr" => $this->whitelistIp
        ];

        Log::info('Ekpay Payment Initiation Payload:', $payload);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/merchant-api', $payload);

            if ($response->failed()) {
                Log::error('Ekpay API Error:', ['status' => $response->status(), 'body' => $response->body()]);
                throw new Exception('Failed to connect to Ekpay API');
            }

            $data = $response->json();
            Log::info('Ekpay API Response:', (array)$data);

            if (!isset($data['secure_token'])) {
                Log::error('Ekpay Secure Token Missing:', (array)$data);
                throw new Exception('Ekpay secure token not received');
            }

            $sToken = $data['secure_token'];
            $trnx_id = $trns_info['trnx_id'];

            return [
                'secure_token' => $sToken,
                'payment_url' => "{$this->apiUrl}?sToken={$sToken}&trnsID={$trnx_id}",
                'trnx_id' => $trnx_id
            ];
        } catch (Exception $e) {
            Log::error('Ekpay Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    public function checkStatus(string $trnx_id, string $trans_date)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/get-status', [
                "trnx_id" => $trnx_id,
                "trans_date" => \Illuminate\Support\Carbon::parse($trans_date)->format('Y-m-d')
            ]);

            if ($response->failed()) {
                Log::error('Ekpay Status Check Error:', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Ekpay Status Check Exception: ' . $e->getMessage());
            return null;
        }
    }
}
