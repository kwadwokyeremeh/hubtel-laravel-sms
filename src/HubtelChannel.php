<?php

namespace NotificationChannels\Hubtel;

use Illuminate\Notifications\Notification;
use NotificationChannels\Hubtel\Exceptions\CouldNotSendNotification;
use NotificationChannels\Hubtel\Exceptions\InvalidConfiguration;
use NotificationChannels\Hubtel\SMSClients\HubtelSMSClient;
use Psr\Http\Message\ResponseInterface;

class HubtelChannel
{
    /**
     * @var HubtelSMSClient
     */
    public HubtelSMSClient $client;

    /**
     * @param HubtelSMSClient $client
     */
    public function __construct(HubtelSMSClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return ResponseInterface
     * @throws CouldNotSendNotification|\GuzzleHttp\Exception\GuzzleException
     * @throws InvalidConfiguration
     */
    public function send(mixed $notifiable, Notification $notification): ResponseInterface
    {
        $message = $notification->toSMS($notifiable);

        if (is_null($message->from)) {
            throw CouldNotSendNotification::senderNotSetError();
        }

        if (is_null($message->to) && is_null($notifiable->routeNotificationFor('SMS'))) {
            throw CouldNotSendNotification::recipientNotSetError();
        }

        if (is_null($message->content)) {
            throw CouldNotSendNotification::contentNotSetError();
        }

        if (is_null($message->to) && ! is_null($notifiable->routeNotificationFor('SMS'))) {
            $message->to = $notifiable->routeNotificationFor('SMS');
        }

        return $this->client->send($message);
    }
}
