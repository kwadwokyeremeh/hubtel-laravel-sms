<?php

namespace NotificationChannels\Hubtel\Commands;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use NotificationChannels\Hubtel\SMSClients\HubtelStatusChecker;

class CheckSMSStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubtel-sms:status {messageId : The message ID to check status for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of a sent SMS using Hubtel SMS service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $messageId = $this->argument('messageId');
        
        try {
            $apiKey = config('hubtel-sms.api_key');
            $apiSecret = config('hubtel-sms.api_secret');
            
            if (!$apiKey || !$apiSecret) {
                $this->error('Hubtel API credentials are not configured properly.');
                $this->line('Please ensure HUBTEL_CLIENT_ID and HUBTEL_CLIENT_SECRET are set in your .env file.');
                return 1;
            }
            
            $statusChecker = new HubtelStatusChecker($apiKey, $apiSecret, new Client());
            $response = $statusChecker->query($messageId);
            
            $this->info('SMS Status Details:');
            $this->line('Message ID: ' . ($response->getMessageId() ?? 'N/A'));
            $this->line('Status: ' . ($response->getStatus() ?? 'N/A'));
            $this->line('To: ' . ($response->getTo() ?? 'N/A'));
            $this->line('From: ' . ($response->getFrom() ?? 'N/A'));
            $this->line('Content: ' . ($response->getContent() ?? 'N/A'));
            $this->line('Delivery Time: ' . ($response->getUpdateTime() ?? 'N/A'));
            $this->line('Network ID: ' . ($response->getNetworkId() ?? 'N/A'));
            
            if ($response->isDelivered()) {
                $this->info('✅ Message was delivered successfully!');
            } else {
                $this->warn('⚠️  Message status is: ' . ($response->getStatus() ?? 'Unknown'));
            }
            
            return 0;
        } catch (Exception $e) {
            $this->error('An error occurred while checking SMS status: ' . $e->getMessage());
            return 1;
        }
    }
}