# Laravel Ekpay Payment Gateway

A reusable Laravel package for integrating the Ekpay Payment Gateway.

## Installation

Install the package via composer:

```bash
composer require freelancernishad/laravel-ekpay
```

## Setup

Publish the config and migrations:

```bash
php artisan vendor:publish --tag=ekpay-config
php artisan vendor:publish --tag=ekpay-migrations
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

Add your Ekpay credentials to your `.env` file:

```env
AKPAY_MER_REG_ID=your_reg_id
AKPAY_MER_PASS_KEY=your_pass_key
AKPAY_API_URL=https://sandbox.ekpay.gov.bd/ekpaypg/v1
AKPAY_IPN_URL=https://your-domain.com
WHITE_LIST_IP=your_server_ip
```

## Usage

### Initiating a Payment

```php
use FreelancerNishad\Ekpay\Services\EkpayService;

$ekpay = app(EkpayService::class);

$trns_info = [
    "trnx_amt" => 100,
    "trnx_currency" => "BDT",
    "trnx_id" => "TRNX_" . time(),
    "ord_det" => "Payment for Order #123",
];

$cust_info = [
    "cust_email" => "user@example.com",
    "cust_name" => "John Doe",
    "cust_mobo_no" => "01712345678",
];

$redirect_urls = [
    'success' => url('/payment/success'),
    'fail' => url('/payment/fail'),
    'cancel' => url('/payment/cancel'),
];

$result = $ekpay->initiatePayment($trns_info, $cust_info, $redirect_urls);

return redirect()->to($result['payment_url']);
```

## License

The MIT License (MIT).
# laravel-ekpay
# laravel-ekpay
# laravel-ekpay
# laravel-ekpay
