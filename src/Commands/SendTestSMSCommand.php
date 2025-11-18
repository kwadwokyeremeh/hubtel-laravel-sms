<?php

namespace NotificationChannels\Hubtel\Commands;

use Exception;
use Illuminate\Console\Command;
use NotificationChannels\Hubtel\HubtelChannel;
use NotificationChannels\Hubtel\HubtelMessage;

class SendTestSMSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubtel-sms:test 
                            {to : The recipient phone number} 
                            {--from= : The sender phone number or name} 
                            {--message= : The content of the SMS message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test SMS using Hubtel SMS service';

    /**
     * Execute the console command.
     *
     * @param HubtelChannel $channel
     * @return int
     */
    public function handle(HubtelChannel $channel)
    {
        $to = $this->argument('to');
        $from = $this->option('from') ?? config('mail.from.name', 'Laravel');
        $message = $this->option('message') ?? 'This is a test message from Hubtel SMS Laravel Notification Channel.';

        try {
            $sms = (new HubtelMessage())
                ->from($from)
                ->to($to)
                ->content($message);

            // Create a dummy notifiable object
            $notifiable = new class {
                public function routeNotificationFor($driver)
                {
                    return null;
                }
            };

            // Create a dummy notification
            $notification = new class($sms) extends \Illuminate\Notifications\Notification {
                private $sms;
                
                public function __construct($sms)
                {
                    $this->sms = $sms;
                }
                
                public function toSMS($notifiable)
                {
                    return $this->sms;
                }
            };

            $response = $channel->send($notifiable, $notification);

            $responseBody = json_decode($response->getBody(), true);
            
            if (isset($responseBody['status']) && $responseBody['status'] === 0) {
                $this->info('SMS sent successfully!');
                $this->line('Message ID: ' . ($responseBody['messageId'] ?? 'N/A'));
                $this->line('Status: ' . ($responseBody['statusDescription'] ?? 'N/A'));
            } else {
                $this->error('Failed to send SMS.');
                $this->line('Error: ' . ($responseBody['statusDescription'] ?? 'Unknown error'));
            }
            
            return 0;
        } catch (Exception $e) {
            $this->error('An error occurred while sending SMS: ' . $e->getMessage());
            return 1;
        }
    }
}