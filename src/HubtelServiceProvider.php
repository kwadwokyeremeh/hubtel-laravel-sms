<?php

namespace NotificationChannels\Hubtel;

use GuzzleHttp\Client;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\Hubtel\SMSClients\HubtelSMSClient;

class HubtelServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishConfig();

        $this->app->when(HubtelChannel::class)
            ->needs(HubtelSMSClient::class)
            ->give(function () {
                $config = config('hubtel-sms.account');

                return new HubtelSMSClient(
                    $config['key'],
                    $config['secret'],
                    new Client
                );
            });
    }

    public function register():void
    {
        $this->registerConfig();
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/hubtel-sms.php' => config_path('hubtel-sms.php')
        ],'hubtel-sms-config');

    }

    protected function registerConfig():void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/hubtel-sms.php',
            'hubtel-sms'
        );
    }
}
