<?php

namespace Liberta\Notification;

class NotificationManager
{
    /** @var array<string, Channel> */
    private array $channels = [];

    public function channel(string $name, Channel $channel): void
    {
        $this->channels[$name] = $channel;
    }

    public function send(Notifiable $notifiable, Notification $notification): void
    {
        foreach ($notification->via($notifiable) as $channelName) {
            $channel = $this->channels[$channelName] ?? null;

            if ($channel === null) {
                throw new \RuntimeException("Notification channel [{$channelName}] not registered");
            }

            $channel->send($notifiable, $notification);
        }
    }

    public function sendNow(Notifiable $notifiable, array $notifications): void
    {
        foreach ($notifications as $notification) {
            $this->send($notifiable, $notification);
        }
    }

    public function getChannel(string $name): Channel
    {
        return $this->channels[$name] ?? throw new \RuntimeException("Channel [{$name}] not found");
    }
}
