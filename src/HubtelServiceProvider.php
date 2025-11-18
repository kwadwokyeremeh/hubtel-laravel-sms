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
                $apiKey = config('hubtel-sms.api_key');
                $apiSecret = config('hubtel-sms.api_secret');
                $usePostMethod = config('hubtel-sms.use_post_method');

                return new HubtelSMSClient(
                    $apiKey,
                    $apiSecret,
                    new Client,
                    $usePostMethod,
                );
            });
            
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\SendTestSMSCommand::class,
                Commands\CheckSMSStatusCommand::class,
            ]);
        }
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

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [HubtelChannel::class];
    }
}