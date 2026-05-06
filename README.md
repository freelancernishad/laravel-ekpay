# Laravel Ekpay Payment Gateway

A reusable and extensible Laravel package for integrating the Ekpay Payment Gateway. Built for flexibility across any project type (E-commerce, School Management, SaaS, etc.).

## Features
- **Project Agnostic**: Link payments to any model (Order, StudentFee, Subscription) using polymorphic relations.
- **Extensible**: Override default models and add custom database fields via configuration.
- **Detailed Logging**: Every request, response, and IPN payload is logged for auditing.
- **Event-Driven**: Dispatches events upon payment success/failure for easy business logic integration.

## Installation

1. Install the package via composer:
```bash
composer require freelancernishad/laravel-ekpay
```

2. Publish the config and migrations:
```bash
php artisan vendor:publish --tag=ekpay-config
php artisan vendor:publish --tag=ekpay-migrations
```

3. Run the migrations:
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

## Basic Usage

### Controller Example
```php
use FreelancerNishad\Ekpay\Services\EkpayService;

public function checkout(EkpayService $ekpayService)
{
    $trns_info = [
        "trnx_amt" => 100,
        "trnx_currency" => "BDT",
        "trnx_id" => "ORDER_" . uniqid(),
        "ord_det" => "Payment for Order #123",
    ];

    $cust_info = [
        "cust_email" => "customer@example.com",
        "cust_name" => "John Doe",
        "cust_mobo_no" => "017XXXXXXXX",
    ];

    $result = $ekpayService->initiatePayment($trns_info, $cust_info);

    return redirect()->to($result['payment_url']);
}
```

## Advanced Usage & Customization

### Adding Extra Fields (Meta Data)
You can pass additional data using the `meta` field. This data is saved as JSON in the database.
```php
$meta = [
    'student_id' => 101,
    'month' => 'May',
    'year' => 2026
];
$ekpayService->initiatePayment($trns_info, $cust_info, [], $meta);
```

### Overriding Models
If you want to add actual database columns to the logs:
1. Create a migration to add columns to `ekpay_logs`.
2. Create a model that extends the package model:
```php
namespace App\Models;
use FreelancerNishad\Ekpay\Models\EkpayLog as BaseLog;

class CustomEkpayLog extends BaseLog {
    protected $fillable = ['user_id', 'trnx_id', 'my_custom_column', ...];
}
```
3. Update `config/ekpay.php`:
```php
'models' => [
    'log' => \App\Models\CustomEkpayLog::class,
],
```

### Listening for Success
Register a listener for `FreelancerNishad\Ekpay\Events\EkpayPaymentEvent` to handle logic after payment is confirmed.

## License
The MIT License (MIT).
