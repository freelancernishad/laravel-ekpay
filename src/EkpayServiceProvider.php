<?php

namespace FreelancerNishad\Ekpay;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use FreelancerNishad\Ekpay\Events\EkpayPaymentEvent;
use FreelancerNishad\Ekpay\Listeners\ProcessEkpayPayment;

class EkpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ekpay.php', 'ekpay');

        $this->app->singleton(\FreelancerNishad\Ekpay\Services\EkpayService::class, function ($app) {
            return new \FreelancerNishad\Ekpay\Services\EkpayService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ekpay.php' => config_path('ekpay.php'),
            ], 'ekpay-config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_ekpay_logs_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_ekpay_logs_table.php'),
            ], 'ekpay-migrations');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/ekpay.php');

        Event::listen(
            EkpayPaymentEvent::class,
            [ProcessEkpayPayment::class, 'handle']
        );
    }
}
