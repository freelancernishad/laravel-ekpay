<?php

namespace FreelancerNishad\Ekpay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EkpayLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trnx_id',
        'trns_date',
        'amount',
        'status',
        'secure_token',
        'pi_name',
        'pi_type',
        'request_payload',
        'response_payload',
        'ipn_payload',
        'redirect_urls',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'ipn_payload' => 'array',
        'redirect_urls' => 'array',
    ];

    public function user()
    {
        $userModel = config('auth.providers.users.model');
        return $this->belongsTo($userModel);
    }
}
